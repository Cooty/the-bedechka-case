<?php

namespace App\Form\Admin;

use App\Entity\Screening;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use App\Util\TimeUtil;

class ScreeningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: Add non-persistent fields to generate the start data (one for date, one for hours and one for minutes)
        // TODO: Figure out how to arrange date and time fields in one line
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date',
                'mapped' => false,
                'widget' => 'single_text',
                'empty_data' => TimeUtil::getFormattedNow(),
                'constraints' => new NotBlank(),
                'format' => 'yyyy-MM-dd'
            ])
            ->add('hours', NumberType::class, [
                'mapped' => false,
                'html5' => true,
                'empty_data' => '00',
                'attr' => [
                    'min' => 0,
                    'max' => 23
                ],
                'constraints' => new PositiveOrZero()
            ])
            ->add('minutes', NumberType::class, [
                'mapped' => false,
                'html5' => true,
                'empty_data' => '00',
                'attr' => [
                    'min' => 0,
                    'max' => 59
                ],
                'constraints' => new PositiveOrZero()
            ])
            ->add('nameEN', TextType::class, [
                'label' => 'Name (EN)',
                'constraints' => new NotBlank()
            ])
            ->add('nameBG', TextType::class, [
                'label' => 'Name (BG)',
                'constraints' => new NotBlank()
            ])
            ->add('venueNameEN', TextType::class, [
                'label' => 'Name of the venue (EN)',
                'constraints' => new NotBlank()
            ])
            ->add('venueNameBG', TextType::class, [
                'label' => 'Name of the venue (BG)',
                'constraints' => new NotBlank()
            ])
            ->add('venueLink', UrlType::class, [
                'label' => 'Link for the venue (optional)',
                'required' => false,
                'constraints' => new Url()
            ])
            ->add('eventLink', UrlType::class, [
                'label' => 'Link for the event (optional)',
                'required' => false,
                'constraints' => new Url()
            ])
            ->add('save', SubmitType::class, ['label'=> 'Save Screening']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Screening::class,
        ]);
    }
}
