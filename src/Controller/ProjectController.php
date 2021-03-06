<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\ProjectLink;
use App\Form\CommentsType;
use App\Repository\CommentRepository;
use App\Repository\ProjectLinkRepository;
use App\Repository\ProjectRepository;
use App\Service\AccessHelper;
use App\Service\ProjectFilesHelper;
use App\Service\ProjectHelper;
use App\Service\ProjectRequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/{identifier}/api/comment", name="comment_api",
     *     methods={"GET", "PUT"},
     *     options = { "expose" = true }
     * )
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function apiComment(
        ProjectLink $projectLink,
        Request $request,
        CommentRepository $commentsRepository
    ): JsonResponse {
        $project = $projectLink->getProject();
        if ($request->getMethod() === 'GET') {
            return $this->json([
                'result' => $commentsRepository->findProjectLinkWithNewComments($project),
            ]);
        } elseif ($request->getMethod() === 'PUT') {
            $commentsRepository->readCommentsOnProjectLink($request->get('linkIdentifier'));
        }

        return $this->json([]);
    }

    /**
     * @Route("/{identifier}/api/link", name="link_api",
     *     methods={"GET", "POST", "DELETE"},
     *     options = { "expose" = true }
     * )
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function apiLinks(
        ProjectLink $projectLink,
        Request $request,
        ProjectLinkRepository $linkRepository
    ): JsonResponse {
        $project = $projectLink->getProject();
        if ($request->getMethod() === 'DELETE') {
            if (!$linkIdentifier = $request->get('linkIdentifier')) {
                return $this->apiError('"linkIdentifier" is required');
            }

            return $this->json(
                $linkRepository->deleteByIdentifier($linkIdentifier) ?
                    $linkIdentifier : false
            );
        } elseif ($request->getMethod() === 'GET') {
            return $this->json(
                $project->getDisplayedProjectLinks()->map(function (ProjectLink $link) {
                    return $link->toArray();
                })
            );
        } elseif ($request->getMethod() === 'POST') {
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
     * @Route("/{identifier}", name="view", options = { "expose" = true })
     * @Entity("project_link", expr="repository.findByIdentifier(identifier)")
     */
    public function view(
        ProjectLink $projectLink,
        Request $request,
        AccessHelper $accessHelper,
        EntityManagerInterface $entityManager
    ): Response {
        $project = $projectLink->getProject();
        if ($accessHelper->canEdit()) {
            return $this->editAction($project, $request, $accessHelper->isMainAccess());
        } elseif ($accessHelper->canComment()) {
            $projectLink->getProject()->setCurrentVersion($projectLink->getProjectVersion());
            $commentsData = ['all_comments' => []];
            $projectPageImgageUrls = $this->projectHelper->getProjectImageUrls($project);
            for ($i = 0; $i < count($projectPageImgageUrls); $i++) {
                $commentsData['all_comments'][$i] = [
                    'comments' => $projectLink->getCommentsByPageNum($i)->toArray(),
                ];
            }

            $commentsForm = $this->createForm(CommentsType::class, $commentsData);
            $commentsForm->handleRequest($request);
            if ($commentsForm->isSubmitted() && $commentsForm->isValid()) {
                $entityManager->getRepository(Comment::class)->clearComments($projectLink);

                foreach ($commentsForm->getData()['all_comments'] as $pageNum => $comments) {
                    /** @var Comment $newComment */
                    foreach ($comments['comments'] as $newComment) {
                        $newComment
                            ->setProjectLink($projectLink)
                            ->setIsNew(true)
                            ->setPageNum($pageNum);
                        $projectLink->addComments($newComment);
                        $entityManager->getRepository(Comment::class)->customSave($newComment);
                    }
                }
            }

            return $this->render('project/comments.html.twig', [
                'project' => $project,
                'comments_form' => $commentsForm->createView(),
            ]);
        } elseif ($accessHelper->canViewOnly()) {
            $project->setCurrentVersion($projectLink->getProjectVersion());
            $mainProjectForm = $this->requestHandler->createMainProjectForm($project, true);

            return $this->render('project/view_only.html.twig', [
                'project' => $project,
                'project_form' => $mainProjectForm->createView(),
            ]);
        }

        return new Response('', Response::HTTP_I_AM_A_TEAPOT);
    }

    private function editAction(
        Project $project,
        Request $request,
        bool $isMainAccess
    ): Response {
        $mainProjectForm = $this->requestHandler->handleMainProjectForm($project, $request);
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

        $params = [
            'project' => $project,
            'project_form' => $mainProjectForm->createView(),
            'select_version_form' => $selectVersionForm->createView(),
        ];

        if ($isMainAccess) {
            $projectNameForm = $this->requestHandler->handleProjectNameForm($project, $request);
            $projectLinksForm = $this->requestHandler->createLinksConfigForm($project);
            $params += [
                'project_name_form' => $projectNameForm->createView(),
                'project_links_form' => $projectLinksForm->createView(),
            ];
        }

        return $this->render('project/edit.html.twig', $params);
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
        $mainPageForm = $this->requestHandler->createMainProjectForm($project, true);

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
