<?php

namespace App\Form;

use App\Entity\MainPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header1', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('header2', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('universityName1', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('universityName2', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('universityName3', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('author', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('workName', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('workTitle', TextareaType::class, ['required' => false, 'empty_data' => ''])
            ->add('courseName', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('footer', TextType::class, ['required' => false, 'empty_data' => ''])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MainPage::class,
        ]);
    }
}
