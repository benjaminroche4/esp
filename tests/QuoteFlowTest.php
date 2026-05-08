<?php

declare(strict_types=1);

namespace App\Tests;

use App\Quote\Entity\QuoteRequest;
use Doctrine\ORM\EntityManagerInterface;

final class QuoteFlowTest extends AbstractWebTestCase
{
    public function testHomeRendersHeroWithQuoteFlow(): void
    {
        $crawler = $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Devis gratuit');
        self::assertSelectorExists('form[action="/devis"]');
        self::assertSelectorExists('input[name="quote_flow[service][serviceType]"]');
    }

    public function testTwoStepSubmissionPersistsAndSendsEmails(): void
    {
        $this->client->enableProfiler();

        // GET / → step 1 form
        $crawler = $this->client->request('GET', '/');
        self::assertResponseIsSuccessful();

        // Submit step 1 (service)
        $form = $crawler->filter('form[action="/devis"]')->form();
        $form['quote_flow[service][serviceType]'] = 'debarras';
        $form['quote_flow[service][propertyType]'] = 'maison';
        $form['quote_flow[service][city]'] = 'Lyon';
        $form['quote_flow[service][zipCode]'] = '69001';
        $crawler = $this->client->submit($form);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="quote_flow[contact][firstName]"]');

        // Submit step 2 (contact)
        $form = $crawler->filter('form[action="/devis"]')->form();
        $form['quote_flow[contact][firstName]'] = 'Jean';
        $form['quote_flow[contact][lastName]'] = 'Dupont';
        $form['quote_flow[contact][email]'] = 'jean.dupont@example.com';
        $form['quote_flow[contact][phoneNumber]'] = '+33612345678';
        $form['quote_flow[contact][message]'] = 'Maison de 80m² à débarrasser';
        $form['quote_flow[contact][consent]']->tick();
        $this->client->submit($form);

        self::assertResponseRedirects('/devis/merci');

        // Persistance
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $quotes = $em->getRepository(QuoteRequest::class)->findAll();
        self::assertCount(1, $quotes);
        $q = $quotes[0];
        self::assertSame('debarras', $q->getServiceType());
        self::assertSame('maison', $q->getPropertyType());
        self::assertSame('Lyon', $q->getCity());
        self::assertSame('69001', $q->getZipCode());
        self::assertSame('Jean', $q->getFirstName());
        self::assertSame('jean.dupont@example.com', $q->getEmail());

        // Emails
        self::assertEmailCount(2);

        // Page de succès accessible
        $this->client->request('GET', '/devis/merci');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Demande bien reçue');
    }

    public function testHoneypotTriggersSilentRedirect(): void
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->filter('form[action="/devis"]')->form();
        $form['quote_flow[service][serviceType]'] = 'nettoyage';
        $form['quote_flow[service][propertyType]'] = 'appartement';
        $form['quote_flow[service][city]'] = 'Annecy';
        $form['quote_flow[service][zipCode]'] = '74000';
        $crawler = $this->client->submit($form);

        $form = $crawler->filter('form[action="/devis"]')->form();
        $form['quote_flow[contact][firstName]'] = 'Bot';
        $form['quote_flow[contact][lastName]'] = 'Bot';
        $form['quote_flow[contact][email]'] = 'bot@bot.test';
        $form['quote_flow[contact][phoneNumber]'] = '0612345678';
        $form['quote_flow[contact][website]'] = 'http://spam.test'; // honeypot rempli
        $form['quote_flow[contact][consent]']->tick();
        $this->client->submit($form);

        self::assertResponseRedirects('/devis/merci');

        $em = static::getContainer()->get(EntityManagerInterface::class);
        self::assertCount(0, $em->getRepository(QuoteRequest::class)->findAll(), 'Honeypot must prevent persist');
    }

    public function testStep1MissingFieldsKeepsUserOnStep1(): void
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->filter('form[action="/devis"]')->form();
        // Champs vides → le flow reste sur step 1
        $form['quote_flow[service][serviceType]'] = 'debarras';
        $form['quote_flow[service][propertyType]'] = 'maison';
        // city + zipCode laissés vides
        $crawler = $this->client->submit($form);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="quote_flow[service][city]"]');
        self::assertSelectorNotExists('input[name="quote_flow[contact][firstName]"]');
    }
}
