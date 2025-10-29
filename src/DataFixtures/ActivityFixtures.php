<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActivityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $activities = [
            [
                'title' => 'Circuit training',
                'location' => 'extérieur',
                'description' => 'Séances cardio et renforcement avec petit matériel (TRX, poids, élastiques)...',
                'benefits' => [
                    'Idéal pour tonifier et améliorer le souffle',
                    'Formats : individuel, duo, petit collectif',
                    'Matériel fourni et adapté au niveau'
                ],
                'image' => '/prof.Salle.avif',
                'buttonText' => 'Je veux en savoir plus',
                'slug' => 'circuit-training',
                'position' => 1,
                'isActive' => true,
            ],
            [
                'title' => 'Pilates',
                'location' => 'intérieur',
                'description' => 'Travail sur la respiration et la posture pour renforcer le centre du corps.',
                'benefits' => [
                    'Améliore la posture',
                    'Renforce le gainage',
                    'Adapté à tous les niveaux'
                ],
                'image' => 'prof.Salle.avif',
                'buttonText' => 'Parler de mon besoin',
                'slug' => 'pilates',
                'position' => 2,
                'isActive' => true,
            ],
            [
                'title' => 'Stretching',
                'location' => 'extérieur',
                'description' => 'Étirements actifs et passifs, travail postural pour gagner en souplesse et en mobilité. Favorise la récupération, la détente et la prévention des blessures.',
                'benefits' => [
                    'Ambiance calme, focus sur la respiration',
                    'Adapté à tous les niveaux',
                    'Accompagnement progressif et sécurisé'
                ],
                'image' => 'prof.Salle.avif',
                'buttonText' => 'Discuter d\'une séance',
                'slug' => 'circuit-training',
                'position' => 1,
                'isActive' => true,
            ],
        ];

        foreach ($activities as $data) {
            $activity = (new Activity())
                ->setTitle($data['title'])
                ->setLocation($data['location'])
                ->setDescription($data['description'])
                ->setBenefits($data['benefits'])
                ->setImage($data['image'])
                ->setButtonText($data['buttonText'])
                ->setSlug($data['slug'])
                ->setPosition($data['position'])
                ->setIsActive($data['isActive']);

            $manager->persist($activity);
        }

        $manager->flush();
    }
}
