<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Place',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'City',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('streetname', null, [
                'label' => 'Street Name',
                'required' => false,
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('latitude', null, [
                'required' => false,
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('longitude', null, [
                'required' => false,
                'attr' => ['class' => 'form-control mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
