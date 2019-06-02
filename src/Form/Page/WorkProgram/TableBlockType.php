<?php

namespace App\Form\Page\WorkProgram;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TableBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('columns', TextType::class, ['empty_data' => '[]'])
            ->add('cells', TextType::class, ['empty_data' => '[]'])
        ;
    }
}