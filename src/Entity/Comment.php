<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectLink", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectLink;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isNew;

    /**
     * @ORM\Column(type="integer")
     */
    private $pageNum;

    public function __toString()
    {
        return $this->text;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectLink(): ?ProjectLink
    {
        return $this->projectLink;
    }

    public function setProjectLink(?ProjectLink $projectLink): self
    {
        $this->projectLink = $projectLink;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): self
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getPageNum(): ?int
    {
        return $this->pageNum;
    }

    public function setPageNum(int $pageNum): self
    {
        $this->pageNum = $pageNum;

        return $this;
    }
}
