<?php

namespace App\Form;

use App\Entity\IncomeFile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSave',TextType::class,['label'=>'تاریخ تراکنش','attr'=>[
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
                'label'=>'بانک',
                'attr'=>[
                    'class'=>'otherData'
                ]
            ])
            ->add('incomeTable', EntityType::class, [
                'class' => \App\Entity\HesabdariTable::class,
                'choice_label' => 'name',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.code <= 1299')
                        ->andWhere('u.code >= 1201');
                },
                'label'=>'مرکز درآمد',
                'attr'=>[
                    'class'=>'otherData'
                ]
            ])
            ->add('amount',IntegerType::class)
            ->add('des',TextType::class,['label'=>'شرح','required'=>false])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IncomeFile::class,
            'bid'=>null
        ]);
    }
}
