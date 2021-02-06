<?php


namespace App\Tests\Conroller\Rest;


use App\Entity\ChainConfiguration;
use App\Entity\UniqueIdentifiers;
use App\Repository\UniqueIdentifiersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChainConfigurationControllerTest extends WebTestCase
{
    const SOME_NAME = 'some_name';
    /**
     * @var EntityManager
     */
    private $entityManager;

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
    public function testPostChainConfigurationAction()
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
        $id = $uniqueIdentifiers->getId();
        $uuidV4 = $uniqueIdentifiers->getRequestHash();
        $name = self::SOME_NAME;
        $this->client->request('POST',
            '/api/chain-configuration/' . $uuidV4,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            '{
              "chainMainName":"' . $name . '",
              "startValue": 1,
              "increment": 1,
              "direction": 1
            }'
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $content = $this->client->getResponse()->getContent();
        $json_decode = (array)json_decode($content);

        $this->assertArrayHasKey('directionType', $json_decode);
        $this->assertSame($json_decode['directionType'], ChainConfiguration::DIRECTION_UP);

        $this->assertArrayHasKey('chainMainName', $json_decode);
        $this->assertSame($json_decode['chainMainName'], self::SOME_NAME);

        $this->assertArrayHasKey('startValue', $json_decode);
        $this->assertSame($json_decode['startValue'], 1);

        $this->assertArrayHasKey('increment', $json_decode);
        $this->assertSame($json_decode['increment'], 1);
        /** @var UniqueIdentifiersRepository $entityRepository */
        $entityRepository = $this->entityManager->getRepository(UniqueIdentifiers::class);
        $actualUniqueIdentifiers = $entityRepository->find($id);
        $this->assertNotNull($actualUniqueIdentifiers->getChainConfiguration());
        $this->assertNotEquals($chainConfiguration, $actualUniqueIdentifiers->getChainConfiguration());
    }
}