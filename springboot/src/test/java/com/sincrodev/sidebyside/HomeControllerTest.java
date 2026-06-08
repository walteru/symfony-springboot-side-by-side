package com.sincrodev.sidebyside;

import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.web.servlet.MockMvc;

import static org.hamcrest.Matchers.containsString;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.content;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;

@SpringBootTest
@AutoConfigureMockMvc
class HomeControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @Test
    void homeRendersSprintContract() throws Exception {
        mockMvc.perform(get("/"))
            .andExpect(status().isOk())
            // Header (Sprint 2)
            .andExpect(content().string(containsString("Same App, Two Stacks — Sprint 2")))
            .andExpect(content().string(containsString("Powered by")))
            .andExpect(content().string(containsString("Spring Boot")))
            // Items (paridad de data con symfony, ahora desde MySQL/H2)
            .andExpect(content().string(containsString("Bootstrap repo")))
            .andExpect(content().string(containsString("Add Symfony service")))
            .andExpect(content().string(containsString("Add Spring Boot service")))
            .andExpect(content().string(containsString("Write README")))
            .andExpect(content().string(containsString("Publish blog post")))
            // Resumen calculado
            .andExpect(content().string(containsString("Total items: <strong>5</strong>")))
            .andExpect(content().string(containsString("Total weight: <strong>18</strong>")))
            .andExpect(content().string(containsString("Completion: <strong>72%</strong>")));
    }
}
