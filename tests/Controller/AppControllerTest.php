<?php

namespace Tests\Controller;

use Tests\WebTestCase;

class AppControllerTest extends WebTestCase
{
    public function testRoot()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('"security":', (string) $response->getBody());
    }
}
