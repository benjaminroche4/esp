<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

final class PublicRoutesTest extends AbstractWebTestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function publicGetRoutes(): iterable
    {
        yield 'home' => ['/'];
        yield 'service' => ['/nos-interventions'];
        yield 'agency Geneva' => ['/agence/geneve'];
        yield 'contact form' => ['/contact'];
        yield 'blog list' => ['/blog'];
        yield 'sitemap (html)' => ['/sitemap'];
        yield 'sitemap (xml)' => ['/sitemap.xml'];
        yield 'legal notice' => ['/mentions-legales'];
        yield 'personal data' => ['/donnees-personnelles'];
        yield 'login' => ['/connexion'];
    }

    #[DataProvider('publicGetRoutes')]
    public function testGetRouteReturnsSuccess(string $path): void
    {
        $this->client->request('GET', $path);

        self::assertResponseIsSuccessful(\sprintf('GET %s should return 2xx', $path));
    }

    public function testHomePageHasExpectedContent(): void
    {
        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('title', 'ESP');
    }

    public function testCspHeaderIsPresent(): void
    {
        $this->client->request('GET', '/');

        self::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        self::assertNotEmpty(
            $this->client->getResponse()->headers->get('Content-Security-Policy'),
            'CSP header must be set on public pages',
        );
    }
}
