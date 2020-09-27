<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/login", name="login")
 */
class LoginController extends AbstractController
{
    /**
     * @Route("/", name=".index")
     */
    public function index(UserRepository $userRepository, Request $request, SessionInterface $session)
    {
        $form = $this->createFormBuilder(null, ["attr" => [
            "class" => "container"
        ]])
            ->add("email")
            ->add("password", PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Login', 'attr' => [
                "class" => "red lighten-1"
            ]])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $userRepository->findOneBy(
                [
                    'email' => $data["email"],
                    'password' => $data["password"]
                ]
            );

            if (count($user) == 0) {
                $this->addFlash("error", "No User Found!");
                return $this->redirect($this->generateUrl("login.index"));
            } else {
                $session->set("user_id", $user->getId());
                return $this->redirect($this->generateUrl("home.index"));
            }
        } elseif ($form->isSubmitted()) {
            $this->addFlash("error", "Form not Valid!");
            return $this->redirect($this->generateUrl("login.index"));
        }

        return $this->render('login/index.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
