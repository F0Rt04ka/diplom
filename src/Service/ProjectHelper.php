<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectLink;
use App\Repository\ProjectLinkRepository;
use App\Repository\ProjectRepository;

class ProjectHelper
{
    /** @var ProjectLinkRepository */
    private $projectLinkRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    /**
     * ProjectHelper constructor.
     * @param ProjectLinkRepository $projectLinkRepository
     * @param ProjectRepository $projectRepository
     */
    public function __construct(
        ProjectLinkRepository $projectLinkRepository,
        ProjectRepository $projectRepository
    ) {
        $this->projectLinkRepository = $projectLinkRepository;
        $this->projectRepository = $projectRepository;
    }

    public function createProjectLink(
        Project $project,
        string $accessLevel = ProjectLink::ACCESS_LVL_MAIN_LINK
    ) {
        $projectLink = new ProjectLink();
        $projectLink->setIdentifier($this->projectLinkRepository->generateNewUniqueIdentifier());
        $projectLink->setAccessLevel($accessLevel);
        $project->addProjectLink($projectLink);

        return $projectLink;
    }

    public function findProjectByIdentifier(string $identifier): ?Project
    {
        $link = $this->projectLinkRepository->findByIdentifier($identifier);
        return $link ? $link->getProject() : null;
    }

    public function getProjectVersionChoices(Project $project)
    {
        $versions = $this->projectRepository->getVersionForProject($project->getId());

        return array_combine($versions, $versions);
    }

}