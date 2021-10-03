<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(): Response
    {
        

        return $this->render('registrant/catalog.html.twig', [
            
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
