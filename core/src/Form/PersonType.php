<?php

namespace App\Form;

use App\Entity\Person;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameandfamily',TextType::class,['required'=>false])
            ->add('company',TextType::class,['required'=>false])
            ->add('nikeName',TextType::class)
            ->add('tel',TelType::class,['required'=>false])
            ->add('mobile',TelType::class,['required'=>false])
            ->add('country',TextType::class,['required'=>false])
            ->add('ostan',TextType::class,['required'=>false])
            ->add('city',TextType::class,['required'=>false])
            ->add('address',TextType::class,['required'=>false])
            ->add('postalcode',TextType::class,['required'=>false])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
