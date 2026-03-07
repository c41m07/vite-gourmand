<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prenom',
                'attr' => ['placeholder' => 'Votre prenom'],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email',
                'attr' => ['placeholder' => 'votre email'],
            ])
            ->add('currentPassword', PasswordType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Mot de passe actuel',
                'attr' => ['placeholder' => 'Le mot de passe est obligatoire pour modifier votre profil'],
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Telephone',
                'attr' => ['placeholder' => '06 00 00 00 00'],
            ])
            ->add('postalAddress', TextType::class, [
                'required' => false,
                'label' => 'Adresse postale',
                'attr' => ['placeholder' => 'Votre adresse postale'],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => ['label' => 'Nouveau mot de passe', 'attr' => ['placeholder' => 'Laisser vide pour ne pas changer le mot de passe']],
                'second_options' => ['label' => 'Confirmer le nouveau mot de passe', 'attr' => ['placeholder' => 'doit être identique au nouveau mot de passe']],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
