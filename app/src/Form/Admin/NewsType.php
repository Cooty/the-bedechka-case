<?php

namespace App\Form\Admin;

use App\Enum\Admin\ImageSizes;
use App\Util\TimeUtil;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('publishingDate', DateType::class, [
                'label' => 'Publishing date',
                'widget' => 'single_text',
                'empty_data' => TimeUtil::getFormattedNow(),
                'constraints' => new NotBlank(),
                'format' => 'yyyy-MM-dd'
            ])
            ->add('link', TextType::class, [
                'label' => 'Link',
                'constraints' => new Url()
            ])
            ->add('source', TextType::class, [
                'label' => 'Source (optional)',
                'required' => false,
                'help' => 'If left empty the domain name will be taken from the link above'
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (optional)',
                'mapped' => false,
                'required' => false,
                'help' => 'The image has to be '.ImageSizes::NEWS_ITEM_IMAGE_WIDTH.'×'.ImageSizes::NEWS_ITEM_IMAGE_HEIGHT.' pixels tall.'
            ])
            ->add('save', SubmitType::class, ['label'=> 'Save News Item']);
    }
}