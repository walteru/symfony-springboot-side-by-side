<?php

namespace App\Tests;

use App\DataFixtures\SprintItemSeed;
use App\Entity\SprintItem;
use App\Repository\SprintItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SprintItemRepositoryTest extends KernelTestCase
{
    public function testPersistsAndReadsItemsInOrder(): void
    {
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
        $em->clear();

        /** @var SprintItemRepository $repository */
        $repository = $em->getRepository(SprintItem::class);
        $items = $repository->findAllOrdered();

        $this->assertCount(5, $items);
        // Orden estable por id (paridad con Spring Boot)
        $this->assertSame([1, 2, 3, 4, 5], array_map(fn ($i) => $i->getId(), $items));
        $this->assertSame('Bootstrap repo', $items[0]->getTitle());

        // El contrato de cálculo se mantiene: done=13, total=18 -> 72%
        $totalWeight = array_sum(array_map(fn ($i) => $i->getWeight(), $items));
        $doneWeight = array_sum(array_map(fn ($i) => 'done' === $i->getStatus() ? $i->getWeight() : 0, $items));
        $this->assertSame(18, $totalWeight);
        $this->assertSame(13, $doneWeight);
        $this->assertSame(72, intdiv($doneWeight * 100, $totalWeight));
    }
}
