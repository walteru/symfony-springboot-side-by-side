package com.sincrodev.sidebyside.domain;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

/**
 * Sprint 2: entidad JPA persistida en MySQL.
 * El id se asigna explícitamente (1..5) para mantener la paridad de contrato
 * con el lado Symfony, por eso no usa @GeneratedValue.
 */
@Entity
@Table(name = "sprint_item")
public class SprintItem {

    @Id
    private int id;
    private String title;
    private String category;
    private String status;
    private int weight;

    protected SprintItem() {
        // requerido por JPA
    }

    public SprintItem(int id, String title, String category, String status, int weight) {
        this.id = id;
        this.title = title;
        this.category = category;
        this.status = status;
        this.weight = weight;
    }

    public int getId() {
        return id;
    }

    public String getTitle() {
        return title;
    }

    public String getCategory() {
        return category;
    }

    public String getStatus() {
        return status;
    }

    public int getWeight() {
        return weight;
    }
}
