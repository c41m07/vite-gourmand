<?php

namespace App\Form;

use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('firstName',TextType::class,[
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('lastName',TextType::class,[
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Votre nom',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ]
            ])

            ->add('email',EmailType::class,[
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'votre.email@example.com',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(max: 180),
                ],
            ])
            ->add('subject', TextType::class,[
                'label' => 'Objet',
                'attr' => [
                    'placeholder' => 'Objet de votre message',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 180),
                    new Assert\Length(min: 5),
                ]
            ])
            ->add('message',TextareaType::class,[
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Votre message',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                    new Assert\Length(min: 10),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
        ]);
    }
}
