<?php

namespace App\Twig;

use App\Entity\Project;
use App\Service\ProjectFilesHelper;
use cebe\markdown\latex\Markdown;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /** @var ProjectFilesHelper */
    private $projectFilesHelper;

    public function __construct(ProjectFilesHelper $projectFilesHelper)
    {
        $this->projectFilesHelper = $projectFilesHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('json_decode', [$this, 'jsonDecode']),
            new TwigFunction('get_project_page_image_urls', [$this, 'getProjectPageImageURLs']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('parse_to_latex', [$this, 'parseToLatex']),
        ];
    }

    public function getProjectPageImageURLs(Project $project)
    {
        $filePath = $this->projectFilesHelper->getOutputLatexFilePath($project->getIdentifier(), $project->getSelectedVersion());
        $urls = [];
        $fileSystem = new Filesystem();
        for ($i = 1; $i < 100; $i++) {
            if (!$fileSystem->exists("$filePath/image_$i.png")) {
                break;
            }
            $urls[] = "/images/project/{$project->getIdentifier()}/{$project->getSelectedVersion()}/image_{$i}.png";
        }

        return $urls;
    }

    public function parseToLatex($htmlText)
    {
        $htmlConverter = new HtmlConverter(['strip_tags' => true]);
        $md = $htmlConverter->convert($htmlText);
        return (new Markdown())->parse($md);
    }

    public function jsonDecode(?string $data)
    {
        if (empty($data)) {
            return [];
        }

        return json_decode($data);
    }

}