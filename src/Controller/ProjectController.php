<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectEditType;
use App\Repository\ProjectRepository;
use App\Service\ProjectFilesHelper;
use App\Service\ProjectRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * Class ProjectController
 * @package App\Controller
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/{identifier}", name="project_view")
     * @Entity("project", expr="repository.findByIdentifier(identifier)")
     */
    public function view(Project $project)
    {
        return $this->redirectToRoute('project_edit', ['identifier' => $project->getIdentifier()]);
    }

    /**
     * @Route("/{identifier}/edit", name="project_edit")
     * @Entity("project", expr="repository.findByIdentifier(identifier)")
     */
    public function edit(Project $project, Request $request, ProjectRequestHandler $requestHandler) {
        $mainProjectForm = $requestHandler->handleMainProjectForm($project, $request);
        $projectNameForm = $requestHandler->handleProjectNameForm($project, $request);

        $selectVersionForm = $requestHandler->createSelectVersionForm($project);
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

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'project_form' => $mainProjectForm->createView(),
            'project_name_form' => $projectNameForm->createView(),
            'select_version_form' => $selectVersionForm->createView(),
        ]);
    }

    /**
     * @Route("/{identifier}/version/{version}", name="project_view_version", requirements={"version": "\d+"})
     * @Entity("project", expr="repository.findByIdentifier(identifier)")
     */
    public function viewVersion(
        Project $project,
        $version,
        ProjectRepository $projectRepository,
        ProjectRequestHandler $requestHandler
    ) {
        $selectedVersion = intval($version);
        $projectVersions = $projectRepository->getVersionForProject($project->getId());
        if (($selectedVersion === $project->getCurrentVersion()) ||
            !in_array($selectedVersion, $projectVersions, true)
        ) {
            return $this->redirectToRoute('project_edit', [
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
     * @Route("/{identifier}/{version}/download/{fileType}", name="project_download", requirements={"version"="\d+", "fileType"="pdf|tex"})
     * @Entity("project", expr="repository.findByIdentifier(identifier)")
     */
    public function download(
        Project $project,
        int $version,
        string $fileType,
        ProjectFilesHelper $filesHelper
    ) {
        if ($file = $filesHelper->getDownloadFile($project->getIdentifier(), $version, $fileType)) {
            return $this->file($file);
        }

        throw $this->createNotFoundException('File for this version not founded');
    }
}
