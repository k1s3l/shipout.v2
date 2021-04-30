<?php

namespace App\Form;

use App\Entity\Factory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryType extends AbstractType
{
    const IS_ACTIVE = 0;
    const IS_SOLD_OUT = 1;
    const IS_DEMOLITION = 2;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Address',
                ]])
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Name',
            ]])
            ->add('state', ChoiceType::class, [
                'label' => false,
                'choices' => $this->stateChoices(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Factory::class,
        ]);
    }

    public static function stateChoices(): array
    {
        return [
            'Продается' => self::IS_ACTIVE,
            'Продано'   => self::IS_SOLD_OUT,
            'Под снос'  => self::IS_DEMOLITION,
        ];
    }
}
