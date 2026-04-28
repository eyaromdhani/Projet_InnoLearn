package com.innolearn.model;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.sql.Timestamp;

import static org.junit.jupiter.api.Assertions.*;

@DisplayName("Tests du modèle Depot")
class DepotTest {

    private Depot depot;

    @BeforeEach
    void setUp() {
        depot = new Depot();
    }

    @Test
    @DisplayName("Le constructeur par défaut crée un objet vide")
    void testDefaultConstructor() {
        assertNotNull(depot);
        assertEquals(0, depot.getId());
        assertNull(depot.getTitle());
        assertNull(depot.getFilePath());
    }

    @Test
    @DisplayName("Le constructeur complet initialise tous les champs")
    void testFullConstructor() {
        Timestamp now = new Timestamp(System.currentTimeMillis());
        Depot d = new Depot(1, "Rapport Final", "Description",
                "PDF", "/path/to/file.pdf", "1MB", "application/pdf",
                now, 5, "Ali Ben Salah", 0, null);

        assertEquals(1,               d.getId());
        assertEquals("Rapport Final", d.getTitle());
        assertEquals("PDF",           d.getType());
        assertEquals(5,               d.getProjectId());
        assertEquals("Ali Ben Salah", d.getStudentName());
    }

    @Test
    @DisplayName("setTitle / getTitle fonctionnent correctement")
    void testSetAndGetTitle() {
        depot.setTitle("Soumission 1");
        assertEquals("Soumission 1", depot.getTitle());
    }

    @Test
    @DisplayName("setStudentName / getStudentName fonctionnent correctement")
    void testSetAndGetStudentName() {
        depot.setStudentName("Sami Turki");
        assertEquals("Sami Turki", depot.getStudentName());
    }

    @Test
    @DisplayName("setType / getType fonctionnent correctement")
    void testSetAndGetType() {
        depot.setType("Code");
        assertEquals("Code", depot.getType());
    }

    @Test
    @DisplayName("setFilePath / getFilePath fonctionnent correctement")
    void testSetAndGetFilePath() {
        depot.setFilePath("C:/depot/project.zip");
        assertEquals("C:/depot/project.zip", depot.getFilePath());
    }

    @Test
    @DisplayName("setProjectId / getProjectId fonctionnent correctement")
    void testSetAndGetProjectId() {
        depot.setProjectId(7);
        assertEquals(7, depot.getProjectId());
    }

    @Test
    @DisplayName("setUploadedAt / getUploadedAt fonctionnent correctement")
    void testSetAndGetUploadedAt() {
        Timestamp ts = new Timestamp(System.currentTimeMillis());
        depot.setUploadedAt(ts);
        assertEquals(ts, depot.getUploadedAt());
    }

    @Test
    @DisplayName("setDownloadCount / getDownloadCount fonctionnent correctement")
    void testSetAndGetDownloadCount() {
        depot.setDownloadCount(42);
        assertEquals(42, depot.getDownloadCount());
    }

    @Test
    @DisplayName("userId peut être null (champ nullable)")
    void testNullableUserId() {
        depot.setUserId(null);
        assertNull(depot.getUserId());
    }

    @Test
    @DisplayName("userId peut prendre une valeur entière")
    void testSetAndGetUserId() {
        depot.setUserId(99);
        assertEquals(99, depot.getUserId());
    }

    @Test
    @DisplayName("toString contient l'ID, le titre et projectId")
    void testToString() {
        depot.setId(3);
        depot.setTitle("DepotTest");
        depot.setProjectId(10);
        String str = depot.toString();
        assertTrue(str.contains("3"));
        assertTrue(str.contains("DepotTest"));
        assertTrue(str.contains("10"));
    }

    @Test
    @DisplayName("setFileSize / getFileSize fonctionnent correctement")
    void testSetAndGetFileSize() {
        depot.setFileSize("500KB");
        assertEquals("500KB", depot.getFileSize());
    }

    @Test
    @DisplayName("setFileType / getFileType fonctionnent correctement")
    void testSetAndGetFileType() {
        depot.setFileType("application/pdf");
        assertEquals("application/pdf", depot.getFileType());
    }

    @Test
    @DisplayName("setDescription / getDescription fonctionnent correctement")
    void testSetAndGetDescription() {
        depot.setDescription("Fichier final du projet");
        assertEquals("Fichier final du projet", depot.getDescription());
    }
}
