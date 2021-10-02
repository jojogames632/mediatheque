<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'invalid_message' => 'Cet email est déja utilisé',
                'required' => true
            ])
            ->add('birthdate', BirthdayType::class, [
                'required' => true
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères',
                    ])
                ],
                'required' => true
            ])
            ->add('plainPassword', RepeatedType::class, [
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'required' => true,
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
