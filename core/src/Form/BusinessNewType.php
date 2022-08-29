<?php

namespace App\Form;

use App\Entity\Business;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('legalName')
            ->add('arzMain',EntityType::class,[
                'class'=> \App\Entity\ArzType::class,
                'choice_label'=>'name'
            ])
            ->add('keshvar')
            ->add('ostan')
            ->add('shahr')
            ->add('address')
            ->add('codeposti')
            ->add('fax')
            ->add('tel')
            ->add('website',UrlType::class,[
                'required'=>false
            ])
            ->add('shenasemeli')
            ->add('codeeghtesadi')
            ->add('shomaresabt')
            ->add('maliyatafzode',NumberType::class,['data'=>0])
            ->add('email')
            ->add('field')
            ->add('type',ChoiceType::class,[
                'choices'  => [
                    'شرکت' => 'شرکت',
                    'مغازه' => 'مغازه',
                    'فروشگاه' => 'فروشگاه',
                    'اتحادیه' => 'اتحادیه',
                    'باشگاه' => 'باشگاه',
                    'موسسه' => 'موسسه',
                    'شخصی' => 'شخصی'
                ],
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Business::class,
            'attr'=>['class'=>'frmAjaxSend']
        ]);
    }
}
