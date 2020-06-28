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

class MapCaseEditType extends AbstractType
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
            ->add('image', FileType::class, [
                'label' => 'Image (optional)',
                'mapped' => false,
                'required' => false,
                'help' => 'The image has to be '.ImageSizes::MAP_CASE_POPUP_WIDTH.'×'.ImageSizes::MAP_CASE_POPUP_HEIGHT.' pixels',
                'constraints' => new Image([
                    'maxSize' => '1024k',
                    'allowPortrait' => false,
                    'allowSquare' => false,
                    'detectCorrupted' => true,
                    'maxWidth' => ImageSizes::MAP_CASE_POPUP_WIDTH,
                    'maxHeight' => ImageSizes::MAP_CASE_POPUP_HEIGHT,
                    'minWidth' => ImageSizes::MAP_CASE_POPUP_WIDTH,
                    'minHeight' => ImageSizes::MAP_CASE_POPUP_HEIGHT
                ])
            ])
            ->add('save', SubmitType::class, ['label'=> 'Update Case']);
    }
}