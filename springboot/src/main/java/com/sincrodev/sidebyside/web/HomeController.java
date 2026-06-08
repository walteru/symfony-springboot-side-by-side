package com.sincrodev.sidebyside.web;

import com.sincrodev.sidebyside.domain.SprintItem;
import com.sincrodev.sidebyside.domain.SprintItemRepository;
import org.springframework.boot.SpringBootVersion;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import java.util.List;

@Controller
public class HomeController {

    private final SprintItemRepository repository;

    public HomeController(SprintItemRepository repository) {
        this.repository = repository;
    }

    @GetMapping("/")
    public String home(Model model) {
        // Sprint 2: los items salen de MySQL (Spring Data JPA), no de un List hardcodeado.
        // El cálculo y el contrato visible no cambian respecto al Sprint 1.
        List<SprintItem> items = repository.findAllByOrderByIdAsc();

        int totalWeight = items.stream().mapToInt(SprintItem::getWeight).sum();
        int doneWeight = items.stream()
            .filter(i -> "done".equals(i.getStatus()))
            .mapToInt(SprintItem::getWeight)
            .sum();
        int completion = totalWeight == 0 ? 0 : (doneWeight * 100) / totalWeight;

        model.addAttribute("items", items);
        model.addAttribute("totalItems", items.size());
        model.addAttribute("totalWeight", totalWeight);
        model.addAttribute("completion", completion);
        model.addAttribute("framework", "Spring Boot");
        model.addAttribute("frameworkVersion", SpringBootVersion.getVersion());

        return "index";
    }
}
