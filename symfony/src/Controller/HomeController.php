<?php

namespace App\Controller;

use App\Entity\SprintItem;
use App\Repository\SprintItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(private readonly SprintItemRepository $repository)
    {
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        // Sprint 2: los items salen de MySQL (Doctrine), no de un array hardcodeado.
        // El cálculo y el contrato visible no cambian respecto al Sprint 1.
        $items = $this->repository->findAllOrdered();
        $totalWeight = array_sum(array_map(fn (SprintItem $i) => $i->getWeight(), $items));
        $doneWeight = array_sum(array_map(
            fn (SprintItem $i) => 'done' === $i->getStatus() ? $i->getWeight() : 0,
            $items
        ));
        $completion = 0 === $totalWeight ? 0 : intdiv($doneWeight * 100, $totalWeight);

        return $this->render('home/index.html.twig', [
            'items' => $items,
            'totalItems' => count($items),
            'totalWeight' => $totalWeight,
            'completion' => $completion,
            'framework' => 'Symfony',
            'frameworkVersion' => Kernel::VERSION,
        ]);
    }
}
