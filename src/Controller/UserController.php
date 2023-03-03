<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\EditPasswordType;
use App\Form\EditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/users", name="users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profile/{id}", name="_profile")
     */
    public function showMyProfile(int $id, UserRepository $userRepository): Response
    {
        $userProfile = $userRepository->find($id);

        return $this->render('user/profile.html.twig', [
            "user" => $userProfile
        ]);
    }

    /**
     * @Route("/edit_profile/{id}", name="_editProfile")
     */
    public function editMyProfile(int $id,Request $request,EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $editForm = $this->createForm(EditType::class, $user);

        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            $user = $editForm->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated !');

            return $this->redirectToRoute('main_home');
        }

        return $this->render('user/edit_profile.html.twig', [
            'editForm' => $editForm->createView()
        ]);
    }

    /**
     * @Route("/edit_password/{id}", name="_editPassword")
     */
    public function editPassword(int $id,Request $request,EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $userRepository->find($id);
        $editForm = $this->createForm(EditPasswordType::class, $user);

        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $editForm->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated !');

            return $this->redirectToRoute('main_home');
        }

        return $this->render('user/edit_password.html.twig', [
            'editForm' => $editForm->createView()
        ]);
    }

    public function editTrip(Trip $trip, Request $request)
    {
        $this-> denyAccessUnlessGranted('POST_EDIT', $trip);
    }
}