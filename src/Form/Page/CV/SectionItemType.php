<?php

namespace App\Form\Page\CV;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'page.cv.section_item.types.item' => 'item',
                    'page.cv.section_item.types.entry' => 'entry',
                    'page.cv.section_item.types.listitem' => 'listitem',
                    'page.cv.section_item.types.listdoubleitem' => 'listdoubleitem',
                    'page.cv.section_item.types.itemwithcomment' => 'itemwithcomment',
                ]
            ])
            ->add('field1', TextType::class, ['empty_data' => ''])
            ->add('field2', TextType::class, ['empty_data' => ''])
            ->add('field3', TextType::class, ['empty_data' => ''])
            ->add('field4', TextType::class, ['empty_data' => ''])
            ->add('field5', TextType::class, ['empty_data' => ''])
            ->add('field6', TextType::class, ['empty_data' => ''])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
