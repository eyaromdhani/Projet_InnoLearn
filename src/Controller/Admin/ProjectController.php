<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/admin/projects')]
// #[IsGranted('ROLE_ADMIN')]  // Temporarily commented for testing
class ProjectController extends AbstractController
{
    #[Route('/', name: 'admin_projects', methods: ['GET'])]
public function index(ProjectRepository $projectRepository, Request $request): Response
{
    $status = (string) $request->query->get('status', '');
    $search = (string) $request->query->get('search', '');
    
    $projects = $projectRepository->findByFilters($search, $status);
    
    // Récupérer TOUS les projets pour les stats
    $allProjects = $projectRepository->findAll();
    
    // Calculer les stats manuellement
    $stats = [
        'total' => count($allProjects),
        'draft' => 0,
        'active' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    
    foreach ($allProjects as $project) {
        switch ($project->getStatus()) {
            case 'draft': $stats['draft']++; break;
            case 'active': $stats['active']++; break;
            case 'completed': $stats['completed']++; break;
            case 'cancelled': $stats['cancelled']++; break;
        }
    }
    
    return $this->render('admin/project/index.html.twig', [
        'projects' => $projects,
        'current_status' => $status,
        'search_query' => $search,
        'stats' => $stats,
    ]);
}

    #[Route('/new', name: 'admin_projects_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreatedAt(new \DateTime());
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet créé avec succès.');

            return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/form.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'form_title' => 'Créer un nouveau projet'
        ]);
    }

    #[Route('/{id}', name: 'admin_projects_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('admin/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_projects_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Projet modifié avec succès.');

            return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/form.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'form_title' => 'Modifier le projet'
        ]);
    }

    #[Route('/{id}', name: 'admin_projects_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
            $this->addFlash('success', 'Projet supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/export/excel', name: 'admin_projects_export_excel', methods: ['GET'])]
    public function exportExcel(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $headers = ['ID', 'Titre', 'Description', 'Statut', 'Difficulté', 'Date Début', 'Date Fin', 'Créé le'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style des en-têtes
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Données
        $row = 2;
        foreach ($projects as $project) {
            $sheet->setCellValue('A' . $row, $project->getId());
            $sheet->setCellValue('B' . $row, $project->getTitle());
            $sheet->setCellValue('C' . $row, strip_tags((string) $project->getDescription()));
            $sheet->setCellValue('D' . $row, $project->getStatus());
            $sheet->setCellValue('E' . $row, $project->getDifficulty() ?? 'Non spécifiée');
            $sheet->setCellValue('F' . $row, $project->getStartDate() ? $project->getStartDate()->format('d/m/Y') : '');
            $sheet->setCellValue('G' . $row, $project->getEndDate() ? $project->getEndDate()->format('d/m/Y') : '');
            $sheet->setCellValue('H' . $row, $project->getCreatedAt() ? $project->getCreatedAt()->format('d/m/Y H:i') : '');
            $row++;
        }

        // Auto-size colonnes
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'projets_innolearn_' . date('Y-m-d') . '.xlsx';

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route('/export/word', name: 'admin_projects_export_word', methods: ['GET'])]
    public function exportWord(ProjectRepository $projectRepository): Response
    {
        // Utiliser PclZip car l'extension zip peut être manquante
        Settings::setZipClass(Settings::PCLZIP);
        
        $projects = $projectRepository->findAll();
        $phpWord = new PhpWord();

        // Style de police par défaut
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        // Section de titre
        $section = $phpWord->addSection();
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 24, 'color' => '1E293B'], ['alignment' => Jc::CENTER, 'spaceAfter' => 240]);
        $section->addTitle('Rapport des Projets InnoLearn', 1);
        $section->addText('Généré le : ' . date('d/m/Y H:i'), ['italic' => true], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(2);

        // Tableau
        $tableStyle = ['borderSize' => 6, 'borderColor' => 'E2E8F0', 'cellMargin' => 80];
        $firstRowStyle = ['bgColor' => 'F1F5F9', 'bold' => true];
        $phpWord->addTableStyle('ProjectTable', $tableStyle, $firstRowStyle);
        $table = $section->addTable('ProjectTable');

        // En-têtes du tableau
        $table->addRow();
        $table->addCell(500)->addText('ID', ['bold' => true]);
        $table->addCell(2500)->addText('Titre', ['bold' => true]);
        $table->addCell(1500)->addText('Statut', ['bold' => true]);
        $table->addCell(1500)->addText('Difficulté', ['bold' => true]);
        $table->addCell(2000)->addText('Date Début', ['bold' => true]);

        // Données
        foreach ($projects as $project) {
            $table->addRow();
            $table->addCell(500)->addText((string)$project->getId());
            $table->addCell(2500)->addText((string) $project->getTitle());
            $table->addCell(1500)->addText($project->getStatus());
            $table->addCell(1500)->addText($project->getDifficulty() ?? 'N/A');
            $table->addCell(2000)->addText($project->getStartDate() ? $project->getStartDate()->format('d/m/Y') : '');
        }

        $fileName = 'projets_innolearn_' . date('Y-m-d') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    #[Route('/import/excel', name: 'admin_projects_import_excel', methods: ['POST'])]
    public function importExcel(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('import_file');

        if (!$file) {
            $this->addFlash('error', 'Veuillez sélectionner un fichier Excel.');
            return $this->redirectToRoute('admin_projects');
        }

        if ($file->getClientOriginalExtension() !== 'xlsx') {
            $this->addFlash('error', 'Format de fichier invalide. Veuillez utiliser un fichier .xlsx');
            return $this->redirectToRoute('admin_projects');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // On ignore la première ligne (en-têtes)
            array_shift($rows);
            
            $count = 0;
            foreach ($rows as $rowData) {
                if (empty($rowData[0])) continue; // Titre vide

                $project = new Project();
                $project->setTitle($rowData[0]);
                $project->setDescription($rowData[1] ?? 'Imported description');
                $project->setStatus($rowData[2] ?? 'draft');
                $project->setDifficulty($rowData[3] ?? 'Débutant');
                
                // Gestion des dates (A: Titre, B: Desc, C: Status, D: Diff, E: Start, F: End)
                $startDate = !empty($rowData[4]) ? new \DateTime($rowData[4]) : new \DateTime();
                $project->setStartDate($startDate);
                
                if (!empty($rowData[5])) {
                    $project->setEndDate(new \DateTime($rowData[5]));
                }

                $project->setCreatedAt(new \DateTime());
                $entityManager->persist($project);
                $count++;
            }

            if ($count > 0) {
                $entityManager->flush();
                $this->addFlash('success', "$count projets ont été importés avec succès !");
            } else {
                $this->addFlash('warning', "Aucun projet valide n'a été trouvé dans le fichier.");
            }

        } catch (\Exception $e) {
            $this->addFlash('error', "Erreur lors de l'import : " . $e->getMessage());
        }

        return $this->redirectToRoute('admin_projects');
    }
}