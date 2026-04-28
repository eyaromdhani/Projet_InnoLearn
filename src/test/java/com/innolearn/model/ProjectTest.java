package com.innolearn.model;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.sql.Date;
import java.sql.Timestamp;
import java.time.LocalDate;

import static org.junit.jupiter.api.Assertions.*;

@DisplayName("Tests du modèle Project")
class ProjectTest {

    private Project project;

    @BeforeEach
    void setUp() {
        project = new Project();
    }

    @Test
    @DisplayName("Le constructeur par défaut crée un objet vide")
    void testDefaultConstructor() {
        assertNotNull(project);
        assertEquals(0, project.getId());
        assertNull(project.getTitle());
        assertNull(project.getDescription());
    }

    @Test
    @DisplayName("Le constructeur complet initialise tous les champs")
    void testFullConstructor() {
        Timestamp now = new Timestamp(System.currentTimeMillis());
        Date start = Date.valueOf(LocalDate.now());
        Date end   = Date.valueOf(LocalDate.now().plusDays(30));

        Project p = new Project(1, "Test Project", "Description",
                "active", start, end, now, null, null, "Débutant");

        assertEquals(1,              p.getId());
        assertEquals("Test Project", p.getTitle());
        assertEquals("active",       p.getStatus());
        assertEquals("Débutant",     p.getDifficulty());
    }

    @Test
    @DisplayName("setTitle / getTitle fonctionnent correctement")
    void testSetAndGetTitle() {
        project.setTitle("Projet Innovation");
        assertEquals("Projet Innovation", project.getTitle());
    }

    @Test
    @DisplayName("setDescription / getDescription fonctionnent correctement")
    void testSetAndGetDescription() {
        project.setDescription("Une description détaillée.");
        assertEquals("Une description détaillée.", project.getDescription());
    }

    @Test
    @DisplayName("setStatus / getStatus fonctionnent correctement")
    void testSetAndGetStatus() {
        project.setStatus("completed");
        assertEquals("completed", project.getStatus());
    }

    @Test
    @DisplayName("setDifficulty / getDifficulty fonctionnent correctement")
    void testSetAndGetDifficulty() {
        project.setDifficulty("Expert");
        assertEquals("Expert", project.getDifficulty());
    }

    @Test
    @DisplayName("setStartDate / getStartDate fonctionnent correctement")
    void testSetAndGetStartDate() {
        Date date = Date.valueOf(LocalDate.of(2026, 1, 15));
        project.setStartDate(date);
        assertEquals(date, project.getStartDate());
    }

    @Test
    @DisplayName("setEndDate / getEndDate fonctionnent correctement")
    void testSetAndGetEndDate() {
        Date date = Date.valueOf(LocalDate.of(2026, 6, 30));
        project.setEndDate(date);
        assertEquals(date, project.getEndDate());
    }

    @Test
    @DisplayName("setCreatedAt / getCreatedAt fonctionnent correctement")
    void testSetAndGetCreatedAt() {
        Timestamp ts = new Timestamp(System.currentTimeMillis());
        project.setCreatedAt(ts);
        assertEquals(ts, project.getCreatedAt());
    }

    @Test
    @DisplayName("setGeneratedImage / getGeneratedImage fonctionnent correctement")
    void testSetAndGetGeneratedImage() {
        project.setGeneratedImage("image.png");
        assertEquals("image.png", project.getGeneratedImage());
    }

    @Test
    @DisplayName("toString contient l'ID et le titre")
    void testToString() {
        project.setId(42);
        project.setTitle("MonProjet");
        String str = project.toString();
        assertTrue(str.contains("42"));
        assertTrue(str.contains("MonProjet"));
    }

    @Test
    @DisplayName("La date de fin peut être nulle (optionnelle)")
    void testEndDateCanBeNull() {
        project.setEndDate(null);
        assertNull(project.getEndDate());
    }
}
