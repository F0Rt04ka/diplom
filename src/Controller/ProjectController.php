<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectEditType;
use App\Repository\ProjectRepository;
use App\Service\LatexHelper;
use App\Service\ProjectFilesHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
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
    public function edit(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        LatexHelper $latexHelper
    ) {
        $mainPageForm = $this->createForm(ProjectEditType::class, $project);
        $mainPageForm->handleRequest($request);

        if ($mainPageForm->isSubmitted() && $mainPageForm->isValid()) {
            $project->incCurrentVersion();
            $latexHelper->createLatexTemplate($project);

            $em->persist($project);
            $em->flush();
        }

        $selectVersionForm = $this->createSelectVersionForm($project);
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
            'project_form' => $mainPageForm->createView(),
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
        ProjectRepository $projectRepository
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
            'select_version_form' => $this->createSelectVersionForm($project)->createView(),
        ]);
    }

    private function createSelectVersionForm(Project $project): FormInterface
    {
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $this->getDoctrine()->getRepository(Project::class);
        $versions = $projectRepository->getVersionForProject($project->getId());

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_edit', [
                'identifier' => $project->getIdentifier()
            ]))
            ->add('version', ChoiceType::class, [
                'choices' => array_combine($versions, $versions),
                'preferred_choices' => [$project->getSelectedVersion()]
            ])
            ->getForm();
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
