<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $query = $em->createQuery("SELECT count(u.id) FROM App\Entity\User u");
        $userCount = $query->getSingleScalarResult();
        return $this->render('admin/dashboard/dashboard.html.twig', [
            'title' => 'Admin Dashboard',
            'userCount' => $userCount,
        ]);
    }
}
