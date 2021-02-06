<?php

namespace App\DataFixtures;

use App\Entity\ChainConfiguration;
use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChainLifeCycleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $chainData = new ChainData();
        $chainDataRight = new ChainData();
        $chainDataRight
            ->setChainDataName('right');
        $chainDataLeft = new ChainData();
        $chainDataLeft
            ->setChainDataName('left');
        $chainData
            ->setCarriage(true)
            ->setChainDataName('current')
            ->setRight($chainDataRight)
            ->setLeft($chainDataLeft);

        $chainConfiguration = new ChainConfiguration();
        $chainConfiguration
            ->setStartValue(1)
            ->setDirection(ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_UP])
            ->setChainMainName('test');

        $uniqueIdentifiers = new UniqueIdentifiers();
        $uniqueIdentifiers->setChainConfiguration($chainConfiguration);
        $uniqueIdentifiers->addChainData($chainData)->addChainData($chainDataLeft)->addChainData($chainDataRight);

        $manager->persist($uniqueIdentifiers);
        $manager->flush();
    }
}
