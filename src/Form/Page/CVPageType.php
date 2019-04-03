<?php

namespace App\Form\Page;

use App\Entity\CVMainPage;
use App\Form\Page\CV\PhoneType;
use App\Form\Page\CV\SectionType;
use App\Form\Page\CV\SocialType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CVPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fontSize', ChoiceType::class, [
                'label' => 'page.cv.fontSize',
                'choices' => [
                    'page.cv.fontSizes.10' => 10,
                    'page.cv.fontSizes.11' => 11,
                    'page.cv.fontSizes.12' => 12,
                ]
            ])
            ->add('style', ChoiceType::class, [
                'label' => 'page.cv.style',
                'choices' => [
                    'page.cv.styles.classic'  => 'classic',
                    'page.cv.styles.casual'   => 'casual',
                    'page.cv.styles.banking'  => 'banking',
                    'page.cv.styles.oldstyle' => 'oldstyle',
                    'page.cv.styles.fancy'    => 'fancy',
                ]
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'page.cv.color',
                'choices' => [
                    'colors.blue'     => 'blue',
                    'colors.black'    => 'black',
                    'colors.burgundy' => 'burgundy',
                    'colors.green'    => 'green',
                    'colors.grey'     => 'grey',
                    'colors.orange'   => 'orange',
                    'colors.purple'   => 'purple',
                    'colors.red'      => 'red',
                ]
            ])
            ->add('firstName', TextType::class, ['label' => 'page.cv.firstName'])
            ->add('lastName', TextType::class, ['label' => 'page.cv.lastName'])
            ->add('title', TextType::class, ['label' => 'page.cv.title'])
//            ->add('address', )
            ->add('phones', CollectionType::class, [
                'label' => 'page.cv.phones',
                'entry_type' => PhoneType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'empty_data' => [],
            ])
            ->add('email', EmailType::class)
            ->add('social', CollectionType::class, [
                'label' => 'page.cv.social.block',
                'entry_type' => SocialType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'empty_data' => [],
            ])
            ->add('extraInfo', TextType::class, [
                'label' => 'page.cv.extraInfo',
                'empty_data' => '',
            ])
            ->add('homepage', TextType::class, [
                'label' => 'page.cv.homepage',
                'empty_data' => '',
            ])
            ->add('quote', TextType::class, [
                'label' => 'page.cv.quote',
                'empty_data' => '',
            ])
            ->add('sections', CollectionType::class, [
                'label' => 'page.cv.sections',
                'entry_type' => SectionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'empty_data' => [],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CVMainPage::class,
        ]);
    }

}