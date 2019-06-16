<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectLink;
use App\Repository\ProjectLinkRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\Filesystem\Filesystem;

class ProjectHelper
{
    /** @var ProjectLinkRepository */
    private $projectLinkRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ProjectFilesHelper */
    private $projectFilesHelper;

    public function __construct(
        ProjectLinkRepository $projectLinkRepository,
        ProjectRepository $projectRepository,
        ProjectFilesHelper $projectFilesHelper
    ) {
        $this->projectLinkRepository = $projectLinkRepository;
        $this->projectRepository = $projectRepository;
        $this->projectFilesHelper = $projectFilesHelper;
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

    public function getProjectImageUrls(Project $project)
    {
        $filePath = $this->projectFilesHelper->getOutputLatexFilePath(
            $project->getIdentifier(),
            $project->getSelectedVersion()
        );
        $urls = [];
        $fileSystem = new Filesystem();
        for ($i = 1; $i < 100; $i++) {
            if (!$fileSystem->exists("$filePath/image_$i.png")) {
                break;
            }
            $urls[] = "/images/project/{$project->getIdentifier()}/{$project->getSelectedVersion()}/image_{$i}.png";
        }

        return $urls;
    }
}