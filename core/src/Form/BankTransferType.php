<?php

namespace App\Form;

use App\Entity\BanksTransfer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankTransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSave',TextType::class,['label'=>'تاریخ تراکنش','attr'=>[
                'class'=>'tarikh',
                'data-sb-validations'=>'required',
                'id'=>'date'
            ]])
            ->add('sideOne', EntityType::class, [
                'class' => \App\Entity\BanksAccount::class,
                'choice_label' => 'name',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.bussiness = :bid')
                        ->setParameter('bid',$options['bid']);
                },
                'label'=>'بانک مبدا',
                'attr'=>[
                    'class'=>'otherData'
                ]
            ])
            ->add('sideTwo', EntityType::class, [
                'class' => \App\Entity\BanksAccount::class,
                'choice_label' => 'name',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.bussiness = :bid')
                        ->setParameter('bid',$options['bid']);
                },
                'label'=>'بانک مقصد',
                'attr'=>[
                    'class'=>'otherData'
                ]
            ])
            ->add('amount',IntegerType::class,['label'=>'مقدار'])
            ->add('TransactionNum',TextType::class,['required'=>false,'label'=>'شماره پیگیری بانک'])
            ->add('des',TextType::class,['required'=>false, 'label'=>'توضیحات'])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BanksTransfer::class,
            'bid'=>null
        ]);
    }
}
