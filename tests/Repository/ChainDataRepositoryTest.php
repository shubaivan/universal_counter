<?php


namespace App\Tests\Repository;


use App\Entity\ChainConfiguration;
use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use App\Repository\ChainDataRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChainDataRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSearchByName()
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

        $this->entityManager->persist($uniqueIdentifiers);
        $this->entityManager->flush();
        /** @var ChainDataRepository $objectRepository */
        $objectRepository = $this->entityManager
            ->getRepository(ChainData::class);
        $resultChainData = $objectRepository
            ->getCarriageElementByIdentity($uniqueIdentifiers);

        $this->assertEquals($chainData, $resultChainData);

        $resultChainData = $objectRepository
            ->getLastElementByIdentity($uniqueIdentifiers);

        $this->assertEquals($chainDataRight, $resultChainData);

        $resultChainData = $objectRepository
            ->getFirstElementByIdentity($uniqueIdentifiers);

        $this->assertEquals($chainDataLeft, $resultChainData);

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}