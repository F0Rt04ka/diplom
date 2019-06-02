<?php

namespace App\Form\Page\WorkProgram;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

class DateBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', NumberType::class, [
                'label' => 'page.main.date_day',
                'constraints' => [
                    new Range(['min' => 1, 'max' => 31]),
                ],
            ])
            ->add('month', TextType::class, [
                'label' => 'page.main.date_month',
                'constraints' => [
                    new Length(['max' => 13]),
                ]
            ])
            ->add('year', NumberType::class, [
                'label' => 'page.main.date_year',
                'constraints' => [
                    new Range(['min' => 0, 'max' => 9])
                ]
            ])
        ;
    }

}