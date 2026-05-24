<?php

namespace App\Controller;

use App\Domain\SprintItem;
use App\Domain\SprintItemRepository;
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
        $items = $this->repository->findAll();
        $totalWeight = array_sum(array_map(fn (SprintItem $i) => $i->weight, $items));
        $doneWeight = array_sum(array_map(
            fn (SprintItem $i) => 'done' === $i->status ? $i->weight : 0,
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
