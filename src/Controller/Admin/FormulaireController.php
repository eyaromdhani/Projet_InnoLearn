<?php

namespace App\Controller\Admin;

use App\Entity\Formulaire;
use App\Form\FormulaireType;
use App\Repository\FormulaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/quiz')]
class FormulaireController extends AbstractController
{
    #[Route('/', name: 'admin_quiz_index', methods: ['GET'])]
    public function index(Request $request, FormulaireRepository $formulaireRepository): Response
    {
        $category = $request->query->get('category', '');
        $sort = $request->query->get('sort', 'id');
        $direction = $request->query->get('direction', 'asc');

        $query = $formulaireRepository->createQueryBuilder('f');

        if ($category) {
            $query->andWhere('f.category LIKE :cat')
                ->setParameter('cat', '%' . $category . '%');
        }

        if (
            in_array($sort, ['id', 'titre', 'description', 'tempsLimite', 'category']) &&
            in_array($direction, ['asc', 'desc'])
        ) {
            $query->orderBy('f.' . $sort, $direction);
        }

        $formulaires = $query->getQuery()->getResult();

        // If AJAX request, return only table rows
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/quiz/_table_rows.html.twig', [
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

        return $this->render('admin/quiz/index.html.twig', [
            'formulaires' => $formulaires,
            'currentCategory' => $category,
            'currentSort' => $sort,
            'currentDirection' => $direction,
            'countBelow10' => $countBelow10,
            'countAbove10' => $countAbove10,
        ]);
    }

    #[Route('/new', name: 'admin_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulaire = new Formulaire();
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulaire);
            $entityManager->flush();

            return $this->redirectToRoute('admin_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/quiz/new.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/quiz/edit.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formulaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formulaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_quiz_index', [], Response::HTTP_SEE_OTHER);
    }
}
