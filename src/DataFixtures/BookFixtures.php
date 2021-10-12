<?php

namespace App\DataFixtures;

use App\Entity\Book;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public const NUMBER_OF_BOOKS = 20;
    public const GENRES = [
        'Développement personnel',
        'Psychologie',
        'Business',
        'Spiritualité',
        'Productivité',
        'Alimentation'
    ];

    public function load(ObjectManager $manager)
    {
        for ($count = 0; $count < self::NUMBER_OF_BOOKS; $count++) {
            $book = new Book();
            $book->setTitle('Vous êtes fou d\'avaler ça');
            $book->setPictureFilename('vous-etes-fou.jpg');
            $book->setReleaseDate(new DateTime('2016-09-14'));
            $book->setDescription('Matières premières avariées, marchandises trafiquées, contrôles d\'hygiène contournés, Christophe Brusset dénonce les multiples dérives dont il est, depuis vingt ans, le complice ou le témoin dans les coulisses de l\'industrie agroalimentaire. À 44 ans, cet ingénieur de haut niveau devenu dirigeant au sein de groupes internationaux a décidé de "faire son devoir" et de briser la loi du silence.');
            $book->setIsBorrowed(false);
            $book->setAuthor('Christophe Brusset');
            
            $randomNumber = random_int(0, 5);
            $book->setGenre(self::GENRES[$randomNumber]);

            $manager->persist($book);
            $manager->flush();
        }
    }
}
