<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\ResponseType;

class QuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleQuestion', TextType::class)
            ->add('survey', EntityType::class, [
                'class' => 'App\Entity\Survey',
            ])
            ->add('format', EntityType::class, [
                'class' => 'App\Entity\QuestionFormat',
            ])
            ->add('responses', CollectionType::class, [
                'entry_type' => ResponseType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ])
            ->add('order', IntegerType::class)
            ->add('active', ChoiceType::class, [
                'choices'  => [
                    1 => 1,
                    0 => 0
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Question',
            'csrf_protection' => false
        ));
    }
}