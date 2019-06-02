<?php

namespace App\Form\Page\WorkProgram;

use App\Entity\WorkProgram\TablePage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TablePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'page.main.title',
                'empty_data' => '',
            ])
            ->add('textBeforeTable', TextareaType::class, [
                'label' => 'page.table.text_before_table',
                'empty_data' => '',
            ])
            ->add('tableBlock', TableBlockType::class, [
                'label' => 'page.table.table_block',
                'empty_data' => [
                    'columns' => '[]',
                    'cells' => [],
                ],
            ])
            ->add('commentToTable', TextareaType::class, [
                'label' => 'page.table.comment_to_table',
                'empty_data' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TablePage::class,
        ]);
    }
}