<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\AIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books')]
final class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('student/book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('student/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/summarize', name: 'app_book_summarize', methods: ['POST'])]
    public function summarize(Book $book, AIService $aiService): JsonResponse
    {
        if (!$book->getPdfPath()) {
            return new JsonResponse(['error' => 'Aucun fichier PDF associé à ce livre.'], 400);
        }

        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/books/' . $book->getPdfPath();
        
        $summary = $aiService->summarizeBook($pdfPath);

        return new JsonResponse(['summary' => $summary]);
    }
}
