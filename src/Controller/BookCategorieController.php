<?php

namespace App\Controller;

use App\Entity\BookCategorie;
use App\Form\BookCategorieType;
use App\Repository\BookCategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book/categorie')]
final class BookCategorieController extends AbstractController
{
    #[Route(name: 'app_book_categorie_index', methods: ['GET'])]
    public function index(BookCategorieRepository $bookCategorieRepository): Response
    {
        return $this->render('book_categorie/index.html.twig', [
            'book_categories' => $bookCategorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bookCategorie = new BookCategorie();
        $form = $this->createForm(BookCategorieType::class, $bookCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bookCategorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_categorie/new.html.twig', [
            'book_categorie' => $bookCategorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_categorie_show', methods: ['GET'])]
    public function show(BookCategorie $bookCategorie): Response
    {
        return $this->render('book_categorie/show.html.twig', [
            'book_categorie' => $bookCategorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BookCategorie $bookCategorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookCategorieType::class, $bookCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_categorie/edit.html.twig', [
            'book_categorie' => $bookCategorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, BookCategorie $bookCategorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bookCategorie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bookCategorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
