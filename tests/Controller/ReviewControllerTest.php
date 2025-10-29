<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReviewControllerTest extends WebTestCase
{
    public function testSubmitReview(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/avis');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Publier');
        if ($button->count() === 0) {
            $button = $crawler->filter('form button[type="submit"]')->first();
        }

        $form = $button->form([
            'review[name]'    => 'Bob',
            'review[rating]'  => 5,
            'review[comment]' => 'Excellent !',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertSelectorTextContains('#reviews, .reviews, body', 'Excellent');
    }
}
