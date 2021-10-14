<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\User;

class EditBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('pictureFilename', FileType::class, [
				'mapped' => false,
				'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024K',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Veuillez importer une image au format PNG ou JPEG uniquement'
                    ])
                ]
            ])
            ->add('releaseDate', BirthdayType::class, [
                'required' => true,
                'format' => 'dd-MM-yyyy'
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
            ])
            ->add('author', TextType::class, [
                'required' => true,
            ])
            ->add('genre', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Développement personnel' => 'Développement personnel',
                    'Psychologie' => 'Psychologie',
                    'Business' => 'Business',
                    'Spiritualité' => 'Spiritualité',
                    'Productivité' => 'Productivité',
                    'Alimentation' => 'Alimentation'
                ]
			])
            ->add('holder', EntityType::class, [
                'required' => false,
                'class' => User::class,
                'choice_label' => 'email'
			])
			->add('reservationDate', DateTimeType::class, [
				'required' => false
			])
			->add('borrowingDate', DateTimeType::class, [
				'required' => false
			])
			->add('isBorrowed', CheckboxType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
