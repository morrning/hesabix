<?php

namespace App\Controller;

use App\Entity\Pay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PayController extends AbstractController
{
    #[Route('/app/user/pay', name: 'app_pay')]
    public function app_pay(EntityManagerInterface $entityManager): Response
    {
        $data = array("merchant_id" => "a7804652-1fb9-4b43-911c-0a1046e61be1",
            "amount" => 250000,
            "callback_url" => "https://hesabix.ir/app/user/buy/verify",
            "description" => 'حذف تبلیغات به مدت یک سال',
        );
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {
                    $pay = new Pay();
                    $pay->setUser($this->getUser());
                    $pay->setDateSubmit(time());
                    $pay->setAmount(250000);
                    $pay->setVerifyCode($result['data']['authority']);
                    $pay->setStatus(0);
                    $entityManager->getRepository('App:Pay')->add($pay);
                    return $this->redirect('https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"]);
                }
            } else {
                return $this->render('shop/fail.html.twig',
                    [
                        'results'=>$result
                    ]);
            }
        }
    }

    #[Route('/app/user/buy/verify', name: 'app_user_buy_verify')]
    public function shop_buy_verify(Request $request,EntityManagerInterface $entityManager): Response
    {
        $Authority = $request->get('Authority');
        $status = $request->get('Status');
        $req = $entityManager->getRepository('App:Pay')->findOneBy(['verifyCode'=>$Authority]);
        $data = array("merchant_id" => "a7804652-1fb9-4b43-911c-0a1046e61be1", "authority" => $Authority, "amount" => $req->getAmount());
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        //-----------------------------------

        //-----------------------------------
        if ($err) {
            return $this->render('buy/fail.html.twig', ['results'=>$result]);
        } else {
            if(array_key_exists('code',$result['data'])){
                if ($result['data']['code'] == 100) {
                    $req->setStatus(100);
                    $req->setRefID($result['data']['ref_id']);
                    $req->setCardPan($result['data']['card_pan']);
                    $entityManager->persist($req);
                    $entityManager->flush();

                    //run commands on server
                    $user = $entityManager->getRepository('App:User')->find($this->getUser()->getId());
                    $user->setAdsBan(true);
                    $user->setAdsBanExpire(time() + 31536000 );
                    $entityManager->persist($user);
                    $entityManager->flush();
                    return $this->render('buy/success.html.twig',['req'=>$req]);
                }
            }
            return $this->render('buy/fail.html.twig', ['results'=>$result]);
        }
    }
    #[Route('/app/user/buy/history', name: 'app_user_buy_history')]
    public function shopHistory(EntityManagerInterface $entityManager): Response
    {
        $items = $entityManager->getRepository('App:Pay')->findBy(['user'=>$this->getUser()],['id'=>'DESC']);
        return $this->render('/buy/history.html.twig', [
            'items' => $items,

        ]);
    }
}
