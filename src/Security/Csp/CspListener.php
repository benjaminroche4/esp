<?php

declare(strict_types=1);

namespace App\Security\Csp;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest', priority: 1024)]
#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onKernelResponse', priority: -100)]
final class CspListener
{
    public const string NONCE_ATTRIBUTE = '_csp_nonce';

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $event->getRequest()->attributes->set(self::NONCE_ATTRIBUTE, bin2hex(random_bytes(16)));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $contentType = (string) $response->headers->get('Content-Type', '');
        if ('' !== $contentType && !str_starts_with($contentType, 'text/html')) {
            return;
        }

        $nonce = $event->getRequest()->attributes->get(self::NONCE_ATTRIBUTE);
        $this->applyHeaders($response, (string) $nonce);
    }

    private function applyHeaders(Response $response, string $nonce): void
    {
        // "Marketing-friendly" CSP: nonce-based protection against XSS on inline scripts,
        // but tolerant of GTM-loaded third-party tools (chat, reviews, retargeting pixels…).
        // `'strict-dynamic'` lets nonced scripts (GTM) load further scripts without listing every domain;
        // `https:` is the fallback for browsers ignoring `'strict-dynamic'`.
        $csp = sprintf(
            "default-src 'self'; "
            ."script-src 'self' 'nonce-%1\$s' 'strict-dynamic' https: 'unsafe-inline'; "
            ."style-src 'self' 'unsafe-inline' https:; "
            ."style-src-attr 'unsafe-inline'; "
            ."font-src 'self' data: https:; "
            ."img-src 'self' data: https:; "
            ."media-src 'self' https:; "
            ."connect-src 'self' https:; "
            ."frame-src https:; "
            ."object-src 'none'; "
            ."base-uri 'self'; "
            ."form-action 'self'; "
            ."frame-ancestors 'none'; "
            .'upgrade-insecure-requests',
            $nonce,
        );

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }
}
