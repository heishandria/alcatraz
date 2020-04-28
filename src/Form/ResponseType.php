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

class ResponseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentResponse', TextType::class)
            ->add('question', EntityType::class, [
                'class' => 'App\Entity\Question',
            ])
            ->add('isGoodResponse', ChoiceType::class, [
                'choices'  => [
                    1 => 1,
                    0 => 0
                ]
            ])
            ->add('scoring', IntegerType::class)
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
            'data_class' => 'App\Entity\Response',
            'csrf_protection' => false
        ));
    }
}