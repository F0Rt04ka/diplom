<?php

namespace App\Controller;

use App\Entity\ProjectLink;
use App\Form\ProjectEditType;
use App\Repository\ProjectLinkRepository;
use App\Repository\ProjectRepository;
use App\Service\ProjectFilesHelper;
use App\Service\ProjectHelper;
use App\Service\ProjectRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * Class ProjectController
 * @package App\Controller
 * @Route("/project", name="project_")
 */
class ProjectController extends AbstractController
{
    /** @var ProjectRequestHandler */
    private $requestHandler;

    /** @var ProjectHelper */
    private $projectHelper;

    /**
     * ProjectController constructor.
     * @param ProjectRequestHandler $requestHandler
     * @param ProjectHelper $projectHelper
     */
    public function __construct(ProjectRequestHandler $requestHandler, ProjectHelper $projectHelper)
    {
        $this->requestHandler = $requestHandler;
        $this->projectHelper = $projectHelper;
    }

    /**
     * @Route("/{identifier}/api/links", name="links_api",
     *     methods={"GET", "POST", "DELETE"},
     *     options = { "expose" = true }
     * )
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function apiLinks(ProjectLink $projectLink, Request $request, ProjectLinkRepository $linkRepository)
    {
        $project = $projectLink->getProject();
        if ($request->getMethod() === 'DELETE') {
            if (!$linkIdentifier = $request->get('linkIdentifier')) {
                return $this->apiError('"linkIdentifier" is required');
            }

            return $this->json(
                $linkRepository->deleteByIdentifier($project, $linkIdentifier) ?
                    $linkIdentifier : false
            );
        } elseif ($request->getMethod() === 'GET') {
            return $this->json(
                $project->getDisplayedProjectLinks()->map(function (ProjectLink $link) {
                    return $link->toArray();
                })
            );
        } else {
            $projectLinksForm = $this->requestHandler->createLinksConfigForm($project);
            $projectLinksForm->handleRequest($request);
            if ($projectLinksForm->isValid()) {
                $result = array_map(
                    function (ProjectLink $newLink) use ($project, $linkRepository) {
                        $newLink->setProject($project);
                        $newLink->setIdentifier($linkRepository->generateNewUniqueIdentifier());
                        $project->addProjectLink($newLink);
                        $this->getDoctrine()->getManager()->persist($newLink);
                        return $newLink->toArray();
                    },
                    $projectLinksForm->getData()['links']
                );
                $this->getDoctrine()->getManager()->flush();

                return $this->json(['result' => $result]);
            }
        }

        return $this->json([]);
    }

    private function apiError(string $message = '')
    {
        return $this->json(['error' => ['message' => $message]]);
    }

    /**
     * @Route("/{identifier}", name="view")
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function view(
        ProjectLink $projectLink,
        Request $request
    ) {
        if ($projectLink->getAccessLevel() === ProjectLink::ACCESS_LVL_MAIN_LINK) {
            return $this->editAction($projectLink, $request);
        }
    }

    private function editAction(
        ProjectLink $projectLink,
        Request $request
    ) {
        $project = $projectLink->getProject();
        $mainProjectForm = $this->requestHandler->handleMainProjectForm($project, $request);
        $projectNameForm = $this->requestHandler->handleProjectNameForm($project, $request);

        $selectVersionForm = $this->requestHandler->createSelectVersionForm($project);
        $selectVersionForm->handleRequest($request);
        if ($selectVersionForm->isSubmitted() && $selectVersionForm->isValid()) {
            $selectedVersion = $selectVersionForm->getData()['version'];
            if (intval($selectedVersion) !== $project->getCurrentVersion()) {
                return $this->redirectToRoute('project_view_version', [
                    'identifier' => $project->getIdentifier(),
                    'version' => $selectedVersion,
                ]);
            }
        }

        $projectLinksForm = $this->requestHandler->createLinksConfigForm($project);

        return $this->render('project/edit.html.twig', [
            'access_level' => $projectLink->getAccessLevel(),
            'project' => $project,
            'project_form' => $mainProjectForm->createView(),
            'project_name_form' => $projectNameForm->createView(),
            'project_links_form' => $projectLinksForm->createView(),
            'select_version_form' => $selectVersionForm->createView(),
        ]);
    }

    /**
     * @Route("/{identifier}/version/{version}", name="view_version", requirements={"version": "\d+"})
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function viewVersion(
        ProjectLink $projectLink,
        $version,
        ProjectRepository $projectRepository,
        ProjectRequestHandler $requestHandler
    ) {
        $project = $projectLink->getProject();
        $selectedVersion = intval($version);
        $projectVersions = $projectRepository->getVersionForProject($project->getId());
        if (($selectedVersion === $project->getCurrentVersion()) ||
            !in_array($selectedVersion, $projectVersions, true)
        ) {
            return $this->redirectToRoute('project_view', [
                'identifier' => $project->getIdentifier()
            ]);
        }

        $project->setSelectedVersion($selectedVersion);
        $mainPageForm = $this->createForm(ProjectEditType::class, $project,
            ['attr' => ['readonly' => true]]
        );

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'project_form' => $mainPageForm->createView(),
            'select_version_form' => $requestHandler->createSelectVersionForm($project)->createView(),
        ]);
    }

    /**
     * @Route("/{identifier}/{version}/download/{fileType}", name="download",
     *     requirements={"version"="\d+", "fileType"="pdf|tex"}
     * )
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function download(
        ProjectLink $projectLink,
        int $version,
        string $fileType,
        ProjectFilesHelper $filesHelper
    ) {
        $project = $projectLink->getProject();
        if ($file = $filesHelper->getDownloadFile($project->getIdentifier(), $version, $fileType)) {
            return $this->file(
                $file,
                $filesHelper->createFilenameForDownloadedFile($project, $version, $fileType)
            );
        }

        throw $this->createNotFoundException('File for this version not founded');
    }
}
