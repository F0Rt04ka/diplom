<?php

namespace App\Service;

use App\Entity\EmptyPage;
use App\Entity\Project;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

class LatexHelper
{
    /**
     * @var string
     */
    private $latexOutputDir;

    /**
     * @var string
     */
    private $latexPathToBin;

    /**
     * @var string
     */
    private $latexPathToDvipngBin;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var ProjectFilesHelper
     */
    private $projectFilesHelper;

    public function __construct(
        Environment $twig,
        ParameterBagInterface $params,
        ProjectFilesHelper $projectFilesHelper
    ) {
        $this->twig = $twig;
        $this->projectFilesHelper = $projectFilesHelper;
        $latexParams = $params->get('latex');
        $this->latexOutputDir = $latexParams['output_dir'];
        $this->latexPathToBin = $latexParams['latex_bin'];
        $this->latexPathToDvipngBin = $latexParams['dvipng_bin'];
    }

    public function createLatexTemplate(Project $project)
    {
        $fileSystem = new Filesystem();
        $latexData =
            $this->twig->render('latex/base.html.twig', [
                'main_page' => $project->getMainPage(),
                'empty_pages' => $project->getPagesByType(EmptyPage::class),
            ]);

        $outputLatexFilePath = $this->projectFilesHelper->getOutputLatexFilePath($project->getIdentifier(), $project->getCurrentVersion());
        $outputLatexFileName = "$outputLatexFilePath/output.tex";

        try {
            $fileSystem->remove($outputLatexFileName);
            $fileSystem->appendToFile($outputLatexFileName, $latexData);
        } catch (IOExceptionInterface $exception) {
            dump('An error occurred while creating your directory at ' . $exception->getPath());
        }

        $this->runLatexCommand($outputLatexFileName);
        $this->runDvipngCommand($outputLatexFilePath);

        try {
            $fileSystem->remove("$outputLatexFilePath/output.log");
        } catch (IOExceptionInterface $exception) {

        }

        $this->projectFilesHelper->createSymlink($project->getIdentifier(), $project->getCurrentVersion());
    }

    private function runLatexCommand(string $latexOutputFilename)
    {
        $latexCommand = sprintf(
            '%s --interaction=nonstopmode --output-format=dvi --width --output-directory=%s %s > /dev/null 2>&1',
            $this->latexPathToBin, dirname($latexOutputFilename), $latexOutputFilename
        );
        exec($latexCommand);
    }

    private function runDvipngCommand(string $latexOutputPath)
    {
        $dvipngCommand = sprintf(
            'cd %s && %s output.dvi -o image_%%d.png',
            $latexOutputPath, $this->latexPathToDvipngBin
        );
        exec($dvipngCommand);
    }
}