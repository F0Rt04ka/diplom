<?php

namespace App\Service;

use App\Entity\EmptyPage;
use App\Entity\Project;
use App\Entity\WorkProgram\TablePage;
use App\Repository\PageRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

class LatexHelper
{
    /** @var string */
    private $latexOutputDir;

    /** @var string */
    private $latexPathToBin;

    /** @var string */
    private $latexPathToDviPngBin;

    /** @var string */
    private $latexPathToDviPdfBin;

    /** @var Environment  */
    private $twig;

    /** @var ProjectFilesHelper  */
    private $projectFilesHelper;

    /** @var PageRepository */
    private $pageRepository;

    public function __construct(
        Environment $twig,
        ParameterBagInterface $params,
        ProjectFilesHelper $projectFilesHelper,
        PageRepository $pageRepository
    ) {
        $this->twig = $twig;
        $this->projectFilesHelper = $projectFilesHelper;
        $this->pageRepository = $pageRepository;

        $latexParams = $params->get('latex');
        $this->latexOutputDir = $latexParams['output_dir'];
        $this->latexPathToBin = $latexParams['latex_bin'];
        $this->latexPathToDviPngBin = $latexParams['dvipng_bin'];
        $this->latexPathToDviPdfBin = $latexParams['dvipdf_bin'];
    }

    public function createLatexTemplate(Project $project)
    {
        $fileSystem = new Filesystem();
        $latexData = $this->renderProject($project);

        $outputLatexFilePath = $this->projectFilesHelper->getOutputLatexFilePath($project->getIdentifier(), $project->getCurrentVersion());
        $outputLatexFileName = "$outputLatexFilePath/output.tex";

        try {
            $fileSystem->remove($outputLatexFileName);
            $fileSystem->appendToFile($outputLatexFileName, $latexData);
        } catch (IOExceptionInterface $exception) {
            dump('An error occurred while creating your directory at ' . $exception->getPath());
        }

        $this->runLatexCommand($outputLatexFileName);
        $this->runDviPngCommand($outputLatexFilePath);
        $this->runDviPdfCommand($outputLatexFilePath);

        try {
            $fileSystem->remove("$outputLatexFilePath/output.log");
        } catch (IOExceptionInterface $exception) {

        }

        $this->projectFilesHelper->createSymlink($project->getIdentifier(), $project->getCurrentVersion());
    }

    public function renderProject(Project $project): string
    {
        $renderData = [
            'main_page' => $project->getMainPage(),
            'pages' => $project->getPagesByType(TablePage::class),
        ];

        if ($project->getType() === Project::TYPE_DEFAULT) {
            $renderData += ['empty_pages' => $project->getPagesByType(EmptyPage::class)];
        }

        return $this->twig->render("latex/base_{$project->getType()}.html.twig", $renderData);
    }

    private function runLatexCommand(string $latexOutputFilename): void
    {
        $latexCommand = sprintf(
            '%s --interaction=nonstopmode --output-format=dvi --width --output-directory=%s %s > /dev/null 2>&1',
            $this->latexPathToBin, dirname($latexOutputFilename), $latexOutputFilename
        );
        exec($latexCommand);
    }

    private function runDviPngCommand(string $latexOutputPath): void
    {
        $command = sprintf(
            'cd %s && %s output.dvi -o image_%%d.png',
            $latexOutputPath, $this->latexPathToDviPngBin
        );
        exec($command);
    }

    private function runDviPdfCommand(string $latexOutputPath): void
    {
        $command = sprintf(
            'cd %s && %s output.dvi -o output.pdf',
            $latexOutputPath, $this->latexPathToDviPdfBin
        );
        exec($command);
    }
}