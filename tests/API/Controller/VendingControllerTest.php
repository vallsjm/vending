<?php

namespace API\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VengingControllerTest extends WebTestCase
{
    public function testPostInsertCoinTenCentsAction()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/coin/insert/0.1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'ACCEPT' => 'application/xml'
            ],
            ''
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', '/api/coin/return');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains('<item key="0">0.1</item>', $client->getResponse()->getContent());
    }

    public function testPostInsertNotValidCoinAction()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/coin/insert/1.9',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'ACCEPT' => 'application/xml'
            ],
            ''
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Invalid coin value', $client->getResponse()->getContent());
    }

}
