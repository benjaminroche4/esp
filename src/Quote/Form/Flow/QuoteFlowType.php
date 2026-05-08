<?php

declare(strict_types=1);

namespace App\Quote\Form\Flow;

use App\Quote\Entity\QuoteRequest;
use Symfony\Component\Form\Flow\AbstractFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\Form\Flow\Type\NavigatorFlowType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class QuoteFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder->addStep(QuoteServiceStepType::GROUP, QuoteServiceStepType::class);
        $builder->addStep(QuoteContactStepType::GROUP, QuoteContactStepType::class);

        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteRequest::class,
            'step_property_path' => 'currentStep',
        ]);
    }
}
