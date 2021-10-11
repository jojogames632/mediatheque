<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CreateBookType;
use App\Form\EditBookType;
use App\Form\EditUserType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="admin_home")
     */
    public function manageBooks(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('admin/book/manageBooks.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/manage-users", name="manage_users")
     */
    public function manageUsers(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        $myAdminAccount = $this->getUser();

        return $this->render('admin/user/manageUsers.html.twig', [
            'users' => $users,
            'myAdminAccount' => $myAdminAccount
        ]);
    }

    /**
     * @Route("/create-book", name="create_book")
     */
    public function createBook(Request $request, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(CreateBookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $imageFile = $form->get('pictureFilename')->getData();

            // add new image
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/book/newBook.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/edit-book/{id<\d+>}", name="edit_book")
     */
    public function editBook($id, Request $request, BookRepository $bookRepository, SluggerInterface $slugger): Response
    {
        if (!$bookRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le livre avec l\'id %s n\'existe pas', $id));
        }

        $book = $bookRepository->find($id);
        
        $form = $this->createForm(EditBookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('pictureFilename')->getData();

            if ($imageFile) {

                // delete old image if exists
                $oldImageFilename = $book->getPictureFilename();
                if ($oldImageFilename) {
                    $fullPathPicture = $this->getParameter('books_image_directory') . '/' . $oldImageFilename;
                    if (file_exists($fullPathPicture)) {
                        unlink($fullPathPicture);
                    }
                }

                // add new image
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
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/book/editBook.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("delete-book/{id<\d+>}", name="delete_book")
     */
    public function deleteBook($id, Request $request, BookRepository $bookRepository): Response
    {
        if (!$bookRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le livre avec l\'id %s n\'existe pas', $id));
        }

        $book = $bookRepository->find($id);

        // delete image in uploads directory
        $imageFilename = $book->getPictureFilename();
        if ($imageFilename) {
            $fullPathPicture = $this->getParameter('books_image_directory') . '/' . $imageFilename;
            if (file_exists($fullPathPicture)) {
                unlink($fullPathPicture);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('admin_home', [], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * @Route("/edit-user/{id<\d+>}", name="edit_user")
     */
    public function editUser($id, Request $request, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'utilisateur avec l\'id %s n\'existe pas', $id));
        }

        $user = $userRepository->find($id);
        
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('manage_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/editUser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("delete-user/{id<\d+>}", name="delete_user")
     */
    public function deleteUser($id, Request $request, UserRepository $userRepository): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'utilisateur avec l\'id %s n\'existe pas', $id));
        }

        $user = $userRepository->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('manage_users', [], Response::HTTP_SEE_OTHER);
    }
}
