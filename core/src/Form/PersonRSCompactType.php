<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonRSCompactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSave',TextType::class,['attr'=>[
                'class'=>'tarikh',
                'data-sb-validations'=>'required',
                'id'=>'date'
            ]])
            ->add('person', EntityType::class, [
                'class' => \App\Entity\Person::class,
                'choice_label' => 'nikename',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.bid = :bid')
                        ->setParameter('bid',$options['bid']);
                },
                'attr'=>[
                    'class'=>'person'
                ]
            ])
            ->add('amount',IntegerType::class,['attr'=>['min'=>0]])
            ->add('des',TextType::class)
            ->add('type',ChoiceType::class,[
                'multiple'=>false,
                'expanded'=>false,
                'choices'  => [
                    'بانک' =>  1002,
                ],
                'data'=>1002,
                'label'=>'طرف حساب'
            ])
            ->add('data', EntityType::class, [
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
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'mapped'=>false,
            'bid'=>null
        ]);
    }
}
