<?php

namespace App\Form\Page\WorkProject;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ApproverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rank', TextType::class, [
                'label' => 'page.main.approver_rank',
                'constraints' => [
                    new Length(['max' => 45]),
                ],
                'attr' => [
                    'class' => 'col',
                ]

            ])
            ->add('fio', TextType::class, [
                'label' => 'page.main.fio',
                'constraints' => [
                    new Length(['max' => 25]),
                ],
            ])
        ;
    }

}