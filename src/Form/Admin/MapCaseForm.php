<?php

namespace App\Form\Admin;

use App\Entity\MapCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Image;

class MapCaseForm extends AbstractType
{
    const IMAGE_WIDTH = 350;
    const IMAGE_HEIGHT = 197;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('',[
            'data_class' => MapCase::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name_en', TextType::class, ['label' => 'Name (EN)'])
            ->add('name_bg', TextType::class, ['label' => 'Name (BG)'])
            ->add('description_en', TextareaType::class, [
                'label' => 'Description (EN)',
                'attr' => ['rows' => 4]
            ])
            ->add('description_bg', TextareaType::class, [
                'label' => 'Description (BG)',
                'attr' => ['rows' => 4]
            ])
            ->add('link', TextType::class, [
                'label' => 'Link (optional)',
                'required' => false,
                'constraints' => new Url()
            ])
            ->add('google_maps_url', TextType::class, [
                'label' => 'URL from Google Maps',
                'mapped' => false,
                'constraints' => new Url()
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (optional)',
                'mapped' => false,
                'required' => false,
                'help' => 'The image has to be '.self::IMAGE_WIDTH.'×'.self::IMAGE_HEIGHT.' pixels',
                'constraints' => new Image([
                    'maxSize' => '1024k',
                    'allowPortrait' => false,
                    'allowSquare' => false,
                    'detectCorrupted' => true,
                    'maxWidth' => self::IMAGE_WIDTH,
                    'maxHeight' => self::IMAGE_HEIGHT,
                    'minWidth' => self::IMAGE_WIDTH,
                    'minHeight' => self::IMAGE_HEIGHT
                ])
            ])
            ->add('save', SubmitType::class, ['label'=> 'Save new Case']);
    }
}