<?php


namespace App\Controller\Api;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RequestController
{
    /**
     * @Route(methods={"GET"}, path="/sh_test")
     */
    public function testAction()
    {
        $t =1;
        $y = 1;
        return new JsonResponse('test');
    }
}