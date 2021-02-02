<?php


namespace App\Controller\Rest;


use App\Exception\ValidatorException;
use App\Services\RequestApproachService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Entity\RequestApproach;
use App\Entity\UniqueIdentifiers;

class RequestApproachController extends AbstractRestController
{
    /**
     * @var RequestApproachService
     */
    private $requestApproachService;

    /**
     * RequestApproachController constructor.
     * @param RequestApproachService $requestApproachService
     */
    public function __construct(RequestApproachService $requestApproachService)
    {
        $this->requestApproachService = $requestApproachService;
    }

    /**
     * post RequestApproach.
     *
     * @Rest\Post("/api/initiate-request")
     *
     * @View(serializerGroups={RequestApproach::SERIALIZED_GROUP_GET_ONE},
     *      statusCode=Response::HTTP_OK
     *     )
     *
     * @SWG\Tag(name="RequestApproach")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json object RequestApproach",
     *     @SWG\Schema(ref=@Model(type=RequestApproach::class,
     *     groups={RequestApproach::SERIALIZED_GROUP_GET_ONE, UniqueIdentifiers::SERIALIZED_GROUP_GET_ONE}))
     * )
     *
     * @return RequestApproach|\FOS\RestBundle\View\View
     *
     * @throws ValidatorException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postRequestAction(Request $request)
    {
        try {
            $requestApproachEntity = $this->requestApproachService
                ->createRequestApproachEntity($request->getClientIp());
        } catch (ValidatorException $e) {
            return $this->createResponse(
                $e->getConstraintViolatinosList(),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $requestApproachEntity;
    }
}