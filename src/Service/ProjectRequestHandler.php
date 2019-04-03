<?php

namespace App\Service;

use App\Entity\Project;
use App\Form\ProjectEditType;
use App\Form\ProjectNameType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProjectRequestHandler extends AbstractController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /** @var EntityManagerInterface */
    private $em;

    /** @var LatexHelper */
    private $latexHelper;

    /**
     * ProjectRequestHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $em
     * @param LatexHelper $latexHelper
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        LatexHelper $latexHelper
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->latexHelper = $latexHelper;
    }

    public function handleMainProjectForm(Project $project, Request $request): FormInterface
    {
        $mainPageForm = $this->createForm(ProjectEditType::class, $project);
        $mainPageForm->handleRequest($request);
        if ($mainPageForm->isSubmitted() && $mainPageForm->isValid()) {
            $project->incCurrentVersion();
            $this->latexHelper->createLatexTemplate($project);

            $this->em->persist($project);
            $this->em->flush();
        }

        return $mainPageForm;
    }

    public function handleProjectNameForm(Project $project, Request $request): FormInterface
    {
        $projectNameForm = $this->formFactory->create(ProjectNameType::class, $project);
        $projectNameForm->handleRequest($request);
        if ($projectNameForm->isSubmitted() && $projectNameForm->isValid()) {
            $this->em->persist($project);
            $this->em->flush();
        }

        return $projectNameForm;
    }

    public function createSelectVersionForm(Project $project): FormInterface
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
}