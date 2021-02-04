<?php


namespace App\Controller\Rest;

use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use App\Services\ChainDataService;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class ChainDataController extends AbstractRestController
{
    /**
     * @var ChainDataService
     */
    private $chainDataService;

    /**
     * ChainDataController constructor.
     * @param ChainDataService $chainDataService
     */
    public function __construct(ChainDataService $chainDataService)
    {
        $this->chainDataService = $chainDataService;
    }

    /**
     * get chain data by uniq identifier.
     *
     * @Rest\Get("/api/chain-data/{uuid}")
     *
     * @Rest\View(serializerGroups={ChainData::SERIALIZED_GROUP_GET_ONE}, statusCode=Response::HTTP_OK)
     *
     * @Operation(
     *     tags={"ChainData"},
     *     summary="get chain data by uniq identifier.",
     *     @SWG\Response(
     *         response=200,
     *         description="Json object ChainData",
     *         @Model(
     *              type=ChainData::class,
     *              groups={ChainData::SERIALIZED_GROUP_GET_ONE}
     *         )
     *     )
     * )
     *
     * @ParamConverter("uniqueIdentifiers", options={"mapping": {"uuid": "requestHash"}})
     *
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData
     * @throws Exception
     */
    public function getChainData(
        UniqueIdentifiers $uniqueIdentifiers
    )
    {
        return $this->chainDataService->getNextDataFromChain($uniqueIdentifiers);
    }
}