<?php

namespace App\Form;

use App\Entity\BanksAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class)
            ->add('shobe',TextType::class,['required'=>false])
            ->add('shomarehesab',TextType::class,['required'=>false])
            ->add('shaba',TextType::class,['required'=>false])
            ->add('shomarecart',TextType::class,['required'=>false])
            ->add('des',TextType::class,['required'=>false])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BanksAccount::class,
        ]);
    }
}
