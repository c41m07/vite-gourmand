<?php

namespace App\Form\Order;

use App\Entity\Menu;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $builder->getOption('user');
        /** @var Menu $menu */
        $menu = $builder->getOption('menu');

        $builder
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'data' => $user->getPhone(),
                'required' => false,
                'attr' => [
                    'placeholder' => '06 00 00 00 00',
                ],
            ])
            ->add('deliveryAddress', TextType::class, [
                'label' => 'Adresse de livraison',
                'data' => $user->getPostalAddress(),
                'attr' => [
                    'placeholder' => 'Numéro et rue',
                ],
            ])
            ->add('deliveryCity', TextType::class, [
                'label' => 'Ville',
                'data' => $user->getCity(),
                'attr' => [
                    'placeholder' => 'Bordeaux',
                ],
            ])
            ->add('deliveryPostalCode', TextType::class, [
                'label' => 'Code postal',
                'data' => $user->getPostalCode(),
                'attr' => [
                    'placeholder' => '33000',
                ],
            ])
            ->add('serviceDate', DateType::class, [
                'label' => 'Date souhaitée de livraison',
                'widget' => 'single_text',
            ])
            ->add('serviceTime', TimeType::class, [
                'label' => 'Heure souhaitée de livraison',
                'widget' => 'single_text',
                'input' => 'string',
            ])
            ->add('peopleCount', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'data' => $menu->getMinPeople(),
                'attr' => [
                    'min' => $menu->getMinPeople(),
                    'max' => $menu->getStock(),
                ],
            ])
            ->add('distancekm', IntegerType::class, [
                'label' => 'Distance de livraison en km',
                'required' => false,
                'data' => 0,
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('needEquipmentLoan', CheckboxType::class, [
                'label' => 'J\'ai besoin d\'un prêt de matériel',
                'required' => false,
                'mapped' => false,
            ])
            ->add('equipmentLoanStartAt', DateTimeType::class, [
                'label' => 'Début du prêt',
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
            ])
            ->add('equipmentLoanEndAt', DateTimeType::class, [
                'label' => 'Fin du prêt',
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
            ])
            ->add('equipmentLoanNote', TextareaType::class, [
                'label' => 'Notes sur le matériel',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Matériel à prévoir, contraintes de reprise, informations utiles',
                ],
            ])
            ->add('note', TextareaType::class, [
                'label' => 'Notes complémentaires',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Informations utiles pour la livraison ou la prestation',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => null,
            'menu' => null,
        ]);
        $resolver->setAllowedTypes('user', [User::class]);
        $resolver->setAllowedTypes('menu', [Menu::class]);
    }
}
