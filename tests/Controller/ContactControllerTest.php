<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

class ContactControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;

    public function testContactFormSubmit(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer');
        if ($button->count() === 0) {
            $button = $crawler->filter('form button[type="submit"]')->first();
        }

        $form = $button->form([
            'contact[name]'    => 'Alice',
            'contact[email]'   => 'alice@example.com',
            'contact[message]' => 'Bonjour, ceci est un test.',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $this->assertEmailCount(1);
    }
}
