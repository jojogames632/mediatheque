<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registrant")
 */
class RegistrantController extends AbstractController
{
    /**
     * @Route("", name="registrant_home")
     */
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $limit = 10;
        $page = (int)$request->query->get('page', 1);

        $filters = $request->get('genre');

        $books = $bookRepository->getPaginatedBooks($filters);

        $total = $bookRepository->getTotalBooks($filters);

        // check if it's an ajax request
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('registrant/_booksContent.html.twig', [
                    'books' => $books
                ])
            ]);
        }

        return $this->render('registrant/catalog.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/my-borrowed-books", name="my_borrowed_books")
     */
    public function myBorrowedBooks()
    {
        return $this->render('registrant/myBorrowedBooks.html.twig', [

        ]);
    }
}
