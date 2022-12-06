<?php

namespace App\Controller;

use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends AbstractController
{
    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32): string
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

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

    #[Route('/app/user/profile/{res}', name: 'app_user_profile')]
    public function app_user_profile($res = 'nothing',Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserEditType::class,$this->getUser(),[
            'action'=>$this->generateUrl('app_user_profile',['res'=>0]),
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
            'res'=>$res
        ]);
    }
    #[Route('/app/user/change-password', name: 'app_user_change_password')]
    public function app_user_change_password(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('password', PasswordType::class,[
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ])
                ],
                ])
            ->add('repassword', PasswordType::class)
            ->add('submit', SubmitType::class,['label'=>'تغییر کلمه عبور'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('password')->getData() == $form->get('repassword')->getData()){
                $user = $this->getUser();
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_user_profile',['res'=>'change-password']);
            }
            else{
                $form->get('repassword')->addError(new FormError('کلمات عبور وارد شده مطابقت ندارند.'));
            }

        }
        return $this->render('user/change-password.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/forget-password', name: 'app_user_forget_password')]
    public function app_user_forget_password(MailerInterface $mailer,Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', EmailType::class,[
                'mapped' => false,

            ])
            ->add('captcha', CaptchaType::class)
            ->add('submit', SubmitType::class,['label'=>'ارسال تاییدیه به پست الکترونیکی'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository('App:User')->findOneBy(['email'=>$form->get('email')->getData()]);
            if($user){
                $token = $this->RandomString(250);
                $user->setResetToken($token);
                $user->setResetValidTime(time() + 3600);
                $entityManager->persist($user);
                $entityManager->flush();
                // generate a signed url and email it to the user
                $email = (new Email())
                    ->from(new Address('noreplay@hesabix.ir', 'حسابیکس'))
                    ->to($user->getEmail())
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('فراموشی کلمه عبور در حسابیکس')
                    ->html($this->renderView('user/reset_password_email.html.twig',[
                        'url'=>$this->generateUrl('app_user_reset_password',['token'=>$token],UrlGeneratorInterface::ABSOLUTE_URL)
                    ]));

                $mailer->send($email);
            }
        }
        return $this->render('user/forget-password.html.twig', [
            'form'=>$form->createView(),
            'send'=>$form->isSubmitted() && $form->isValid()
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_user_reset_password')]
    public function app_user_reset_password($token,MailerInterface $mailer,Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $entityManager->getRepository('App:User')->findOneBy(['resetToken'=>$token]);
        if(!$user)
            throw $this->createNotFoundException();
        if($user->getResetValidTime() < time())
            throw $this->createNotFoundException();

        //show form for reset password
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('password', PasswordType::class,[
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ])
                ],
            ])
            ->add('repassword', PasswordType::class)
            ->add('submit', SubmitType::class,['label'=>'تغییر کلمه عبور'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('password')->getData() == $form->get('repassword')->getData()){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $user->setResetToken(null);
                $user->setResetValidTime(null);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('login',['msg'=>'2']);
            }
            else{
                $form->get('repassword')->addError(new FormError('کلمات عبور وارد شده مطابقت ندارند.'));
            }

        }
        return $this->render('user/set-password.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
}
