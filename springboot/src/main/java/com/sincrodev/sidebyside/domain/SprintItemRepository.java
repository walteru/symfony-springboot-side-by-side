package com.sincrodev.sidebyside.domain;

import org.springframework.stereotype.Component;

import java.util.List;

@Component
public class SprintItemRepository {

    public List<SprintItem> findAll() {
        return List.of(
            new SprintItem(1, "Bootstrap repo",          "infra",   "done",        3),
            new SprintItem(2, "Add Symfony service",     "backend", "done",        5),
            new SprintItem(3, "Add Spring Boot service", "backend", "done",        5),
            new SprintItem(4, "Write README",            "docs",    "in_progress", 2),
            new SprintItem(5, "Publish blog post",       "docs",    "planned",     3)
        );
    }
}
