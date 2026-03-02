<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Cours;
use App\Repository\CoursRepository;
use App\Repository\ProgressionRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    private $coursRepository;
    private $progressionRepository;

    public function __construct(CoursRepository $coursRepository, ProgressionRepository $progressionRepository)
    {
        $this->coursRepository = $coursRepository;
        $this->progressionRepository = $progressionRepository;
    }

    public function getRecommendations(User $user): array
    {
        // 1. Get user's active progressions
        $progressions = $this->progressionRepository->findBy(['student' => $user]);

        $categoryInterest = []; // sum of activity points per category
        $skilledCategories = []; // Highest level completed/passed per category

        $levelOrder = ['Débutant' => 1, 'Intermédiaire' => 2, 'Avancé' => 3, 'Expert' => 4];
        $startedCourseIds = [];

        foreach ($progressions as $prog) {
            $course = $prog->getCours();
            if (!$course)
                continue;

            $startedCourseIds[] = $course->getId();
            $category = $course->getCategorieCours();
            if (!$category)
                continue;

            $catId = $category->getId();

            // Weighting interest: view = 1pt, quiz pass = 2pts
            $categoryInterest[$catId] = ($categoryInterest[$catId] ?? 0) + ($prog->isViewed() ? 1 : 0);

            $passRate = ($prog->getQuizTotalPoints() > 0) ? ($prog->getQuizScore() / $prog->getQuizTotalPoints()) : 0;
            if ($passRate >= 0.7) {
                $categoryInterest[$catId] += 2;
                $currentLevelVal = $levelOrder[$course->getNiveau()] ?? 0;
                $prevMaxLevelVal = $levelOrder[$skilledCategories[$catId] ?? ''] ?? 0;

                if ($currentLevelVal > $prevMaxLevelVal) {
                    $skilledCategories[$catId] = $course->getNiveau();
                }
            }
        }

        // 2. Build recommendations
        $recommendations = [];

        // Sort categories by interest
        arsort($categoryInterest);

        foreach ($categoryInterest as $catId => $points) {
            $maxLevel = $skilledCategories[$catId] ?? null;
            $nextLevel = null;

            if ($maxLevel) {
                // Find next level
                $currentLevelValue = $levelOrder[$maxLevel] ?? 0;
                foreach ($levelOrder as $l => $v) {
                    if ($v === $currentLevelValue + 1) {
                        $nextLevel = $l;
                        break;
                    }
                }
            } else {
                // Not skilled in anything yet? Suggest Debutant
                $nextLevel = 'Débutant';
            }

            // Fetch courses for this category and level
            $criteria = ['categorieCours' => $catId];
            if ($nextLevel) {
                $criteria['niveau'] = $nextLevel;
            }

            $suggestedCourses = $this->coursRepository->findBy($criteria, ['viewsCount' => 'DESC'], 3);
            foreach ($suggestedCourses as $course) {
                if (!in_array($course->getId(), $startedCourseIds) && !in_array($course, $recommendations)) {
                    $recommendations[] = $course;
                }
            }

            if (count($recommendations) >= 6)
                break;
        }

        // 3. Fallback: If no activity, suggest top viewed courses across all categories
        if (empty($recommendations)) {
            $recommendations = $this->coursRepository->findBy([], ['viewsCount' => 'DESC'], 6);
        }

        return array_slice($recommendations, 0, 6);
    }

    public function getPreferredCategories(User $user): array
    {
        $progressions = $this->progressionRepository->findBy(['student' => $user]);
        $counts = [];
        foreach ($progressions as $p) {
            $cat = $p->getCours()->getCategorieCours();
            if ($cat) {
                $id = $cat->getId();
                $counts[$id] = ($counts[$id] ?? 0) + 1;
            }
        }
        arsort($counts);
        return array_slice(array_keys($counts), 0, 3);
    }

}
