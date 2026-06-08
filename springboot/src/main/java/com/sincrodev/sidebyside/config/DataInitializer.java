package com.sincrodev.sidebyside.config;

import com.sincrodev.sidebyside.domain.SprintItem;
import com.sincrodev.sidebyside.domain.SprintItemRepository;
import org.springframework.boot.CommandLineRunner;
import org.springframework.stereotype.Component;

import java.util.List;

/**
 * Siembra los sprint items iniciales si la tabla está vacía (idempotente).
 * Misma data que el seed del lado Symfony, para mantener el contrato de paridad.
 */
@Component
public class DataInitializer implements CommandLineRunner {

    private final SprintItemRepository repository;

    public DataInitializer(SprintItemRepository repository) {
        this.repository = repository;
    }

    @Override
    public void run(String... args) {
        if (repository.count() > 0) {
            return;
        }
        repository.saveAll(List.of(
            new SprintItem(1, "Bootstrap repo",          "infra",   "done",        3),
            new SprintItem(2, "Add Symfony service",     "backend", "done",        5),
            new SprintItem(3, "Add Spring Boot service", "backend", "done",        5),
            new SprintItem(4, "Write README",            "docs",    "in_progress", 2),
            new SprintItem(5, "Publish blog post",       "docs",    "planned",     3)
        ));
    }
}
