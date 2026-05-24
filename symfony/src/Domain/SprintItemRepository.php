<?php

namespace App\Domain;

final class SprintItemRepository
{
    /** @return SprintItem[] */
    public function findAll(): array
    {
        return [
            new SprintItem(1, 'Bootstrap repo',          'infra',   'done',        3),
            new SprintItem(2, 'Add Symfony service',     'backend', 'done',        5),
            new SprintItem(3, 'Add Spring Boot service', 'backend', 'done',        5),
            new SprintItem(4, 'Write README',            'docs',    'in_progress', 2),
            new SprintItem(5, 'Publish blog post',       'docs',    'planned',     3),
        ];
    }
}
