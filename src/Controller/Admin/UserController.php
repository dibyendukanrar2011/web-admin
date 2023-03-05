<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin/add/user', name: 'app_admin_add_user')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $em = $doctrine->getManager();
        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('app_admin_add_user'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if($form->isValid()) {
                $cellphone = $form['cellphone']->getData();
                $email = $form['email']->getData();
                if($email) {
                    $user->setUsername($email);
                } else {
                    $user->setUsername($cellphone);
                }
                $em->persist($user);
                $em->flush();
            } else {
                dd("a");
            }
        }

        return $this->render('admin/user/add.html.twig', [
            'title' => 'Add User',
            'form' => $form->createView(),
        ]);
    }
}
