package com.sincrodev.sidebyside.domain;

import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface SprintItemRepository extends JpaRepository<SprintItem, Integer> {

    /** Orden estable por id para mantener la paridad visual con Symfony. */
    List<SprintItem> findAllByOrderByIdAsc();
}
