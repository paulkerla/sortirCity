<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Place;
use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class ProfileditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'attr' => ['class' => 'form-select mb-3']
            ])
            ->add('firstname', null, [
                'attr' => ['class' => 'form-select mb-3']
            ])
            ->add('surname', null, [
                'attr' => ['class' => 'form-select mb-3']
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select mb-3']
            ])
            ->add('phonenumber', TelType::class, [
                'label' => 'Phone number',
                'attr' => ['class' => 'form-select mb-3']
            ])
            ->add('avatarurl', FileType::class, [
                'label' => 'Photo de profil (jpg, png)',
                'required' => false,
                'attr' => ['class' => 'form-select mb-3'],
                'data_class' => null,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Sign up',
                'attr' => ['class' => 'btn btn-primary w-100']
                ]);
        ;
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
