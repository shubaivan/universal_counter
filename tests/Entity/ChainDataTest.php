<?php


namespace App\Tests\Entity;


use App\Entity\ChainConfiguration;
use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ChainDataTest extends TestCase
{
    public function testMoveUp()
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


        $afterMoveData = $chainData->move();

        $this->assertEquals($chainDataRight, $afterMoveData);
        $this->assertTrue($chainDataRight->getCarriage());
        $this->assertFalse($chainData->getCarriage());
        $this->assertFalse($chainDataLeft->getCarriage());

        $this->expectException(BadRequestException::class);
        $chainDataLeft->move();
    }

    public function testMoveDown()
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
            ->setDirection(ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_DOWN])
            ->setChainMainName('test');

        $uniqueIdentifiers = new UniqueIdentifiers();
        $uniqueIdentifiers->setChainConfiguration($chainConfiguration);
        $uniqueIdentifiers->addChainData($chainData)->addChainData($chainDataLeft)->addChainData($chainDataRight);


        $afterMoveData = $chainData->move();

        $this->assertEquals($chainDataLeft, $afterMoveData);
        $this->assertTrue($chainDataLeft->getCarriage());
        $this->assertFalse($chainData->getCarriage());
        $this->assertFalse($chainDataRight->getCarriage());
    }

    public function testMoveDownException()
    {
        $this->expectException(BadRequestException::class);
        $chainData = new ChainData();
        $chainDataRight = new ChainData();
        $chainDataRight
            ->setChainDataName('right');
        $chainDataLeft = new ChainData();
        $chainDataLeft
            ->setCarriage(true)
            ->setChainDataName('left');
        $chainData
            ->setChainDataName('current')
            ->setRight($chainDataRight)
            ->setLeft($chainDataLeft);

        $chainConfiguration = new ChainConfiguration();
        $chainConfiguration
            ->setDirection(ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_DOWN])
            ->setChainMainName('test');

        $uniqueIdentifiers = new UniqueIdentifiers();
        $uniqueIdentifiers->setChainConfiguration($chainConfiguration);
        $uniqueIdentifiers->addChainData($chainData)->addChainData($chainDataLeft)->addChainData($chainDataRight);

        $chainDataLeft->move();
    }

    public function testMoveUpException()
    {
        $this->expectException(BadRequestException::class);
        $chainData = new ChainData();
        $chainDataRight = new ChainData();
        $chainDataRight
            ->setCarriage(true)
            ->setChainDataName('right');
        $chainDataLeft = new ChainData();
        $chainDataLeft
            ->setChainDataName('left');
        $chainData
            ->setChainDataName('current')
            ->setRight($chainDataRight)
            ->setLeft($chainDataLeft);

        $chainConfiguration = new ChainConfiguration();
        $chainConfiguration
            ->setDirection(ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_UP])
            ->setChainMainName('test');

        $uniqueIdentifiers = new UniqueIdentifiers();
        $uniqueIdentifiers->setChainConfiguration($chainConfiguration);
        $uniqueIdentifiers->addChainData($chainData)->addChainData($chainDataLeft)->addChainData($chainDataRight);

        $chainDataLeft->move();
    }
}