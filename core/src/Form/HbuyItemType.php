<?php

namespace App\Form;

use App\Entity\HbuyItem;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HbuyItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('num',IntegerType::class,['label'=>''])
            ->add('des',TextType::class,['label'=>''])
            ->add('price',IntegerType::class,['label'=>''])
            ->add('off',IntegerType::class,['label'=>'','attr'=>['value'=>0]])
            ->add('commodity', EntityType::class, [
                'class' => \App\Entity\Commodity::class,
                'choice_label' => 'name',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.bid = :bid')
                        ->setParameter('bid',$options['bid']);
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HbuyItem::class,
            'bid'=> null
        ]);
    }
}
