<?php

namespace App\Controller;

use App\Entity\Formulaire;
use App\Form\FormulaireType;
use App\Repository\FormulaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formulaire')]
final class FormulaireController extends AbstractController
{
    #[Route(name: 'app_formulaire_index', methods: ['GET'])]
    public function index(Request $request, FormulaireRepository $formulaireRepository): Response
    {
        $category = $request->query->get('category', '');
        $sort = $request->query->get('sort', 'id'); // default sort
        $direction = $request->query->get('direction', 'asc'); // default direction

        $query = $formulaireRepository->createQueryBuilder('f');

        if ($category) {
            $query->andWhere('f.category LIKE :cat')
                ->setParameter('cat', '%' . $category . '%');
        }

        // Sorting
        if (
            in_array($sort, ['id', 'titre', 'description', 'tempsLimite', 'category']) &&
            in_array($direction, ['asc', 'desc'])
        ) {
            $query->orderBy('f.' . $sort, $direction);
        }

        $formulaires = $query->getQuery()->getResult();

        // If AJAX request, return only table rows
        if ($request->isXmlHttpRequest()) {
            return $this->render('formulaire/_table_rows.html.twig', [
                'formulaires' => $formulaires
            ]);
        }

        // Statistics for the chart
        $countBelow10 = $formulaireRepository->createQueryBuilder('f')
            ->select('count(f.id)')
            ->where('f.tempsLimite < 10')
            ->getQuery()
            ->getSingleScalarResult();

        $countAbove10 = $formulaireRepository->createQueryBuilder('f')
            ->select('count(f.id)')
            ->where('f.tempsLimite >= 10')
            ->getQuery()
            ->getSingleScalarResult();

        // Normal page load
        return $this->render('formulaire/index.html.twig', [
            'formulaires' => $formulaires,
            'currentCategory' => $category,
            'currentSort' => $sort,
            'currentDirection' => $direction,
            'countBelow10' => $countBelow10,
            'countAbove10' => $countAbove10,
        ]);
    }


    #[Route('/new', name: 'app_formulaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulaire = new Formulaire();
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulaire/new.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formulaire_show', methods: ['GET'])]
    public function show(Formulaire $formulaire): Response
    {
        return $this->render('formulaire/show.html.twig', [
            'formulaire' => $formulaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formulaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulaire/edit.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formulaire_delete', methods: ['POST'])]
    public function delete(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formulaire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($formulaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/questions', name: 'app_formulaire_questions', methods: ['GET', 'POST'])]
    public function manageQuestions(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        // Handle new question creation
        if ($request->isMethod('POST')) {
            $questionText = $request->request->get('question_text');
            $type = $request->request->get('type');
            $correctAnswer = $request->request->get('correct_answer');
            $points = $request->request->get('points');

            if ($questionText && $type && $correctAnswer && $points) {
                $question = new \App\Entity\Question();
                $question->setQuestionText($questionText);
                $question->setType($type);
                $question->setCorrectAnswer($correctAnswer);
                $question->setPoints((int) $points);
                $question->setFormulaire($formulaire);

                $entityManager->persist($question);
                $entityManager->flush();

                $this->addFlash('success', 'Question ajoutée avec succès!');
                return $this->redirectToRoute('app_formulaire_questions', ['id' => $formulaire->getId()]);
            }
        }

        return $this->render('formulaire/questions.html.twig', [
            'formulaire' => $formulaire,
            'questions' => $formulaire->getQuestions(),
        ]);
    }

    #[Route('/search', name: 'app_formulaire_search', methods: ['GET'])]
    public function search(Request $request, FormulaireRepository $repo): Response
    {
        $category = $request->query->get('category', '');
        $sort = $request->query->get('sort', 'id');
        $direction = $request->query->get('direction', 'asc');

        $formulaires = $repo->findAllSortedAndFiltered($sort, $direction, $category);

        // return only the table rows as HTML
        return $this->render('formulaire/_table_rows.html.twig', [
            'formulaires' => $formulaires,
        ]);
    }
}
