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

    #[Route('/admin/add/user', name: 'app_admin_add_user')]
    public function index(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $user = new User();
        $em = $doctrine->getManager();
        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('app_admin_add_user'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $email = $form['email']->getData();
                $cellphone = $form['cellphone']->getData();
                $profilePictureFile = $form->get('profilePicture')->getData();

                if ($email) {
                    $user->setUsername($email);
                } else {
                    $user->setUsername($cellphone);
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
                $user->setPassword($this->passwordEncoder->hashPassword($user, $cellphone));
                $em->persist($user);
                $em->flush();
                $this->addFlash('successMessage', 'User created!');
                return $this->redirect($request->headers->get('referer'));
            } else {
                $errors = $form->getErrors(true, false);

                foreach ($errors as $error) {
                    if ($error instanceof FormError) {
                        $this->addFlash('errorMessage', $error->getMessage());
                        break;
                    }
                }
            }
        }

        return $this->render('admin/user/add.html.twig', [
            'title' => 'Add User',
            'form' => $form->createView(),
        ]);
    }
}
