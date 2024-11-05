<?php

namespace App\Form;

use App\Entity\Meetup;
use App\Entity\Place;
use App\Entity\Site;
use App\Entity\State;
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
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('startdatetime', null, [
                'widget' => 'single_text',
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('duration', null, [
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('registrationlimitdate', null, [
                'widget' => 'single_text',
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('maxregistrations', null, [
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('meetupinfos', null, [
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('organizer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
            ])
//            ->add('state', EntityType::class, [
//                'class' => State::class,
//                'choice_label' => 'label',
//                'attr' => ['class' => 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500']
//            ])
 ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meetup::class,
        ]);
    }
}
