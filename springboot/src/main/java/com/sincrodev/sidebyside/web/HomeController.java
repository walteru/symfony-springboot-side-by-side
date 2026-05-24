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
        List<SprintItem> items = repository.findAll();

        int totalWeight = items.stream().mapToInt(SprintItem::weight).sum();
        int doneWeight = items.stream()
            .filter(i -> "done".equals(i.status()))
            .mapToInt(SprintItem::weight)
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
