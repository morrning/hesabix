<?php

namespace App\Form;

use App\Entity\HbuyPay;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HbuyPayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount',IntegerType::class)
            ->add('idpay',NumberType::class)
            ->add('datePay',TextType::class,['attr'=>[
                'class'=>'tarikh',
                'data-sb-validations'=>'required',
                'id'=>'date'
            ]])
            ->add('bank', EntityType::class, [
                'class' => \App\Entity\BanksAccount::class,
                'choice_label' => 'name',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.bussiness = :bid')
                        ->setParameter('bid',$options['bid']);
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HbuyPay::class,
            'bid'=>null
        ]);
    }
}
