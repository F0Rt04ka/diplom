<?php

namespace App\Service;

use App\Entity\ProjectLink;
use App\Repository\ProjectLinkRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessHelper
{
    /** @var string */
    private $accessLevel;

    /** @var string */
    private $identifier;

    /** @var bool */
    private $isViewVersionPage;

    public function __construct(RequestStack $requestStack, ProjectLinkRepository $linkRepository)
    {
        if (!$request = $requestStack->getMasterRequest()) {
            return;
        }

        $projectIdentifier = $request->get('project_identifier', $request->get('identifier'));
        if ($projectIdentifier && $link = $linkRepository->findByIdentifier($projectIdentifier)) {
            $this->accessLevel = $link->getAccessLevel();
            $this->identifier = $link->getIdentifier();
        }
        $this->isViewVersionPage = $request->get('_route') === 'project_view_version';
    }

    public function isViewVersionPage(): bool
    {
        return $this->isViewVersionPage;
    }

    public function isMainAccess(): bool
    {
        return $this->accessLevel === ProjectLink::ACCESS_LVL_MAIN_LINK;
    }

    public function canViewOnly(): bool
    {
        return $this->accessLevel === ProjectLink::ACCESS_LVL_VIEW_ONLY;
    }

    public function canEdit(): bool
    {
        return in_array($this->accessLevel, [
            ProjectLink::ACCESS_LVL_MAIN_LINK,
            ProjectLink::ACCESS_LVL_EDIT,
        ], true);
    }

    public function canComment(): bool
    {
        return in_array($this->accessLevel, [ProjectLink::ACCESS_LVL_COMMENTS], true);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}