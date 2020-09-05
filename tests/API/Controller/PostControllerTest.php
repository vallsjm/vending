<?php

namespace API\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testPostPostAction()
    {
        $client = static::createClient();

        $payload = [
            'author_id'   => 'fe749042-f744-411c-80cc-5c7bbfb4d02a',
            'title'       => 'Titulo del Post',
            'description' => 'Pequeña descripción',
            'content'     => 'contenido'
        ];

        $client->request(
            'POST',
            '/api/post',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'ACCEPT' => 'application/xml'
            ],
            json_encode($payload)
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetPostByIdAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/post/a1f33606-cd97-11ea-87d0-0242ac130003');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
