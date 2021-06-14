<?php

namespace App\DataFixtures;

use App\Entity\Season;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 10; $i++) {
            $season = new Season();
            $slug = $this->slugify->generate($i);
            $season->setSlug($slug);
            $season->setNumber($i);
            $season->setYear(201 . $i);
            $season->setDescription('Here will come the description');
            $season->setPrograms($this->getReference('program_0'));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class
        ];
    }
}
