package services;

import models.Formulaire;
import org.junit.jupiter.api.*;
import java.sql.SQLException;
import java.util.List;
import static org.junit.jupiter.api.Assertions.*;

/**
 * Classe de test pour ServiceFormulaire.
 * Vérifie les opérations CRUD sur la base de données.
 */
@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class ServiceFormulaireTest {

    private static ServiceFormulaire service;
    private static String testTitre = "Test Formulaire " + System.currentTimeMillis();
    private static String testDesc = "Description de test";

    @BeforeAll
    public static void setup() {
        service = new ServiceFormulaire();
    }

    @Test
    @Order(1)
    @DisplayName("Test de l'ajout d'un formulaire")
    public void testAjouter() throws SQLException {
        Formulaire f = new Formulaire(testTitre, testDesc, 3600, "Test");
        service.ajouter(f);
        
        List<Formulaire> list = service.rechercherParTitre(testTitre);
        assertFalse(list.isEmpty(), "Le formulaire devrait être présent dans la base de données après l'ajout.");
        assertEquals(testTitre, list.get(0).getTitre(), "Le titre doit correspondre.");
    }

    @Test
    @Order(2)
    @DisplayName("Test de l'affichage des formulaires")
    public void testAfficher() throws SQLException {
        List<Formulaire> list = service.afficher();
        assertNotNull(list, "La liste ne doit pas être nulle.");
        assertTrue(list.size() > 0, "La liste doit contenir au moins le formulaire ajouté précédemment.");
    }

    @Test
    @Order(3)
    @DisplayName("Test de la modification d'un formulaire")
    public void testModifier() throws SQLException {
        // Récupérer le formulaire inséré pour avoir son ID
        List<Formulaire> list = service.rechercherParTitre(testTitre);
        assertFalse(list.isEmpty(), "Le formulaire à modifier doit exister.");
        Formulaire f = list.get(0);
        
        String nouveauTitre = "Titre Modifié " + System.currentTimeMillis();
        f.setTitre(nouveauTitre);
        f.setDescription("Description modifiée");
        
        service.modifier(f);
        
        // Vérifier la modification
        List<Formulaire> listModifiee = service.rechercherParTitre(nouveauTitre);
        assertFalse(listModifiee.isEmpty(), "Le formulaire modifié doit être trouvable par son nouveau titre.");
        assertEquals("Description modifiée", listModifiee.get(0).getDescription());
        
        // Mettre à jour le titre pour le test de suppression
        testTitre = nouveauTitre;
    }

    @Test
    @Order(4)
    @DisplayName("Test de la suppression d'un formulaire")
    public void testSupprimer() throws SQLException {
        // Récupérer le formulaire pour avoir son ID
        List<Formulaire> list = service.rechercherParTitre(testTitre);
        assertFalse(list.isEmpty(), "Le formulaire à supprimer doit exister.");
        int id = list.get(0).getId();
        
        service.supprimer(id);
        
        // Vérifier qu'il n'existe plus
        List<Formulaire> listApresSuppression = service.rechercherParTitre(testTitre);
        assertTrue(listApresSuppression.isEmpty(), "Le formulaire ne doit plus être présent après suppression.");
    }
}
