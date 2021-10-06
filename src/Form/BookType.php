<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères',
                        'max' => 255
                    ])
                ]
            ])
            ->add('pictureFilename', FileType::class, [
                'required' => true,
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
                'constraints' => [
                    new Length([
                        'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères',
                        'max' => 2000
                    ])
                ]
                
            ])
            ->add('author', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'maxMessage' => 'L\'auteur doit contenir au maximum {{ limit }} caractères',
                        'max' => 255
                    ])
                ]
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
