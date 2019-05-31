<?php

namespace App\Entity\WorkProject;

use App\Entity\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkProject\MainPageRepository")
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

    public function initFields(): array
    {
        $this->fields = [
            'headers' => ['', '', '', '',],
            'approver' => [
                'rank' => 'Декан факультета...',
                'fio' => 'О.Г. Мелентьев',
            ],
            'date' => [
                'day' => 0,
                'month' => '',
                'year' => 0,
            ],
            'title' => 'Рабочая программа',
            'discipline' => 'Математические основы моделирования сетей связи',
            'subtitles' => ['', '',],
            'faculty' => 'АЭС',
            'cathedra' => 'ПДС и М',
            'developer' => 'Квиткова Ирина Геннадьевна',
            'footer' => 'Новосибирск 2017',
        ];

        return $this->fields;
    }
}
