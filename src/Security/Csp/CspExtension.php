<?php

declare(strict_types=1);

namespace App\Security\Csp;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CspExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csp_nonce', $this->cspNonce(...)),
        ];
    }

    public function cspNonce(): string
    {
        $request = $this->requestStack->getMainRequest();

        return (string) ($request?->attributes->get(CspListener::NONCE_ATTRIBUTE) ?? '');
    }
}
