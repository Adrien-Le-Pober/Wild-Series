<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 10; $i++) {
            $episode = new Episode();
            $episode->setNumber($i);
            $episode->setTitle('episode n°' . $i);
            $slug = $this->slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $episode->setSynopsis('Here will come the episode\'s synopsis');
            $episode->setSeason($this->getReference('season_1'));
            $manager->persist($episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class
        ];
    }
}
