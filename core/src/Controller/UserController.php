<?php

namespace App\Controller;

use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route('/login/{msg}', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, $msg = 'nothing'): Response
    {
        if($this->getUser())
            return $this->redirectToRoute('home');
        if(!empty($_POST['arcaptcha-token']))
        {
            $secret = '4z0c9dyi5zda8gd4jbq5';
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if($responseData->success){
            }
            else{
                $error = null;
            }
        }
        $error = $authenticationUtils->getLastAuthenticationError();

        // get the login error if there is one
        //$error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'msg'=>$msg
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/app/user/profile', name: 'app_user_profile')]
    public function app_user_profile(Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserEditType::class,$this->getUser(),[
            'action'=>$this->generateUrl('app_user_profile'),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($this->getUser());
            $entityManager->flush();
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success',
                'reload'=> 1
            ];
            return $this->json($response);
        }
        return $this->render('user/profile.html.twig', [
            'form'=>$form->createView(),
            'business'=>$entityManager->getRepository('App:Business')->findBy(['owner'=>$this->getUser()])
        ]);
    }
}
