<?php

namespace App\Form\Order;

use App\Entity\Menu;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
//                'disabled' => true,
                'label' => 'prenom',
                'data' => $builder->getOption('user')->getFirstName(),
                'attr' => [
                    'placeholder' => 'Votre Prenom',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'data' => $builder->getOption('user')->getLastName(),
                'attr' => [
                    'placeholder' => 'Votre Nom',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => '',
                'data' => $builder->getOption('user')->getEmail(),
                'attr' => [
                    'placeholder' => 'votre@email.fr',
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'telephone',
                'data' => $builder->getOption('user')->getPhone(),
                'attr' => [
                    'placeholder' => '06 00 00 00 00',
                ],
            ])
            ->add('deliveryAddress', TextType::class, [
                'label' => 'Adresse de livraison',
                'data' => $builder->getOption('user')->getPostalAddress(),
                'attr' => [
                    'placeholder' => 'Numéro et rue',
                ],
            ])
            ->add('deliveryCity', TextType::class, [
                'label' => 'Ville',
                'data' => 'NO DEFINED',
//TODO rajouter City dans l'entité User
//                'data' => $builder->getOption('user')->getCity(),
                'attr' => [
                    'placeholder' => 'Bordeaux',
                ],
            ])
            ->add('deliveryPostalCode', TextType::class, [
                'label' => 'Code Postal',
                'data' => 'NO DEFINED',
//TODO rajouter PostalCode dans l'entité User
//            'data' => $builder->getOption('user')->getPostalCode(),
                'attr' => [
                    'placeholder' => '33000',
                ],
            ])
            ->add('serviceDate', DateType::class, [
                'label' => 'Date souhaité de livraison',
                'widget' => 'single_text',
            ])
            ->add('serviceTime', TimeType::class, [
                'label' => 'Heure souhaité de livraison',
                'widget' => 'single_text',
                'input' => 'string'
            ])
            ->add('peopleCount', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'data' => $builder->getOption('menu')->getMinPeople(),
                'attr' => [
                    'min' => $builder->getOption('menu')->getMinPeople(),
                    'max' => $builder->getOption('menu')->getstock(),
                ],
            ])
            ->add('distancekm', IntegerType::class, [
                'label' => 'Distance de livraison en km',
                'required' => false,
                'data' => '0',
                'attr' => [
                    'min' => 0,
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => User::class,
            'menu' => Menu::class,
        ]);
    }
}
