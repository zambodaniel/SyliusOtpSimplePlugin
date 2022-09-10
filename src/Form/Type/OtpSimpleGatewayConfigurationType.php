<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class OtpSimpleGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('merchant_id', TextType::class, [
            'required' => true
        ]);
        $builder->add('secret_key', TextType::class, [
            'required' => true
        ]);
        $builder->add('auto_challenge', ChoiceType::class, [
            'required' => true,
            'choices' => [
                'sylius.ui.yes_label' => 1,
                'sylius.ui.no_label' => 0,
            ]
        ]);
        $builder->add('log_enabled', ChoiceType::class, [
            'required' => true,
            'choices' => [
                'sylius.ui.yes_label' => 1,
                'sylius.ui.no_label' => 0,
            ]
        ]);
        $builder->add('sandbox', ChoiceType::class, [
            'required' => true,
            'choices' => [
                'sylius.ui.yes_label' => 1,
                'sylius.ui.no_label' => 0,
            ]
        ]);
    }
}
