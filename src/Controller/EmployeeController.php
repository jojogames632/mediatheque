<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function addBook()
    {
        return $this->render('employee/addBook.html.twig', [

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
