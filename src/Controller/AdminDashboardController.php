<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function index(StatsService $statsService)
    {
        $stats = $statsService->getStats();

        $statsTables = $statsService->getStatsTables();
              
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'statsTables' => $statsTables,
        ]);
    }
} 
