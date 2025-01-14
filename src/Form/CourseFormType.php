<?php
// src/Form/CourseType.php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('courseName', TextType::class, [
                'label' => 'Course Name',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Programming' => 'programming',
                    'Design' => 'design',
                    'Marketing' => 'marketing',
                ],
                'label' => 'Category',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text',
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}