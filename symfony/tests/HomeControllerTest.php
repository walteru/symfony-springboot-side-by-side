<?php

namespace App\Tests;

use App\DataFixtures\SprintItemSeed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        // Crea el esquema SQLite (config when@test) y siembra los datos del contrato,
        // para que la request lea desde persistencia igual que en runtime con MySQL.
        self::bootKernel();
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('test.em');

        $schemaTool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        foreach (SprintItemSeed::items() as $item) {
            $em->persist($item);
        }
        $em->flush();

        // El cliente del test arranca su propio kernel: cerramos este para evitar conflicto.
        self::ensureKernelShutdown();
    }

    public function testHomeRendersSprintContractFromDatabase(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();

        // Header (Sprint 2)
        $this->assertStringContainsString('Same App, Two Stacks — Sprint 2', $content);
        $this->assertStringContainsString('Powered by', $content);
        $this->assertStringContainsString('Symfony', $content);

        // Items (paridad de data con springboot, ahora desde MySQL/SQLite)
        $this->assertStringContainsString('Bootstrap repo', $content);
        $this->assertStringContainsString('Add Symfony service', $content);
        $this->assertStringContainsString('Add Spring Boot service', $content);
        $this->assertStringContainsString('Write README', $content);
        $this->assertStringContainsString('Publish blog post', $content);

        // Resumen calculado (contrato sin cambios)
        $this->assertStringContainsString('Total items: <strong>5</strong>', $content);
        $this->assertStringContainsString('Total weight: <strong>18</strong>', $content);
        $this->assertStringContainsString('Completion: <strong>72%</strong>', $content);
    }
}
