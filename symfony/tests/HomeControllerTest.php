<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomeRendersSprintContract(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();

        // Header
        $this->assertStringContainsString('Same App, Two Stacks — Sprint 1', $content);
        $this->assertStringContainsString('Powered by', $content);
        $this->assertStringContainsString('Symfony', $content);

        // Items (paridad de data con springboot)
        $this->assertStringContainsString('Bootstrap repo', $content);
        $this->assertStringContainsString('Add Symfony service', $content);
        $this->assertStringContainsString('Add Spring Boot service', $content);
        $this->assertStringContainsString('Write README', $content);
        $this->assertStringContainsString('Publish blog post', $content);

        // Resumen calculado
        $this->assertStringContainsString('Total items: <strong>5</strong>', $content);
        $this->assertStringContainsString('Total weight: <strong>18</strong>', $content);
        $this->assertStringContainsString('Completion: <strong>72%</strong>', $content);
    }
}
