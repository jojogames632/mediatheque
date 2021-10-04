<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data")
 */
class DataController extends AbstractController
{
    /**
     * @Route("/books", name="books_data")
     */
    public function getBooksData(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        $datas = array();

        foreach ($books as $key => $book) {
            $datas[$key]['id'] = $book->getId();
            $datas[$key]['holderId'] = $book->getHolder();
            $datas[$key]['title'] = $book->getTitle();
            $datas[$key]['pictureFilename'] = $book->getPictureFilename();
            $datas[$key]['releaseDate'] = $book->getReleaseDate();
            $datas[$key]['description'] = $book->getDescription();
            $datas[$key]['author'] = $book->getAuthor();
            $datas[$key]['genre'] = $book->getGenre();
            $datas[$key]['reservationDate'] = $book->getReservationDate();
            $datas[$key]['isBorrowed'] = $book->getIsBorrowed();
            $datas[$key]['borrowingDate'] = $book->getBorrowingDate();
        }

        return new JsonResponse($datas);
    }
}
