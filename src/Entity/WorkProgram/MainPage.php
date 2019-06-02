<?php

namespace App\Entity\WorkProgram;

use App\Entity\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("main_page_wp")
 */
class MainPage extends Page
{
    /**
     * @ORM\Column(type="array")
     */
    private $fields = [];

    public function __construct()
    {
        $this->setNumber(0);
        $this->initFields();
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->fields['headers'] ?? [];
    }

    public function setHeaders(array $headers): self
    {
        $this->fields['headers'] = $headers;

        return $this;
    }

    public function getApprover(): array
    {
        return $this->fields['approver'];
    }

    public function setApprover(array $approverData): self
    {
        $this->fields['approver'] = $approverData;

        return $this;
    }

    public function getApproverRank(): string
    {
        return $this->fields['approver']['rank'];
    }

    public function setApproverRank(string $rank): self
    {
        $this->fields['approver']['rank'] = $rank;

        return $this;
    }

    public function getApproverFio(): string
    {
        return $this->fields['approver']['fio'];
    }

    public function setApproverFio(string $fio): self
    {
        $this->fields['approver']['fio'] = $fio;

        return $this;
    }

    public function getDateBlock(): array
    {
        return $this->fields['date_block'];
    }

    public function setDateBlock(array $dateBlock): self
    {
        $this->fields['date_block'] = $dateBlock;

        return $this;
    }

    public function getDateDay(): ?int
    {
        return $this->fields['date_block']['day'];
    }

    public function setDateDay(?int $day): self
    {
        $this->fields['date_block']['day'] = $day;

        return $this;
    }

    public function getDateMonth(): ?string
    {
        return $this->fields['date_block']['month'];
    }

    public function setDateMonth(?int $month): self
    {
        $this->fields['date_block']['month'] = $month;

        return $this;
    }

    public function getDateYear(): ?int
    {
        return $this->fields['date_block']['year'];
    }

    public function setDateYear(?int $year): self
    {
        $this->fields['date_block']['year'] = $year;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->fields['title'];
    }

    public function setTitle(string $title): self
    {
        $this->fields['title'] = $title;

        return $this;
    }

    public function getDiscipline(): ?string
    {
        return $this->fields['discipline'];
    }

    public function setDiscipline(string $discipline): self
    {
        $this->fields['discipline'] = $discipline;

        return $this;
    }

    public function getSubtitles(): array
    {
        return $this->fields['subtitles'];
    }

    public function setSubtitles(array $subtitles): self
    {
        $this->fields['subtitles'] = $subtitles;

        return $this;
    }

    public function getFaculty(): ?string
    {
        return $this->fields['faculty'];
    }

    public function setFaculty(string $faculty): self
    {
        $this->fields['faculty'] = $faculty;

        return $this;
    }

    public function getcathedra(): ?string
    {
        return $this->fields['cathedra'];
    }

    public function setCathedra(string $cathedra): self
    {
        $this->fields['cathedra'] = $cathedra;

        return $this;
    }

    public function getDeveloper(): ?string
    {
        return $this->fields['developer'];
    }

    public function setDeveloper(string $developer): self
    {
        $this->fields['developer'] = $developer;

        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->fields['footer'];
    }

    public function setFooter(string $footer): self
    {
        $this->fields['footer'] = $footer;

        return $this;
    }

    public function initFields(): array
    {
        $this->fields = [
            'headers' => [],
            'approver' => [
                'rank' => '',
                'fio' => '',
            ],
            'date_block' => [
                'day' => 0,
                'month' => '',
                'year' => 0,
            ],
            'title' => '',
            'discipline' => '',
            'subtitles' => [],
            'faculty' => '',
            'cathedra' => '',
            'developer' => '',
            'footer' => '',
        ];

        return $this->fields;
    }
}
