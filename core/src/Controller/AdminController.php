<?php

namespace App\Controller;

use App\Entity\Year;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class AdminController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('admin/sync/database', name: 'app_admin_sync_database')]
    public function app_admin_sync_database(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:schema:update',
            // (optional) define the value of command arguments
            '--force' => true,
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);
    }

    #[Route('admin/sync/rsfile', name: 'app_admin_sync_rsfile')]
    public function app_admin_sync_rsfile(KernelInterface $kernel,EntityManagerInterface $entityManager): Response
    {
       $files = $entityManager->getRepository('App:PersonRSFile')->findAll();
       foreach ($files as $file){
           $bid = $file->getBid();
           $year = $entityManager->getRepository(\App\Entity\Year::class)->findOneBy(['active'=>true,'bid'=>$bid]);
           if(!$year){
               $year = new Year();
               $year->setActive(true);
               $year->setBid($bid);
               $year->setStart(time());
               $year->setEnd(time() + 31536000);// 31536000 = 1 year
               $year->setName('سال مالی اول');
               $entityManager->persist($year);
               $entityManager->flush();
           }
           $file->setYear($year);
           $entityManager->persist($file);
           $entityManager->flush();
       }

        $transfers = $entityManager->getRepository('App:BanksTransfer')->findAll();
        foreach ($transfers as $transfer){
            $bid = $transfer->getBid();
            $year = $entityManager->getRepository(\App\Entity\Year::class)->findOneBy(['active'=>true,'bid'=>$bid]);
            if(!$year){
                $year = new Year();
                $year->setActive(true);
                $year->setBid($bid);
                $year->setStart(time());
                $year->setEnd(time() + 31536000);// 31536000 = 1 year
                $year->setName('سال مالی اول');
                $entityManager->persist($year);
                $entityManager->flush();
            }
            $transfer->setYear($year);
            $entityManager->persist($transfer);
            $entityManager->flush();
        }
        return new Response("OK");
    }
}
