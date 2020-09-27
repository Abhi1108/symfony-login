<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registration", name="registration.")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder(null, ["attr" => [
            "class" => "container"
        ]])
            ->add("name")
            ->add("email")
            ->add("password", RepeatedType::class, [
                "type" => PasswordType::class,
                "required" => true,
                "first_options" => ["label" => "Password"],
                "second_options" => ["label" => "Confirm Password"]
            ])
            ->add("phone", NumberType::class)
            ->add("address", TextareaType::class)
            ->add("state")
            ->add("city")
            ->add('save', SubmitType::class, ['label' => 'Create User', 'attr' => [
                "class" => "red lighten-1"
            ]])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $user = new User();

            $user->setName($data["name"]);
            $user->setEmail($data["email"]);
            $user->setPassword($data["password"]);
            $user->setPhone($data["phone"]);
            $user->setAddress($data["address"]);
            $user->setState($data["state"]);
            $user->setCity($data["city"]);
            $user->setIsAdmin(false);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl("login.index"));
        }

        return $this->render('registration/index.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
