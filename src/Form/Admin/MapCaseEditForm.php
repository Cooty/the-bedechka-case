<?php

namespace App\Form\Admin;

use App\Entity\MapCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Image;
use App\Enum\Admin\ImageSizes;

class MapCaseEditForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('', [
            'data_class' => MapCase::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name_en', TextType::class, [
                'label' => 'Name (EN)',
                'constraints' => new NotBlank()
            ])
            ->add('name_bg', TextType::class, [
                'label' => 'Name (BG)',
                'constraints' => new NotBlank()
            ])
            ->add('description_en', TextareaType::class, [
                'label' => 'Description (EN)',
                'attr' => ['rows' => 4],
                'constraints' => new NotBlank()
            ])
            ->add('description_bg', TextareaType::class, [
                'label' => 'Description (BG)',
                'attr' => ['rows' => 4],
                'constraints' => new NotBlank()
            ])
            ->add('link', TextType::class, [
                'label' => 'Link (optional)',
                'required' => false,
                'constraints' => new Url()
            ])
            ->add('longitude', NumberType::class, [
                'label' => false,
                'constraints' => new NotBlank(),
                'attr' => ['hidden' => true]
            ])
            ->add('latitude', NumberType::class, [
                'label' => false,
                'constraints' => new NotBlank(),
                'attr' => ['hidden' => true]
            ])
            ->add('picture_url', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['hidden' => true]
            ])
            ->add('save', SubmitType::class, ['label'=> 'Update Case']);
    }
}