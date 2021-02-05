<?php


namespace App\Controller\Rest;

use App\Entity\ChainConfiguration;
use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use App\Exception\ValidatorException;
use App\Services\ChainDataService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

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
     *     @OA\Response(
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

    /**
     * get all chain data by uniq identifier.
     *
     * @Rest\Get("/api/all/chain-data/{uuid}")
     *
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     * @Operation(
     *     tags={"ChainData"},
     *     summary="get all chain data by uniq identifier.",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="chain_data_name", type="string"),
     *                      @OA\Property(property="left_id", type="integer"),
     *                      @OA\Property(property="right_id", type="integer")
     *                  )
     *         )
     *     ),
     * )
     *
     * @ParamConverter("uniqueIdentifiers", options={"mapping": {"uuid": "requestHash"}})
     *
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData
     * @throws Exception
     */
    public function getAllChainData(
        UniqueIdentifiers $uniqueIdentifiers
    )
    {
        return $this->chainDataService->fetchAllChainData($uniqueIdentifiers);
    }

    /**
     * post csv file with exist conf.
     *
     * @Rest\Post("/api/upload-file/{uuid}")
     *
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     * @OA\Tag(name="ChainData")
     * @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 allOf={
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             description="should be csv file",
     *                             property="csv_file",
     *                             type="string", format="binary"
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     * )
     *
     * @OA\Parameter(
     *         description="direction - append or prepend",
     *         in="query",
     *         name="direction",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64",
     *           default="0"
     *         )
     *     )
     *
     * @ParamConverter("uniqueIdentifiers", options={"mapping": {"uuid": "requestHash"}})
     * @Rest\QueryParam(name="direction", default="0", description="direction - append or prepend", requirements="\d+")
     * @Rest\FileParam(name="csv_file", requirements={"mimeTypes"={"text/csv","text/plain"}}, nullable=false)
     *
     * @param ParamFetcher $paramFetcher
     * @param UniqueIdentifiers $uniqueIdentifiers
     *
     * @return array
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidatorException
     * @throws \League\Csv\Exception
     */
    public function postFileAction(
        ParamFetcher $paramFetcher,
        UniqueIdentifiers $uniqueIdentifiers
    )
    {
        $this->chainDataService->applyCustomChainFromFile(
            $uniqueIdentifiers,
            $paramFetcher->get('csv_file'),
            $paramFetcher->get('direction')
        );

        return ['data' => 'success'];
    }
}