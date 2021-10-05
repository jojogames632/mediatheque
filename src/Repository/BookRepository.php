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

    public function getBooksWithTitleAndFilter($title = null, $filter = null) {
        $query = $this->createQueryBuilder('b')
            ->where('b.title LIKE :title')
            ->setParameter(':title', '%'.$title.'%');

        if ($filter != null) {
            $query->andwhere('b.genre = :genres')
                ->setParameter(':genres', $filter);
        }

        return $query->getQuery()->getResult();
    }
    
    // get total books count
    public function getTotalBooks($filter = null) {
        $query = $this->createQueryBuilder('b')
            ->select('COUNT(b)');

        if ($filter != null) {
            $query->andwhere('b.genre = :genres')
                ->setParameter(':genres', $filter);
        }
        
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getBorrowedBooks($userId) {
        $query = $this->createQueryBuilder('b')
            ->where('b.holder = :userId')
            ->setParameter(':userId', $userId)
            ->orderBy('b.reservationDate', 'ASC');
        
        return $query->getQuery()->getResult();
    }
}
