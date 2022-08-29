<?php

namespace App\Form;

use App\Entity\PersonRSOther;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonRSOtherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonRSOther::class,
            'bid'=>null
        ]);
    }
}
