<?php

namespace App\Form;

use App\Entity\HesabdariItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HesabdariItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bs',IntegerType::class,[
                'attr'=>['min'=>0,'onkeyup'=>'reloadData();',
                    'data-inputmask' => '\'alias\': \'currency\'']
            ])
            ->add('bd',IntegerType::class,[
                'attr'=>['min'=>0,'onkeyup'=>'reloadData();']
            ])
            ->add('type',TextType::class)
            ->add('des',TextType::class)
            ->add('code', EntityType::class, [
                'class' => \App\Entity\HesabdariTable::class,
                'choice_label' => 'name',
                'choice_value'=> 'code',
                'group_by' => function($choice, $key, $value) {
                    if ($choice->getCode() < 11000 )
                        return 'دارایی جاری';
                    elseif ($choice->getCode() < 12000 )
                        return 'دارایی غیرجاری';
                    elseif ($choice->getCode() < 13999 )
                        return 'بدهی‌های جاری';
                    elseif ($choice->getCode() < 14000 )
                        return 'بدهی‌های غیرجاری';
                    elseif ($choice->getCode() < 15000 )
                        return 'درآمد عملیاتی';
                    elseif ($choice->getCode() < 16000 )
                        return 'درآمد غیرعملیاتی';
                    elseif ($choice->getCode() < 17000 )
                        return 'هزینه‌های عملیاتی';
                    elseif ($choice->getCode() < 18000 )
                        return 'هزینه‌های غیرعملیاتی';
                    elseif ($choice->getCode() < 19000 )
                        return 'هزینه‌های پرسنلی';
                    elseif ($choice->getCode() < 20000 )
                        return 'هزینه‌های بازاریابی/توزیع/فروش';
                    elseif ($choice->getCode() < 21000 )
                        return 'فروش کالا و خدمات';
                    elseif ($choice->getCode() < 22000 )
                        return 'خرید کالا و خذمات';
                    return 'سایر';
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HesabdariItem::class,
        ]);
    }
}
