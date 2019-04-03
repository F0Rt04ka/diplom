<?php

namespace App\Form\Page\CV;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'page.cv.social.types.linkedin' => 'linkedin',
                    'page.cv.social.types.twitter'  => 'twitter',
                    'page.cv.social.types.github'   => 'github',
                    'page.cv.social.types.vk'       => 'vk',
                    'page.cv.social.types.skype'    => 'skype',
                ]
            ])
            ->add('link', TextType::class, ['label' => 'page.cv.social.link'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
