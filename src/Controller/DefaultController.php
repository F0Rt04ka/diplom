<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\CreateProjectType;
use App\Form\FindExistingProjectType;
use App\Service\ProjectHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        Request $request,
        EntityManagerInterface $em,
        ProjectHelper $projectHelper
    ) {
        $createProjectForm = $this->createForm(CreateProjectType::class, new Project());
        $createProjectForm->handleRequest($request);

        if ($createProjectForm->isSubmitted() && $createProjectForm->isValid()) {
            /** @var Project $project */
            $project = $createProjectForm->getData();
            $link = $projectHelper->createProjectLink($project);
            $em->persist($link);
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_view', ['identifier' => $project->getIdentifier()]);
        }


        $findProjectForm = $this->createForm(FindExistingProjectType::class);
        $findProjectForm->handleRequest($request);

        if ($findProjectForm->isSubmitted() && $findProjectForm->isValid()) {
            $identifier = $findProjectForm->getData()['identifier'];

            if ($projectHelper->findProjectByIdentifier($identifier)) {
                return $this->redirectToRoute('project_view', ['identifier' => $identifier]);
            }

            $findProjectForm->addError(new FormError('Project not exist!'));
        }

        return $this->render('default/index.html.twig', [
            'project_form' => $createProjectForm->createView(),
            'find_project_form' => $findProjectForm->createView(),
        ]);
    }
}
