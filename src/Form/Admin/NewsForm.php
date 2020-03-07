<?php

namespace App\Form\Admin;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Entity\News;

class NewsForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('', [
            'data_class' => News::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'constraints' => new NotBlank()
            ])
            ->add('link', TextType::class, [
                'label' => 'Link',
                'constraints' => new Url()
            ])
            ->add('source', TextType::class, [
                'label' => 'Source',
                'constraints' => new NotBlank()
            ]);
    }
}