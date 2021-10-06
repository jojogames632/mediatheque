<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/employee")
 */
class EmployeeController extends AbstractController
{

    /**
     * @Route("", name="employee_home")
     */
    public function index(UserRepository $userRepository): Response
    {
        $unActiveUsers = $userRepository->findBy(['isActive' => false]);

        return $this->render('employee/validAccounts.html.twig', [
            'unActiveUsers' => $unActiveUsers
        ]);
    }

    /**
     * @Route("/activate-account/{id<\d+>}", name="activate_account")
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

        return $this->redirectToRoute('employee_home');
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

            $this->addFlash('success', 'Livre créé avec succès !');
        }

        return $this->render('employee/addBook.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm-borrowing", name="confirm_borrowing")
     */
    public function confirmBorrowing()
    {
        return $this->render('employee/confirmBorrowing.html.twig', [

        ]);
    }

    /**
     * @Route("/borrowed-books", name="borrowed_books")
     */
    public function getBorrowedBooks()
    {
        return $this->render('employee/borrowedBooks.html.twig', [

        ]);
    }
}