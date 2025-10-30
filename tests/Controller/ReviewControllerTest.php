<?php
namespace App\Tests\Controller;

use App\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReviewControllerTest extends WebTestCase
{
    public function testSubmitReview(): void
    {
        $client = static::createClient();
        $client->catchExceptions(false);

        $container = static::getContainer();
        $router    = $container->get('router');
        $em        = $container->get('doctrine')->getManager();

        $homeUrl   = $router->generate('app_home');
        $uniqueMsg = 'Avis test ' . uniqid('', true);

        // 1) Ouvre la home (le formulaire d'avis est dedans)
        $crawler = $client->request('GET', $homeUrl);
        $this->assertResponseIsSuccessful();

        // 2) Cible le formulaire d'avis via son action
        $formNode = $crawler->filter('form[action$="/avis/nouveau"]')->first();
        $this->assertGreaterThan(0, $formNode->count(), 'Formulaire /avis/nouveau introuvable.');

        // 3) Récupère les vrais "full_name" des champs
        $fn  = $formNode->filter('input[name$="[firstName]"]')->attr('name');
        $ln  = $formNode->filter('input[name$="[lastName]"]')->attr('name');
        $nn  = $formNode->filter('input[name$="[nickname]"]')->attr('name');
        
        // 🔧 FIX : Récupère le nom du premier bouton radio pour rating
        $ratingInputs = $formNode->filter('input[type="radio"][name$="[rating]"]');
        $rt = $ratingInputs->first()->attr('name');
        
        $msg = $formNode->filter('[name$="[message]"]')->attr('name');

        $this->assertNotEmpty($fn,  'name firstName introuvable');
        $this->assertNotEmpty($ln,  'name lastName introuvable');
        $this->assertNotEmpty($nn,  'name nickname introuvable');
        $this->assertNotEmpty($rt,  'name rating introuvable');
        $this->assertNotEmpty($msg, 'name message introuvable');

        // 4) Soumets CE formulaire (CSRF inclus)
        $form = $formNode->form();
        $client->submit($form, [
            $fn  => 'John',
            $ln  => 'Doe',
            $nn  => 'JD',
            $rt  => '5', // 🔧 FIX : Valeur en string comme un vrai formulaire HTML
            $msg => $uniqueMsg,
        ], ['HTTP_REFERER' => $homeUrl]);

        // 5) Redirection vers #reviews puis suivi
        $this->assertResponseRedirects($homeUrl.'#reviews');
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // 🔍 DEBUG : Affiche les flash messages pour voir les erreurs éventuelles
        $flashMessages = $crawler->filter('.alert')->each(fn($node) => $node->text());
        if (!empty($flashMessages)) {
            dump('Flash messages:', $flashMessages);
        }

        // 6) Vérifie que l'avis est bien enregistré en base
        $saved = $em->getRepository(Review::class)->findOneBy(['message' => $uniqueMsg]);
        $this->assertNotNull($saved, "L'avis n'a pas été enregistré.");
        $this->assertEquals('John', $saved->getFirstName());
        $this->assertEquals('Doe', $saved->getLastName());
        $this->assertEquals('JD', $saved->getNickname());
        $this->assertEquals(5, $saved->getRating());
        $this->assertEquals(Review::STATUS_PENDING, $saved->getStatus());

        // 7) Checks légers côté UI (section présente ; flash optionnel)
        $this->assertGreaterThan(
            0,
            $crawler->filter('#reviews')->count(),
            'La section #reviews devrait être présente sur la home.'
        );

        // Flash success devrait être présent
        $hasSuccessFlash = $crawler->filter('.alert-success')->count() > 0;
        $this->assertTrue($hasSuccessFlash, "Le flash de succès devrait être affiché.");
    }
}