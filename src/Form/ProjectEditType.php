<?php

namespace App\Form;

use App\Entity\EmptyPage;
use App\Entity\MainPage;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectEditType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project_name')
            ->add('main_page', MainPageType::class)
            ->add('pages', CollectionType::class, [
                'entry_type' => EmptyPageType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
            ])
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
        $forms['project_name']->setData($data->getName());
        $forms['main_page']->setData($data->getMainPage($data->getSelectedVersion()));
        $forms['pages']->setData($data->getPagesByType(EmptyPage::class, $data->getSelectedVersion()));
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
        $data->setName($forms['project_name']->getData());
        /** @var MainPage $mainPage */
        $mainPage = clone $forms['main_page']->getData();
        $mainPage->setVersion($newVersion);
        $data->addPage($mainPage);

        foreach ($forms['pages'] as $form) {
            /** @var EmptyPage $newPage */
            $newPage = clone $form->getData();
            $newPage->setVersion($newVersion);
            $data->addPage($newPage);
        }
    }

}