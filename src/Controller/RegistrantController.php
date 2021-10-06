<?php

namespace App\Controller;

use App\Repository\BookRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

        $filter = $request->get('genre');
        $title = $request->get('title');

        $total = $bookRepository->getTotalBooks($filter);

        $user = $this->getUser();
        $books = $bookRepository->getBooksWithTitleAndFilter($title, $filter);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('registrant/_booksContent.html.twig', [
                    'books' => $books,
                    'user' => $user
                ])
            ]);
        }

        $books = $bookRepository->findAll();

        return $this->render('registrant/catalog.html.twig', [
            'books' => $books,
            'user' => $user
        ]);
    }

    /**
     * @Route("/my-borrowed-books", name="my_borrowed_books")
     */
    public function getMyBorrowedBooks(BookRepository $bookRepository)
    {
        $userId = $this->getUser()->getId();
        $borrowedBooks = $bookRepository->getBorrowedBooks($userId); 
        
        return $this->render('registrant/myBorrowedBooks.html.twig', [
            'borrowedBooks' => $borrowedBooks
        ]);
    }

    /**
     * @Route("/borrow-book/{id<\d+>}", name="borrow_book_registrant")
     */
    public function borrowBook($id, BookRepository $bookRepository)
    {
        if (!$bookRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le livre avec l\'id %s n\'existe pas', $id));
        }

        $user = $this->getUser();
        $book = $bookRepository->find($id);

        // stop action if book is already borrowed *** [security] ***
        if ($book->getIsBorrowed()) {
            throw new AccessDeniedException('Vous ne pouvez pas emprunter un livre déjà emprunté');
        }

        $book->setReservationDate(new DateTime());
        $book->setIsBorrowed(true);
        $book->setHolder($user);

        $entitManager = $this->getDoctrine()->getManager();
        $entitManager->persist($book);
        $entitManager->flush();

        return $this->redirectToRoute('registrant_home');
    }
}
