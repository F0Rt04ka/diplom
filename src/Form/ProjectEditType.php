<?php

namespace App\Form;

use App\Entity\EmptyPage;
use App\Entity\MainPage;
use App\Entity\Project;
use App\Form\Page\CVPageType;
use App\Form\Page\EmptyPageType;
use App\Form\Page\MainPageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectEditType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA ,function (FormEvent $event) {
                /** @var Project $project */
                $project = $event->getData();
                $form = $event->getForm();

                switch ($project->getType()) {
                    case Project::TYPE_CV:
                        $form->add('main_page', CVPageType::class);
                        break;
                    case Project::TYPE_DEFAULT:
                        $form
                            ->add('main_page', MainPageType::class)
                            ->add('pages', CollectionType::class, [
                                'entry_type' => EmptyPageType::class,
                                'entry_options' => ['label' => false],
                                'allow_add' => true,
                                'allow_delete' => true,
                            ])
                        ;
                        break;

                }
            })
            ->setDataMapper($this)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('empty_data', null);
    }

    public function mapDataToForms($data, $forms)
    {
        if ($data === null) {
            return;
        }

        if (!$data instanceof Project) {
            throw new UnexpectedTypeException($data, Project::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['main_page']->setData($data->getMainPage($data->getSelectedVersion()));
        if (array_key_exists('pages', $forms)) {
            $forms['pages']->setData(
                $data->getPagesByType(EmptyPage::class, $data->getSelectedVersion())
            );
        }
    }

    /**
     * @param FormInterface[]|\Traversable $forms
     * @param Project $data
     */
    public function mapFormsToData($forms, &$data)
    {
        if (!$data instanceof Project) {
            throw new UnexpectedTypeException($data, Project::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $newVersion = $data->getCurrentVersion() + 1;
        /** @var MainPage $mainPage */
        $mainPage = clone $forms['main_page']->getData();
        $mainPage->setVersion($newVersion);
        $data->addPage($mainPage);

        foreach ($forms['pages'] ?? [] as $form) {
            /** @var EmptyPage $newPage */
            $newPage = clone $form->getData();
            $newPage->setVersion($newVersion);
            $data->addPage($newPage);
        }
    }

}