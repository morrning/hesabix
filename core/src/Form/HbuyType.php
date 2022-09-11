<?php

namespace App\Form;

use App\Entity\Hbuy;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HbuyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateBuy',TextType::class,['label'=>'تاریخ تراکنش','attr'=>[
                'class'=>'tarikh',
                'data-sb-validations'=>'required',
                'id'=>'date'
            ]])
            ->add('title',TextType::class,['label'=>'عنوان'])
            ->add('des',TextType::class,['label'=>'توضیحات'])
            ->add('tax',IntegerType::class,['label'=>'مالیات','data'=>0,'attr'=>['min'=>0]])
            ->add('supplier', EntityType::class, [
                'class' => \App\Entity\Person::class,
                'choice_label' => 'nikeName',
                'choice_value'=> 'id',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.bid = :bid')
                        ->setParameter('bid',$options['bid']);
                },
                'label'=>'تامین کننده',
                'attr'=>[
                    'class'=>'otherData'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hbuy::class,
            'bid'=>null
        ]);
    }
}
