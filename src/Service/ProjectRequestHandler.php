<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectLink;
use App\Form\ProjectEditType;
use App\Form\ProjectLinkType;
use App\Form\ProjectNameType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

    /** @var ProjectHelper */
    private $projectHelper;

    /**
     * ProjectRequestHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $em
     * @param LatexHelper $latexHelper
     * @param ProjectHelper $projectHelper
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        LatexHelper $latexHelper,
        ProjectHelper $projectHelper
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->latexHelper = $latexHelper;
        $this->projectHelper = $projectHelper;
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

    public function createMainProjectForm(Project $project, bool $disabled = false): FormInterface
    {
        return $this->createForm(ProjectEditType::class, $project, ['disabled' => $disabled]);
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
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_view', [
                'identifier' => $project->getIdentifier()
            ]))
            ->add('version', ChoiceType::class, [
                'choices' => $this->projectHelper->getProjectVersionChoices($project),
                'preferred_choices' => [$project->getSelectedVersion()]
            ])
            ->getForm();
    }

    public function createLinksConfigForm(Project $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_links_api', ['identifier' => $project->getIdentifier()]))
            ->setMethod('POST')
            ->add('links', CollectionType::class, [
                'label' => ' ',
                'entry_options' => [],
                'entry_type' => ProjectLinkType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype_data' => new ProjectLink($project),
            ])
            ->getForm();
    }
}