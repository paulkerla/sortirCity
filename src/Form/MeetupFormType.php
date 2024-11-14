<?php

namespace App\Form;

use App\Entity\Meetup;
use App\Entity\Place;
use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Activity title',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('startdatetime', null, [
                'label' => 'Start date & time',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('duration', null, [
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('registrationlimitdate', null, [
                'label' => 'Registration deadline',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('maxregistrations', null, [
                'label' => 'Maximum number of participants',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('meetupinfos', null, [
                'label' => 'Meetup description and details',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'attr' => ['class' => 'form-select mb-3']
            ])
//            ->add('organizer', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'username',
//                'attr' => ['class' => 'form-select mb-3']
//            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select mb-3']
            ])
//            ->add('place', EntityType::class, [
//                'class' => Place::class,
//                'choice_label' => 'name',
//                'attr' => ['class' => 'form-select mb-3']
//            ])
//            ->add('state', HiddenType::class, [
//                'data' => $options['data']->getState() ? $options['data']->getState()->getId() : null,
//            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Select an existing place',
                'attr' => ['class' => 'form-select mb-3']
            ])
            // Nested form for creating a new place if needed
            ->add('newPlace', PlaceFormType::class, [
                'label' => 'Or create a new place',
                'mapped'=>false,
                'required' => false,
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meetup::class,
        ]);
    }
}