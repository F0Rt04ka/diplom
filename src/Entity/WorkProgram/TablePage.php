<?php

namespace App\Entity\WorkProgram;

use App\Entity\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("table_page_wp")
 */
class TablePage extends Page
{
    const TEXT_ORIENTATION_DEFAULT = 'default';
    const TEXT_ORIENTATION_VERTICAL = 'vertical';
    const TEXT_ORIENTATIONS = [
        self::TEXT_ORIENTATION_DEFAULT,
        self::TEXT_ORIENTATION_VERTICAL,
    ];

    const TEXT_SIZE_SINGLE_LINE = 'single_line';
    const TEXT_SIZE_MULTILINE = 'multiline';
    const TEXT_SIZES = [
      self::TEXT_SIZE_SINGLE_LINE,
      self::TEXT_SIZE_MULTILINE,
    ];

    const TEXT_ALIGN_CENTER = 'center';
    const TEXT_ALIGN_LEFT = 'left';
    const TEXT_ALIGN_RIGHT = 'right';
    const TEXT_ALIGNS = [
        self::TEXT_ALIGN_CENTER,
        self::TEXT_ALIGN_LEFT,
        self::TEXT_ALIGN_RIGHT,
    ];

    /**
     * @ORM\Column(type="array")
     */
    private $fields = [];

    /**
     * @ORM\Column(type="array")
     */
    private $tableBlock = [];

    public function __construct()
    {
        $this->init();
    }

    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->fields['title'];
    }

    public function setTitle(string $title): self
    {
        $this->fields['title'] = $title;

        return $this;
    }

    public function getTextBeforeTable(): ?string
    {
        return $this->fields['text_before_table'];
    }

    public function setTextBeforeTable(string $text): self
    {
        $this->fields['text_before_table'] = $text;

        return $this;
    }

    public function getCommentToTable(): ?string
    {
        return $this->fields['comment_to_table'];
    }

    public function setCommentToTable(string $comment): self
    {
        $this->fields['comment_to_table'] = $comment;

        return $this;
    }

    public function getTableBlock(): ?array
    {
        return $this->tableBlock;
    }

    public function setTableBlock(array $tableBlock): self
    {
        $this->tableBlock = $tableBlock;

        return $this;
    }

    public function getTableBlockColumnsData(): array
    {
        if (empty($this->tableBlock['columns'])) {
            return [];
        }

        return json_decode($this->tableBlock['columns'], true);
    }

    public function getTableBlockCellsData(): array
    {
        if (empty($this->tableBlock['cells'])) {
            return [];
        }

        return json_decode($this->tableBlock['cells'], true);
    }

    public function init(): array
    {
        $this->fields = [
            'title' => '',
            'text_before_table' => '',
            'comment_to_table' => '',
        ];

        return $this->fields;
    }
}
