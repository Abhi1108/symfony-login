<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/", name="home.")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserRepository $userRepository, Request $request, SessionInterface $session)
    {
        $user_id = $session->get("user_id");

        if ($user_id) {
            $user = $userRepository->findOneBy(
                [
                    'id' => $user_id
                ]
            );

            $userList = [];
            if ($user->getIsAdmin()) {
                $userList = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findBy(['is_admin' => false]);
            }

            $form = $this->createFormBuilder($user, ["attr" => [
                "class" => "container"
            ]])
                ->add("name")
                ->add("email")
                ->add("phone", NumberType::class)
                ->add("address", TextareaType::class)
                ->add("state")
                ->add("city")
                ->add('update', SubmitType::class, ['label' => 'Update Details', 'attr' => [
                    "class" => "red lighten-1"
                ]])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() &&  $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Details Updated'
                );
                return $this->redirectToRoute('home.index');
            }

            return $this->render('home/index.html.twig', [
                "form" => $form->createView(),
                "userList" => $userList
            ]);
        } else {
            return $this->redirect($this->generateUrl("login.index"));
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(SessionInterface $session)
    {
        $session->clear();
        return $this->redirectToRoute('login.index');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'User Removed'
        );
        return $this->redirectToRoute('home.index');
    }
}
