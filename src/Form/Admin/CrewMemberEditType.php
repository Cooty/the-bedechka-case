<?php

namespace App\Form\Admin;

use App\Entity\CrewMember;
use App\Enum\Admin\ImageSizes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class CrewMemberEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameEn', TextType::class, [
                'label' => 'Name (EN)',
                'constraints' => new NotBlank()
            ])
            ->add('nameBg', TextType::class, [
                'label' => 'Name (BG)',
                'constraints' => new NotBlank()
            ])
            ->add('titleEn', TextType::class, [
                'label' => 'Job title (EN)',
                'constraints' => new NotBlank()
            ])
            ->add('titleBg', TextType::class, [
                'label' => 'Job title (BG)',
                'constraints' => new NotBlank()
            ])
            ->add('link', TextType::class, [
                'label' => 'Link (optional)',
                'required' => false,
                'constraints' => new Url(),
                'help' => 'Has to start with "http://" or "https://"'
            ])
            ->add('linkLabel', TextType::class, [
                'label' => 'Label for the link (optional)',
                'required' => false,
                'help' => 'If filled, this text will appear as the clickable text for the link'
            ])
            ->add('linkLabelBg', TextType::class, [
                'label' => 'Label for the link (BG - optional)',
                'required' => false,
                'help' => 'If filled, this text will appear as the clickable text for the link in Bulgarian'
            ])
            ->add('image', FileType::class, [
                'label' => 'New Image',
                'mapped' => false,
                'required' => false,
                'help' => 'The image has to be '.ImageSizes::CREW_MEMBER_PORTRAIT_WIDTH.'×'.ImageSizes::CREW_MEMBER_PORTRAIT_HEIGHT.' pixels'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Update Crew Member'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CrewMember::class,
        ]);
    }
}