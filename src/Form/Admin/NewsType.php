<?php

namespace App\Form\Admin;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Entity\News;

class NewsType extends AbstractType
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
                'required' => false,
                'help' => 'If left empty the domain name will be taken from the link above'
            ])
            ->add('save', SubmitType::class, ['label'=> 'Save News Item']);
    }
}