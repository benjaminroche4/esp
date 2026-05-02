<?php

declare(strict_types=1);

namespace App\Tests;

use App\Contact\Entity\Contact;

final class ContactFormTest extends AbstractWebTestCase
{
    public function testValidSubmissionPersistsAndRedirects(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Envoyer')->form([
            'contact[firstName]' => 'Jean',
            'contact[lastName]' => 'Dupont',
            'contact[email]' => 'jean.dupont@example.com',
            'contact[contactType]' => 'Particulier',
            'contact[interventionDeadline]' => 'Intervention rapide (1 à 3 jours)',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/contact');

        $em = static::getContainer()->get(\Doctrine\ORM\EntityManagerInterface::class);
        $contacts = $em->getRepository(Contact::class)->findAll();

        self::assertCount(1, $contacts);
        self::assertSame('jean.dupont@example.com', $contacts[0]->getEmail());
    }
}
