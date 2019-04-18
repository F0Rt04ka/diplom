<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectLinkRepository")
 */
class ProjectLink
{
    const ACCESS_LVL_MAIN_LINK = 'main';
    const ACCESS_LVL_EDIT = 'edit';
    const ACCESS_LVL_COMMENTS = 'comments';
    const ACCESS_LVL_VIEW_ONLY = 'view_only';

    const EDITORIAL_ACCESS_LEVELS = [
        self::ACCESS_LVL_VIEW_ONLY,
        self::ACCESS_LVL_COMMENTS,
        self::ACCESS_LVL_EDIT,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="projectLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accessLevel;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $projectVersion;

    public function __construct(?Project $project = null)
    {
        $this->project = $project;
    }

    public function toArray(): array
    {
        return [
            'identifier'      => $this->getIdentifier(),
            'access_level'    => $this->getAccessLevel(),
            'project_version' => $this->getProjectVersion(),
        ];
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getAccessLevel(): ?string
    {
        return $this->accessLevel;
    }

    public function setAccessLevel(string $accessLevel): self
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    public function getProjectVersion(): ?int
    {
        return $this->projectVersion;
    }

    public function setProjectVersion(?int $projectVersion): self
    {
        $this->projectVersion = $projectVersion;

        return $this;
    }
}
