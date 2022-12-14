<?php

namespace App\Form;

use App\Entity\PersonRSFile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonRSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSave',TextType::class,['attr'=>[
                'class'=>'tarikh',
                'data-sb-validations'=>'required',
                'id'=>'date'
            ]])
            ->add('RS',ChoiceType::class,[
                'multiple'=>false,
                'expanded'=>true,
                'choices'  => [
                    'دریافت از شخص' => true,
                    'پرداخت به شخص' => false,
                ],
                'data'=>true,
                'attr'=>[
                    'class'=>'float-start'
                ]
                ])
            ->add('des',TextType::class)
            ->add('amount',TextType::class)
            ->add('person', EntityType::class, [
                'class' => \App\Entity\Person::class,
                'choice_label' => 'nameandfamily',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonRSFile::class,
        ]);
    }
}
