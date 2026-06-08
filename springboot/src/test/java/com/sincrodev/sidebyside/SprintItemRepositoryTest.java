package com.sincrodev.sidebyside;

import com.sincrodev.sidebyside.domain.SprintItem;
import com.sincrodev.sidebyside.domain.SprintItemRepository;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.orm.jpa.DataJpaTest;

import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;

@DataJpaTest
class SprintItemRepositoryTest {

    @Autowired
    private SprintItemRepository repository;

    @Test
    void persistsAndReadsItemsInOrder() {
        repository.saveAll(List.of(
            new SprintItem(1, "Bootstrap repo",          "infra",   "done",        3),
            new SprintItem(2, "Add Symfony service",     "backend", "done",        5),
            new SprintItem(3, "Add Spring Boot service", "backend", "done",        5),
            new SprintItem(4, "Write README",            "docs",    "in_progress", 2),
            new SprintItem(5, "Publish blog post",       "docs",    "planned",     3)
        ));

        List<SprintItem> items = repository.findAllByOrderByIdAsc();

        assertThat(items).hasSize(5);
        // Orden estable por id (paridad con Symfony)
        assertThat(items.stream().map(SprintItem::getId).toList()).containsExactly(1, 2, 3, 4, 5);
        assertThat(items.get(0).getTitle()).isEqualTo("Bootstrap repo");

        // El contrato de cálculo se mantiene: done=13, total=18 -> 72%
        int totalWeight = items.stream().mapToInt(SprintItem::getWeight).sum();
        int doneWeight = items.stream()
            .filter(i -> "done".equals(i.getStatus()))
            .mapToInt(SprintItem::getWeight)
            .sum();
        assertThat(totalWeight).isEqualTo(18);
        assertThat(doneWeight).isEqualTo(13);
        assertThat(doneWeight * 100 / totalWeight).isEqualTo(72);
    }
}
