<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/admin/list/user', name: 'app_admin_list_user')]
    public function list(Request $request, ManagerRegistry $doctrine): Response
    {
        $recordPerPage = 10;
        $em = $doctrine->getManager();
        $query = $em->createQuery("SELECT
            u.id,
            CONCAT(u.firstName, ' ', u.lastName) AS fullName,
            u.email,
            u.cellphone,
            u.gender,
            u.status,
            u.roles
            FROM App:User u
        ");
        $query->setFirstResult(0);
        $query->setMaxResults($recordPerPage);
        $users = $query->getResult();

        return $this->render('admin/user/list.html.twig', [
            'title' => 'List User',
            'users' => $users,
            'recordPerPage' => $recordPerPage,
        ]);
    }

    #[Route('/admin/manage/user/{id}', name: 'app_admin_manage_user')]
    public function manage(int $id = -1, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            $user = new User();
        }

        $em = $doctrine->getManager();
        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('app_admin_manage_user', ['id' => $id]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $email = $form['email']->getData();
                $cellphone = $form['cellphone']->getData();
                $profilePictureFile = $form->get('profilePicture')->getData();
                $password = $form->get('password')->getData();

                if(!$user->getUsername()) {
                    if ($email) {
                        $user->setUsername($email);
                    } else {
                        $user->setUsername($cellphone);
                    }
                }

                // handel profile pic
                if ($profilePictureFile) {

                    $profilePath = $this->getParameter('kernel.project_dir') . $this->getParameter('PROFILE_PICTURE_DIRECTORY');
                    if (!is_dir($profilePath)) {
                        mkdir($profilePath, 0777, true);
                    }
                    $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                    try {
                        $profilePictureFile->move(
                            $profilePath,
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $user->setProfilePicture($newFilename);
                }

                if ($password) {
                    $user->setPassword($this->passwordEncoder->hashPassword($user, $password));
                } elseif (!$user->getId()) { // time of ass user if password is blank default pass is cellphone
                    $user->setPassword($this->passwordEncoder->hashPassword($user, $cellphone));
                }

                $em->persist($user);
                $em->flush();
                $this->addFlash('successMessage', 'User saved!');
                return $this->redirect($request->headers->get('referer'));
            } else {
                $errors = $form->getErrors(true);

                foreach ($errors as $error) {
                    if ($error instanceof FormError) {
                        $this->addFlash('errorMessage', $error->getMessage());
                        break;
                    }
                }
            }
        }

        return $this->render('admin/user/manage.html.twig', [
            'title' => $user->getId() ? 'Edit User' : 'Add User',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/delete/user/{user}', name: 'app_admin_delete_user')]
    public function delete(User $user, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user->setStatus('Deleted');
        $em->persist($user);
        $em->flush();
        
        $this->addFlash('successMessage', 'User Deleted!');
        return $this->redirect($this->generateUrl('app_admin_list_user'));
    }

    #[Route('/admin/active/user/{user}', name: 'app_admin_active_user')]
    public function active(User $user, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user->setStatus('Active');
        $em->persist($user);
        $em->flush();
        
        $this->addFlash('successMessage', 'User Activated!');
        return $this->redirect($this->generateUrl('app_admin_list_user'));
    }

    #[Route('/admin/inactive/user/{user}', name: 'app_admin_inactive_user')]
    public function inactive(User $user, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user->setStatus('Inactive');
        $em->persist($user);
        $em->flush();
        
        $this->addFlash('successMessage', 'User Inactivated!');
        return $this->redirect($this->generateUrl('app_admin_list_user'));
    }
}
