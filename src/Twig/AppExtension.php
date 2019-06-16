<?php

namespace App\Twig;

use App\Entity\Project;
use App\Service\ProjectHelper;
use cebe\markdown\latex\Markdown;
use League\HTMLToMarkdown\HtmlConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /** @var ProjectHelper */
    private $projectHelper;

    public function __construct(ProjectHelper $projectHelper)
    {
        $this->projectHelper = $projectHelper;
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
        return $this->projectHelper->getProjectImageUrls($project);
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