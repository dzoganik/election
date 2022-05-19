<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultsControllerTest extends WebTestCase
{
    public function testAll(): void
    {
        $client = static::createClient();
        $client->request('GET', '/results');

        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($content[0]['title']);
        $this->assertNotEmpty($content[0]['parties']);
        $this->assertNotEmpty($content[0]['parties'][0]['number']);
        $this->assertNotEmpty($content[0]['parties'][0]['title']);
        $this->assertNotEmpty($content[0]['parties'][0]['percent']);
    }
}
