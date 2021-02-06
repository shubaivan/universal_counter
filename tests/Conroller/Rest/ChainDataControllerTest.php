<?php


namespace App\Tests\Conroller\Rest;


use App\Entity\ChainConfiguration;
use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use App\Repository\ChainDataRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChainDataControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var KernelBrowser
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $kernel = self::$kernel;
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testGetChainDataAction()
    {
        $chainConfiguration = new ChainConfiguration();
        $chainConfiguration
            ->setStartValue(1)
            ->setDirection(ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_UP])
            ->setChainMainName('test');

        $uniqueIdentifiers = new UniqueIdentifiers();
        $uniqueIdentifiers->setChainConfiguration($chainConfiguration);

        $this->entityManager->persist($uniqueIdentifiers);
        $this->entityManager->flush();
        /** @var ChainDataRepository $entityRepository */
        $entityRepository = $this->entityManager->getRepository(ChainData::class);
        $beforeCount = $entityRepository->count(['uniqueIdentifiers' => $uniqueIdentifiers]);
        $uuidV4 = $uniqueIdentifiers->getRequestHash();

        $this->client->request('GET',
            '/api/chain-data/' . $uuidV4,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $content = $this->client->getResponse()->getContent();
        $json_decode = (array)json_decode($content);

        $afterCount = $entityRepository->count(['uniqueIdentifiers' => $uniqueIdentifiers]);

        $this->assertSame($beforeCount + 1, $afterCount);
    }
}