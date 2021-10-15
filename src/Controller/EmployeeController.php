<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/employee")
 */
class EmployeeController extends AbstractController
{
    /**
     * @Route("", name="employee_home")
     */
    public function index(Request $request, BookRepository $bookRepository, PaginatorInterface $paginator): Response
    {
        $this->updateBooksReservation($bookRepository);

        $filter = $request->get('genre');
        $title = $request->get('title');

        $limit = 10;
        $page = $request->query->get('page', 1);

        $user = $this->getUser();
        $books = $bookRepository->getBooksWithTitleAndFilter($title, $filter);
        $paginatedBooks = $paginator->paginate($books, $page, $limit);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('employee/_booksContent.html.twig', [
                    'books' => $paginatedBooks,
                    'user' => $user
                ])
            ]);
        }

        $books = $bookRepository->findAll();
        $paginatedBooks = $paginator->paginate($books, $page, $limit);

        return $this->render('employee/catalog.html.twig', [
            'books' => $paginatedBooks,
            'user' => $user
        ]);
    }

    // Set available again all books reserved 3 days ago or more without someone coming to pick them up
    public function updateBooksReservation(BookRepository $bookRepository)
    {
        $books = $bookRepository->getAllReservedBooks();
        $now = new DateTime();
        $threeDaysInSeconds = 3 * 24 * 60 * 60;

        foreach ($books as $book) {
            if ($now->getTimestamp() - $book->reservationDate->getTimestamp() > $threeDaysInSeconds) {
                $book->setReservationDate(null);
                $book->setHolder(null);
                $book->setIsBorrowed(false);

                $entitManager = $this->getDoctrine()->getManager();
                $entitManager->persist($book);
                $entitManager->flush();
            }
        }
    }

    /**
     * @Route("/borrow-book/{id<\d+>}", name="borrow_book_employee")
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
        $book->setBorrowingDate(new DateTime());
        $book->setIsBorrowed(true);
        $book->setHolder($user);

        $entitManager = $this->getDoctrine()->getManager();
        $entitManager->persist($book);
        $entitManager->flush();

        return $this->redirectToRoute('employee_home');
    }

    /**
     * @Route("/validate-accounts-list", name="validate_accounts_list")
     */
    public function getValidateAccountsList(UserRepository $userRepository): Response
    {
        $unActiveUsers = $userRepository->findBy(['isActive' => false]);

        return $this->render('employee/validateAccounts.html.twig', [
            'unActiveUsers' => $unActiveUsers
        ]);
    }

    /**
     * @Route("/validate-account/{id<\d+>}", name="validate_account")
     */
    public function activateAccount($id, UserRepository $userRepository)
    {   
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException('L\'utilisateur avec l\'id %s n\'existe pas', $id);
        }

        $user = $userRepository->find($id);
        $user->setIsActive(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('validate_accounts_list');
    }

    /**
     * @Route("/add-book", name="add_book")
     */
    public function addBook(Request $request, SluggerInterface $slugger)
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('pictureFilename')->getData();

            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('books_image_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw $this->createNotFoundException('Erreur lors du téléchargement de votre image');
            }

            $book->setPictureFilename($newFilename);
            $book->setIsBorrowed(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre ajouté avec succès !');
        }

        return $this->render('employee/addBook.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm-borrowing-list", name="confirm_borrowing_list")
     */
    public function getConfirmBorrowingList(BookRepository $bookRepository)
    {   
        $this->updateBooksReservation($bookRepository);

        $books = $bookRepository->findBy([
            'isBorrowed' => true,
            'borrowingDate' => null
        ]);

        return $this->render('employee/confirmBorrowing.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/confirm-borrowing/{id<\d+>}", name="confirm_borrowing")
     */
    public function confirmBorrowing($id, BookRepository $bookRepository)
    {
        if (!$bookRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le livre avec l\'id %s n\'existe pas', $id));
        }

        $book = $bookRepository->find($id);

        // prevent employees to use this function on wrong books when typing this route in url directly *** [Security] ***
        if ($book->getBorrowingDate()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas confirmer l\'emprunt d\'un livre déja emprunté');
        }

        $book->setBorrowingDate(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('confirm_borrowing_list');
    }

    /**
     * @Route("/borrowed-books-list", name="borrowed_books_list")
     */
    public function getBorrowedBooksList(BookRepository $bookRepository)
    {
        $books = $bookRepository->getAllBorrowedBooks();
        $now = new DateTime();

        return $this->render('employee/borrowedBooks.html.twig', [
            'books' => $books,
            'now' => $now
        ]);
    }

    /**
     * @Route("/confirm-return/{id<\d+>}", name="confirm_return")
     */
    public function confirmReturn($id, BookRepository $bookRepository)
    {
        if (!$bookRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le livre avec l\'id %s n\'existe pas', $id));
        }

        $book = $bookRepository->find($id);

        // prevent employees to use this function on wrong books when typing this route in url directly *** [Security] ***
        if (!$book->getBorrowingDate()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas confirmer le retour d\'un livre non emprunté');
        }

        $book->setIsBorrowed(false);
        $book->setReservationDate(null);
        $book->setBorrowingDate(null);
        $book->setHolder(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('borrowed_books_list');
    }

}
