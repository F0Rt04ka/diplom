<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProjectLink", inversedBy="comments", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectLink;

    /**
     * @ORM\Column(type="array")
     */
    private $comments = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isNew = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectLink(): ?ProjectLink
    {
        return $this->projectLink;
    }

    public function setProjectLink(ProjectLink $projectLink): self
    {
        $this->projectLink = $projectLink;

        return $this;
    }

    public function getComments(): ?array
    {
        return $this->comments;
    }

    public function setComments(array $comments): self
    {
        $this->comments = $comments;

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
}
