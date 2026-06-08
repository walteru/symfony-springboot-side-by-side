<?php

namespace App\Command;

use App\DataFixtures\SprintItemSeed;
use App\Repository\SprintItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed',
    description: 'Carga los sprint items iniciales si la tabla está vacía (idempotente).'
)]
class SeedSprintItemsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SprintItemRepository $repository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (\count($this->repository->findAll()) > 0) {
            $io->note('La tabla sprint_item ya tiene datos; no se siembra nada.');

            return Command::SUCCESS;
        }

        foreach (SprintItemSeed::items() as $item) {
            $this->em->persist($item);
        }
        $this->em->flush();

        $io->success('Sembrados 5 sprint items.');

        return Command::SUCCESS;
    }
}
