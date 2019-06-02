<?php

namespace App\Form\Page\WorkProgram;

use App\Entity\WorkProgram\MainPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('headers', CollectionType::class, [
                'label' => 'page.main.headers',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'empty_data' => [],
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
            ->add('discipline', TextType::class, [
                'label' => 'page.main.discipline',
                'constraints' => [
                    new NotNull(),
                ],
                'empty_data' => '',
            ])
            ->add('subtitles', CollectionType::class, [
                'label' => 'page.main.subtitles',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'empty_data' => [],
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
