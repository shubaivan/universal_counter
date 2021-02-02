<?php

namespace App\Controller\Rest;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRestController extends AbstractFOSRestController
{
    /**
     * @param $data
     * @param array $groups
     * @param null $withEmptyField
     * @param int $code
     * @return View
     */
    protected function createResponse(
        $data,
        int $code = Response::HTTP_OK,
        array $groups = [],
        $withEmptyField = null
    )
    {
        $context = new Context();
        if ($groups) {
            $context->setGroups($groups);
        }

        if ($withEmptyField !== null) {
            $context->setSerializeNull($withEmptyField);
        }

        return View::create()
            ->setStatusCode($code)
            ->setData($data)
            ->setContext($context);
    }
}
