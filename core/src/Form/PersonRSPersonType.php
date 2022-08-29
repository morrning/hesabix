<?php

namespace App\Form;

use App\Entity\PersonRSPerson;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonRSPersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            'data_class' => PersonRSPerson::class,
            'bid' => null
        ]);
    }
}
