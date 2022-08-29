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
                'attr'=>['onkeyup'=>'reloadData();',
                    'data-inputmask' => '\'alias\': \'currency\'']
            ])
            ->add('bd',IntegerType::class,[
                'attr'=>['onkeyup'=>'reloadData();']
            ])
            ->add('type',TextType::class)
            ->add('des',TextType::class)
            ->add('code', EntityType::class, [
                'class' => \App\Entity\HesabdariTable::class,
                'choice_label' => 'name',
                'choice_value'=> 'code',
                'group_by' => function($choice, $key, $value) {
                    if ($choice->getCode() <= 1099 )
                        return 'دارایی جاری';
                    elseif ($choice->getCode() <= 1199 )
                        return 'دارایی غیرجاری';
                    elseif ($choice->getCode() <= 1299 )
                        return 'درآمدها';
                    elseif ($choice->getCode() <= 1399 )
                        return 'هزینه‌ها';
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
