<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    // get books according to current page
    public function getPaginatedBooks($filters = null) {
        $query = $this->createQueryBuilder('b');

        if ($filters != null) {
            $query->andwhere('b.genre = :genres')
                ->setParameter(':genres', $filters);
        }

        return $query->getQuery()->getResult();
    }

    // get total books count
    public function getTotalBooks($filters = null) {
        $query = $this->createQueryBuilder('b')
            ->select('COUNT(b)');

        if ($filters != null) {
            $query->andwhere('b.genre = :genres')
                ->setParameter(':genres', $filters);
        }
        
        return $query->getQuery()->getSingleScalarResult();
    }

    // get books according to current page
    public function searchBooksWithTitle($title, $filters = null) {
        $query = $this->createQueryBuilder('b')
            ->where('b.title LIKE :title')
            ->setParameter(':title', '%'.$title.'%')
        ;

        if ($filters != null) {
            $query->andwhere('b.genre = :genres')
                ->setParameter(':genres', $filters);
        }

        return $query->getQuery()->getResult();
    }
}
