<?php


namespace App\Service;
use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileMGR
{
    private $em;

    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    function __construct(EntityMGR  $entityManager)
    {
        $this->em = $entityManager;
    }

    public function upload(UploadedFile $file,$public=true,$users=[]){
        $guid = $this->RandomString(8);
        $tempFileName = $file->getClientOriginalName() . $guid . '.' . $file->getClientOriginalExtension();
        $file->move(str_replace('src','media',dirname(__DIR__)) . '/',$tempFileName );
        $fileEntity = new File();
        $fileEntity->setFileName($tempFileName);
        $fileEntity->setExtention($file->getClientOriginalExtension());
        $fileEntity->setPublic($public);
        $this->em->insertEntity($fileEntity);
        return $tempFileName;

    }
}