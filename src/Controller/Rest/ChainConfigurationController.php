<?php


namespace App\Controller\Rest;

use App\Entity\UniqueIdentifiers;
use App\Exception\ValidatorException;
use App\Services\ChainConfigurationService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use App\Entity\ChainConfiguration;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as SWG;
use Symfony\Component\Validator\ConstraintViolationListInterface;


class ChainConfigurationController extends AbstractRestController
{
    /**
     * @var ChainConfigurationService
     */
    private $chainConfigurationService;

    /**
     * ChainConfigurationController constructor.
     * @param ChainConfigurationService $chainConfigurationService
     */
    public function __construct(ChainConfigurationService $chainConfigurationService)
    {
        $this->chainConfigurationService = $chainConfigurationService;
    }

    /**
     * post ChainConfiguration.
     *
     * @Rest\Post("/api/chain-configuration/{uuid}")
     *
     * @Rest\View(serializerGroups={ChainConfiguration::SERIALIZED_GROUP_GET_ONE},
     *      statusCode=Response::HTTP_OK
     *     )
     *
     * @ParamConverter(
     *     "chainConfiguration",
     *      converter="fos_rest.request_body",
     *      options={
     *          "deserializationContext"={"groups"={ChainConfiguration::SERIALIZED_GROUP_POST}},
     *          "version"="1.0",
     *          "validator"={"groups"={ChainConfiguration::SERIALIZED_GROUP_POST}}
     *      }
     * )
     *
     * @Operation(
     *     tags={"ChainConfiguration"},
     *     summary="post ChainConfiguration",
     *     @SWG\RequestBody(
     *         request="body",
     *         description="chain data params",
     *         required=true,
     *         @Model(
     *              type=ChainConfiguration::class,
     *              groups={ChainConfiguration::SERIALIZED_GROUP_POST}
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Json object ChainConfiguration",
     *         @Model(
     *              type=ChainConfiguration::class,
     *              groups={ChainConfiguration::SERIALIZED_GROUP_GET_ONE}
     *         )
     *     )
     * )
     *
     * @ParamConverter("uniqueIdentifiers", options={"mapping": {"uuid": "requestHash"}})
     * @return ChainConfiguration|View
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postChainConfigurationAction(
        ChainConfiguration $chainConfiguration,
        UniqueIdentifiers $uniqueIdentifiers,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            if (count($validationErrors) > 0) {
                throw new ValidatorException(
                    $validationErrors,
                    $chainConfiguration
                );
            }
            $this->chainConfigurationService
                ->createChainConfiguration($chainConfiguration, $uniqueIdentifiers);
        } catch (ValidatorException $e) {
            return $this->createResponse(
                $e->getConstraintViolatinosList(),
                Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            throw new $e;
        }

        return $chainConfiguration;
    }
}