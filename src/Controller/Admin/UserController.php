<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin/add/user', name: 'app_admin_add_user')]
    public function index(): Response
    {
        return $this->render('admin/user/add.html.twig', [
            'title' => 'Add User',
        ]);
    }
}
