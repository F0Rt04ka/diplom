<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\CreateProjectType;
use App\Form\FindExistingProjectType;
use App\Repository\ProjectRepository;
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
    public function index(Request $request, EntityManagerInterface $em, ProjectRepository $projectRepository)
    {
        $createProjectForm = $this->createForm(CreateProjectType::class, new Project());
        $createProjectForm->handleRequest($request);

        if ($createProjectForm->isSubmitted() && $createProjectForm->isValid()) {
            /** @var Project $project */
            $project = $createProjectForm->getData();
            $project->setIdentifier($projectRepository->generateNewUniqueIdentifier());
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_edit', ['identifier' => $project->getIdentifier()]);
        }


        $findProjectForm = $this->createForm(FindExistingProjectType::class);
        $findProjectForm->handleRequest($request);

        if ($findProjectForm->isSubmitted() && $findProjectForm->isValid()) {
            $identifier = $findProjectForm->getData()['identifier'];

            if ($projectRepository->findByIdentifier($identifier)) {
                return $this->redirectToRoute('project_view', ['identifier' => $identifier]);
            }

            $findProjectForm->addError(new FormError('Project not exist!'));
        }


        return $this->render('default/index.html.twig', [
            'project_form'      => $createProjectForm->createView(),
            'find_project_form' => $findProjectForm->createView(),
        ]);
    }
}
