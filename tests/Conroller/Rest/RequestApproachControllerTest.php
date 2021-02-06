<?php


namespace App\Tests\Conroller\Rest;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestApproachControllerTest extends WebTestCase
{
    public function testPostRequestAction()
    {
        $client = static::createClient();

        $client->request('POST', '/api/initiate-request');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}