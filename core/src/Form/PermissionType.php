<?php

namespace App\Form;

use App\Entity\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('admin', CheckboxType::class, [
                'label'=>'کنترل دسترسی و تنظیمات'
                ,'required'=>false
            ])
            ->add('personAdd', CheckboxType::class, [
                'label'=>'افزودن'
                ,'required'=>false
            ])
            ->add('personEdit', CheckboxType::class, [
                'label'=>'ویرایش'
                ,'required'=>false
            ])
            ->add('personDelete', CheckboxType::class, [
                'label'=>'حذف'
                ,'required'=>false
            ])
            ->add('personPrint', CheckboxType::class, [
                'label'=>'چاپ لیست'
                ,'required'=>false
            ])
            ->add('personRSAdd', CheckboxType::class, [
                'label'=>'افزودن'
                ,'required'=>false
            ])
            ->add('personRSDelete', CheckboxType::class, [
                'label'=>'حذف'
                ,'required'=>false
            ])
            ->add('personRSPrint', CheckboxType::class, [
                'label'=>'چاپ'
                ,'required'=>false
            ])
            ->add('commodityAdd', CheckboxType::class, [
                'label'=>'افزودن'
                ,'required'=>false
            ])
            ->add('commodityEdit', CheckboxType::class, [
                'label'=>'ویرایش'
                ,'required'=>false
            ])
            ->add('commodityDelete', CheckboxType::class, [
                'label'=>'حذف'
                ,'required'=>false
            ])
            ->add('bankAdd', CheckboxType::class, [
                'label'=>'افزودن بانک'
                ,'required'=>false
            ])
            ->add('bankEdit', CheckboxType::class, [
                'label'=>'ویرایش بانک'
                ,'required'=>false
            ])
            ->add('bankDelete', CheckboxType::class, [
                'label'=>'حذف بانک'
                ,'required'=>false
            ])
            ->add('bankTransferAdd', CheckboxType::class, [
                'label'=>'افزودن انتقال'
                ,'required'=>false
            ])
            ->add('bankTransferEdit', CheckboxType::class, [
                'label'=>'ویرایش  انتقال'
                ,'required'=>false
            ])
            ->add('bankTransferDelete', CheckboxType::class, [
                'label'=>'حذف انتقال'
                ,'required'=>false
            ])
            ->add('incomeAdd', CheckboxType::class, [
                'label'=>'افزودن'
                ,'required'=>false
            ])
            ->add('incomeEdit', CheckboxType::class, [
                'label'=>'ویرایش'
                ,'required'=>false
            ])
            ->add('incomeDelete', CheckboxType::class, [
                'label'=>'حذف'
                ,'required'=>false
            ])
            ->add('incomePrint', CheckboxType::class, [
                'label'=>'چاپ لیست'
                ,'required'=>false
            ])
            ->add('castAdd', CheckboxType::class, [
                'label'=>'افزودن'
                ,'required'=>false
            ])
            ->add('castEdit', CheckboxType::class, [
                'label'=>'ویرایش'
                ,'required'=>false
            ])
            ->add('castDelete', CheckboxType::class, [
                'label'=>'حذف'
                ,'required'=>false
            ])
            ->add('castPrint', CheckboxType::class, [
                'label'=>'چاپ لیست'
                ,'required'=>false
            ])
            ->add('storeAdd', CheckboxType::class, [
                'label'=>'افزودن'
                ,'required'=>false
            ])
            ->add('storeEdit', CheckboxType::class, [
                'label'=>'ویرایش'
                ,'required'=>false
            ])
            ->add('storeDelete', CheckboxType::class, [
                'label'=>'حذف'
                ,'required'=>false
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
        ]);
    }
}
