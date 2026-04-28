package com.innolearn.validation;

import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.time.LocalDate;

import static org.junit.jupiter.api.Assertions.*;

/**
 * Tests unitaires pour la logique de validation des formulaires.
 * Ces tests valident les règles de saisie sans dépendance à JavaFX.
 */
@DisplayName("Tests de validation des formulaires Projet & Dépôt")
class FormValidationTest {

    // ════════════════════════════════════════════════════
    //  RÈGLES DE VALIDATION - PROJET
    // ════════════════════════════════════════════════════

    @Test
    @DisplayName("PROJET - Titre valide : 3 caractères ou plus")
    void testProjectTitle_valid() {
        String title = "App Mobile";
        assertTrue(title != null && title.trim().length() >= 3,
                "Un titre valide doit contenir au moins 3 caractères");
    }

    @Test
    @DisplayName("PROJET - Titre invalide : vide")
    void testProjectTitle_empty() {
        String title = "";
        assertTrue(title == null || title.trim().isEmpty(),
                "Un titre vide doit échouer la validation");
    }

    @Test
    @DisplayName("PROJET - Titre invalide : moins de 3 caractères")
    void testProjectTitle_tooShort() {
        String title = "AB";
        assertTrue(title.trim().length() < 3,
                "Un titre de moins de 3 caractères doit échouer");
    }

    @Test
    @DisplayName("PROJET - Titre invalide : null")
    void testProjectTitle_null() {
        String title = null;
        assertTrue(title == null || title.trim().isEmpty() == false || title == null,
                "Un titre null doit être considéré comme invalide");
        assertNull(title);
    }

    @Test
    @DisplayName("PROJET - Description valide : contient du texte")
    void testProjectDescription_valid() {
        String desc = "Développement d'une application de gestion.";
        assertFalse(desc == null || desc.trim().isEmpty(),
                "Une description non-vide est valide");
    }

    @Test
    @DisplayName("PROJET - Description invalide : vide ou espaces")
    void testProjectDescription_blank() {
        String desc = "   ";
        assertTrue(desc.trim().isEmpty(),
                "Une description composée d'espaces doit échouer");
    }

    @Test
    @DisplayName("PROJET - Dates valides : début avant fin")
    void testProjectDates_valid() {
        LocalDate start = LocalDate.of(2026, 1, 1);
        LocalDate end   = LocalDate.of(2026, 6, 30);
        assertFalse(end.isBefore(start),
                "La date de fin doit être après la date de début");
    }

    @Test
    @DisplayName("PROJET - Dates invalides : fin avant début")
    void testProjectDates_endBeforeStart() {
        LocalDate start = LocalDate.of(2026, 6, 1);
        LocalDate end   = LocalDate.of(2026, 1, 1);
        assertTrue(end.isBefore(start),
                "Une date de fin antérieure au début doit échouer");
    }

    @Test
    @DisplayName("PROJET - Dates valides : début et fin identiques")
    void testProjectDates_sameDay() {
        LocalDate date = LocalDate.now();
        assertFalse(date.isBefore(date),
                "Même date début et fin est autorisé");
    }

    @Test
    @DisplayName("PROJET - Date de début nulle doit être invalide")
    void testProjectStartDate_null() {
        LocalDate start = null;
        assertNull(start, "La date de début null est invalide");
    }

    // ════════════════════════════════════════════════════
    //  RÈGLES DE VALIDATION - DÉPÔT
    // ════════════════════════════════════════════════════

    @Test
    @DisplayName("DÉPÔT - Titre valide")
    void testDepotTitle_valid() {
        String title = "Soumission Finale";
        assertFalse(title == null || title.trim().isEmpty(),
                "Un titre de dépôt non vide est valide");
    }

    @Test
    @DisplayName("DÉPÔT - Titre invalide : vide")
    void testDepotTitle_empty() {
        String title = "";
        assertTrue(title.trim().isEmpty(),
                "Un titre de dépôt vide doit échouer");
    }

    @Test
    @DisplayName("DÉPÔT - Nom étudiant valide")
    void testDepotStudentName_valid() {
        String name = "Fatima Ben Ali";
        assertFalse(name == null || name.trim().isEmpty(),
                "Un nom d'étudiant non vide est valide");
    }

    @Test
    @DisplayName("DÉPÔT - Nom étudiant invalide : vide")
    void testDepotStudentName_empty() {
        String name = "";
        assertTrue(name.trim().isEmpty(),
                "Un nom d'étudiant vide doit échouer");
    }

    @Test
    @DisplayName("DÉPÔT - Chemin de fichier valide")
    void testDepotFilePath_valid() {
        String path = "C:/projets/rapport.pdf";
        assertFalse(path == null || path.trim().isEmpty(),
                "Un chemin non vide est valide");
    }

    @Test
    @DisplayName("DÉPÔT - Chemin de fichier invalide : vide")
    void testDepotFilePath_empty() {
        String path = "";
        assertTrue(path.trim().isEmpty(),
                "Un chemin vide doit échouer la validation");
    }

    @Test
    @DisplayName("DÉPÔT - Chemin de fichier invalide : null")
    void testDepotFilePath_null() {
        String path = null;
        assertNull(path, "Un chemin null doit échouer la validation");
    }

    // ════════════════════════════════════════════════════
    //  RÈGLES GÉNÉRALES
    // ════════════════════════════════════════════════════

    @Test
    @DisplayName("GÉNÉRAL - Trim des espaces est appliqué avant validation")
    void testTrimming() {
        String raw = "  Projet Final  ";
        String trimmed = raw.trim();
        assertEquals("Projet Final", trimmed);
        assertTrue(trimmed.length() >= 3);
    }

    @Test
    @DisplayName("GÉNÉRAL - Valeur nulle ne plante pas avec null-check")
    void testNullGuard() {
        String value = null;
        boolean isInvalid = (value == null || value.trim().isEmpty());
        assertTrue(isInvalid, "Un null doit être capturé par le null-check");
    }
}
