<?php

namespace App\Form\Page\WorkProgram;

use App\Entity\WorkProgram\MainPage;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class MainPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header', CKEditorType::class, [
                'label' => 'page.main.header',
                'empty_data' => '',
                'config_name' => 'inline_config',
                'inline' => true,
            ])
            ->add('approver', ApproverType::class, [
                'label' => 'page.main.approver',
                'empty_data' => [
                    'rank' => '',
                    'fio' => '',
                ],
            ])
            ->add('date_block', DateBlockType::class, [
                'label' => 'page.main.date',
                'empty_data' => [
                    'day' => 0,
                    'month' => '',
                    'year' => 0,
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'page.main.title',
                'constraints' => [
                    new NotNull(),
                ],
                'empty_data' => '',
            ])
            ->add('subtitle', CKEditorType::class, [
                'label' => 'page.main.subtitle',
                'empty_data' => '',
                'config_name' => 'inline_config',
                'inline' => true,
            ])
            ->add('faculty', TextType::class, [
                'label' => 'page.main.faculty',
                'constraints' => [
                    new Length(['max' => 15]),
                ],
                'empty_data' => '',
            ])
            ->add('cathedra', TextType::class, [
                'label' => 'page.main.cathedra',
                'constraints' => [
                    new Length(['max' => 20]),
                ],
                'empty_data' => '',
            ])
            ->add('developer', TextType::class, [
                'label' => 'page.main.developer',
                'constraints' => [
                    new Length(['max' => 100]),
                ],
                'empty_data' => '',
            ])
            ->add('footer', TextType::class, [
                'label' => 'page.main.footer',
                'constraints' => [
                    new NotNull(),
                ],
                'empty_data' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MainPage::class,
        ]);
    }
}
