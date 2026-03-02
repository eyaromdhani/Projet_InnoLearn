<?php

namespace App\Controller\Admin;

use App\Repository\CoursRepository;
use App\Service\AIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/advanced-stats')]
class AdvancedStatsController extends AbstractController
{
    private $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    #[Route('/', name: 'app_admin_advanced_stats')]
    public function index(CoursRepository $coursRepository): Response
    {
        // 1. Core Data
        $allCourses = $coursRepository->findAll();
        $topViewed = $coursRepository->findBy([], ['viewsCount' => 'DESC'], 6);
        
        $statsData = [];
        $totalViews = 0;
        $totalCompletions = 0;

        foreach ($allCourses as $course) {
            $views = $course->getViewsCount() ?? 0;
            $completions = $course->getCompletionsCount() ?? 0;
            $totalViews += $views;
            $totalCompletions += $completions;
            
            $statsData[] = [
                'course' => $course,
                'views' => $views,
                'completions' => $completions,
                'dropoutRate' => $views > 0 ? round((($views - $completions) / $views) * 100, 1) : 0,
                'successRate' => $views > 0 ? round(($completions / $views) * 100, 1) : 0
            ];
        }

        // 2. Specialized Lists
        usort($statsData, fn($a, $b) => $b['dropoutRate'] <=> $a['dropoutRate']);
        $highDropout = array_slice($statsData, 0, 5);

        // 3. AI Global Insight
        $aiAnalysis = null;
        if (!empty($statsData)) {
            // Prepare a summary for AI
            $summary = "Top Viewed: " . implode(', ', array_map(fn($c) => $c->getNom(), $topViewed)) . ". ";
            $summary .= "Total Platform Views: $totalViews. Avg Success: " . ($totalViews > 0 ? round(($totalCompletions/$totalViews)*100, 1) : 0) . "%.";
            
            try {
                $aiAnalysis = $this->aiService->getPedagogicalAnalysis("Global Platform Overview", [
                    'pass' => $totalCompletions,
                    'fail' => $totalViews - $totalCompletions
                ], count($allCourses));
            } catch (\Exception $e) {
                $aiAnalysis = "L'IA analyse vos données... revenez dans quelques instants.";
            }
        }

        return $this->render('admin/advanced_stats/index.html.twig', [
            'totalViews' => $totalViews,
            'totalCompletions' => $totalCompletions,
            'topViewed' => $topViewed,
            'highDropout' => $highDropout,
            'aiAnalysis' => $aiAnalysis,
            'allStats' => $statsData
        ]);
    }
}
