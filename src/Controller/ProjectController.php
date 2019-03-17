<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectEditType;
use App\Repository\ProjectRepository;
use App\Service\LatexHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        LatexHelper $latex,
        ProjectRepository $projectRepository
    ) {
        $mainPageForm = $this->createForm(ProjectEditType::class, $project);
        $mainPageForm->handleRequest($request);

        if ($mainPageForm->isSubmitted() && $mainPageForm->isValid()) {
            $project->incCurrentVersion();
            $latex->createLatexTemplate($project);

            $em->persist($project);
            $em->flush();
        }

        $versions = $projectRepository->getVersionForProject($project->getId());
        $selectVersionForm = $this->createFormBuilder()
            ->add('version', ChoiceType::class, [
                'choices' => array_combine($versions, $versions),
                'preferred_choices' => [$project->getCurrentVersion()]
            ])
            ->getForm();
        $selectVersionForm->handleRequest($request);
        if ($selectVersionForm->isSubmitted() && $selectVersionForm->isValid()) {
            $selectedVersion = $selectVersionForm->getData()['version'];
            if (intval($selectedVersion) !== $project->getCurrentVersion()) {
                return $this->redirectToRoute('project_view_version', ['identifier' => $project->getIdentifier(), 'version' => $selectedVersion]);
            }
        }


        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'project_form' => $mainPageForm->createView(),
            'select_version_form' => $selectVersionForm->createView(),
        ]);
    }

    /**
     * @Route("/{identifier}/version/{version}", name="project_view_version")
     * @Entity("project", expr="repository.findByIdentifier(identifier)")
     */
    public function viewVersion (
        Project $project,
        $version,
        Request $request,
        ProjectRepository $projectRepository
    ) {
        $selectedVersion = intval($version);
        if (!in_array($selectedVersion, $projectRepository->getVersionForProject($project->getId()), true) ||
            intval($selectedVersion) === $project->getCurrentVersion())
        {
            return $this->redirectToRoute('project_edit', ['identifier' => $project->getIdentifier()]);
        }

        $project->setSelectedVersion($selectedVersion);
        $mainPageForm = $this->createForm(ProjectEditType::class, $project);
        $mainPageForm->handleRequest($request);

        $versions = $projectRepository->getVersionForProject($project->getId());
        $selectVersionForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('project_edit', ['identifier' => $project->getIdentifier()]))
            ->add('version', ChoiceType::class, [
                'choices' => array_combine($versions, $versions),
                'preferred_choices' => [$project->getSelectedVersion()]
            ])
            ->getForm();

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'project_form' => $mainPageForm->createView(),
            'select_version_form' => $selectVersionForm->createView(),
        ]);
    }
}
