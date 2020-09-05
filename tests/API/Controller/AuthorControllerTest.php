<?php

namespace API\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    public function testPostAuthorAction()
    {
        $client = static::createClient();

        $payload = [
            'name'      => 'Jose María',
            'surname'   => 'Rodríguez'
        ];

        $client->request(
            'POST',
            '/api/author',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'ACCEPT' => 'application/xml'
            ],
            json_encode($payload)
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testGetAuthorByIdAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/author/a1f33606-cd97-11ea-87d0-0242ac130003');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
