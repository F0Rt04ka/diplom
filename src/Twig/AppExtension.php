<?php

namespace App\Twig;

use cebe\markdown\latex\Markdown;
use League\HTMLToMarkdown\HtmlConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('parse_to_latex', [$this, 'parseToLatex']),
        ];
    }

    public function parseToLatex($htmlText)
    {
        $htmlConverter = new HtmlConverter(['strip_tags' => true]);
        $md = $htmlConverter->convert($htmlText);
        return (new Markdown())->parse($md);
    }

}