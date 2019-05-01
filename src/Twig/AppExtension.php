<?php

namespace App\Twig;

use App\Entity\Project;
use cebe\markdown\latex\Markdown;
use League\HTMLToMarkdown\HtmlConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('render_page_image', [$this, 'renderPageImage']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('parse_to_latex', [$this, 'parseToLatex']),
        ];
    }

    public function renderPageImage(Project $project, int $imageIndex): string
    {
        $src = "/images/project/{$project->getIdentifier()}/{$project->getSelectedVersion()}/image_{$imageIndex}.png";
        return <<<"HTML"
<div class="page-image"><img src="{$src}" alt="page_image_{$imageIndex}"></div>
HTML;
    }

    public function parseToLatex($htmlText)
    {
        $htmlConverter = new HtmlConverter(['strip_tags' => true]);
        $md = $htmlConverter->convert($htmlText);
        return (new Markdown())->parse($md);
    }

}