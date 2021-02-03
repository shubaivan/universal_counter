<?php


namespace App\Services;


use App\Entity\RequestApproach;
use App\Entity\UniqueIdentifiers;
use App\Exception\ValidatorException;
use App\Repository\RequestApproachRepository;
use App\Services\ObjectsHandler;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class RequestApproachService
{
    /**
     * @var RequestApproachRepository
     */
    private $requestApproachRepository;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * RequestApproachService constructor.
     * @param RequestApproachRepository $requestApproachRepository
     * @param ObjectsHandler $objectsHandler
     */
    public function __construct(RequestApproachRepository $requestApproachRepository, ObjectsHandler $objectsHandler)
    {
        $this->requestApproachRepository = $requestApproachRepository;
        $this->objectsHandler = $objectsHandler;
    }

    /**
     * @param string $ip
     * @return RequestApproach
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidatorException
     */
    public function createRequestApproachEntity(string $ip): RequestApproach
    {
        $requestApproach = new RequestApproach();
        $requestApproach
            ->setIpAddress($ip)
            ->setUniqueIdentifiers((new UniqueIdentifiers()));

        $this->objectsHandler->validateEntity($requestApproach);
        $this->requestApproachRepository->save($requestApproach);

        return $requestApproach;
    }

    public function getAll()
    {
        return $this->requestApproachRepository->findAll();
    }
}