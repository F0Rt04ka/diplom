<?php

namespace App\Form;

use App\Entity\ProjectLink;
use App\Repository\ProjectLinkRepository;
use App\Service\ProjectHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectLinkType extends AbstractType
{
    /** @var ProjectHelper */
    private $projectHelper;

    /** @var ProjectLinkRepository */
    private $projectLinkRepository;

    /**
     * ProjectLinkType constructor.
     * @param ProjectHelper $projectHelper
     * @param ProjectLinkRepository $projectLinkRepository
     */
    public function __construct(
        ProjectHelper $projectHelper,
        ProjectLinkRepository $projectLinkRepository
    ) {
        $this->projectHelper = $projectHelper;
        $this->projectLinkRepository = $projectLinkRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $accessLevelChoices = [];
        foreach (ProjectLink::EDITORIAL_ACCESS_LEVELS as $item) {
            $accessLevelChoices["project.link.access_levels.$item"] = $item;
        }

        $builder
            ->add('accessLevel', ChoiceType::class, [
                'label' => 'project.link.access_level',
                'choices' => $accessLevelChoices,
            ])
            ->add('projectVersion')
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                if (!$link = $event->getData()) {
                    return;
                }
                if (!$link instanceof ProjectLink) {
                    throw new UnexpectedTypeException($link, ProjectLink::class);
                }

                $event->getForm()->add('projectVersion', ChoiceType::class, [
                    'label' => 'project.version',
                    'choices' =>
                        $this->projectHelper->getProjectVersionChoices($link->getProject()),
                ]);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectLink::class,
        ]);
    }
}
