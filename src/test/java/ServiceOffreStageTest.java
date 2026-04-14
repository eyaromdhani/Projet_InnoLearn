import Entities.OffreStage;
import Services.ServiceOffreStage;
import utils.MyDatabase;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.MethodOrderer;
import org.junit.jupiter.api.TestMethodOrder;
import org.junit.jupiter.api.Order;

import java.sql.Connection;
import java.sql.Timestamp;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;

@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class ServiceOffreStageTest {

    private ServiceOffreStage service;
    private static int idTest = -1; // Pour stocker l'ID de l'offre de test

    @BeforeEach
    void setUp() {
        System.out.println("Initialisation de ServiceOffreStage avec vraie base...");
        Connection conn = MyDatabase.getInstance().getConnection();
        service = new ServiceOffreStage(conn);
    }

    @AfterEach
    void tearDown() {
        System.out.println("Fin du test.");
    }

    @Test
    @Order(1)
    void testAjouter() {
        System.out.println("-> Test de ajouter()");
        OffreStage os = new OffreStage();
        os.setTitre("TITRE_TEST_UNITAIRE_UNIQUE");
        os.setDescription("Description test");
        os.setEntreprise("InnoLearn");
        os.setLieu("Ariana");
        os.setDomaine("IT");
        os.setCompetences("Java");
        os.setDuree(6);
        os.setDate_publication(new Timestamp(System.currentTimeMillis()));
        os.setStatut("Ouvert");
        os.setId_recruteur(null);

        assertDoesNotThrow(() -> service.ajouter(os), "L'ajout ne doit pas générer d'exception");
    }

    @Test
    @Order(2)
    void testAfficherAllAndFindId() {
        System.out.println("-> Test de afficherAll() et récupération de l'ID");
        assertDoesNotThrow(() -> {
            List<OffreStage> liste = service.afficherAll();
            assertNotNull(liste);
            assertTrue(liste.size() > 0, "La liste ne doit pas être vide après l'ajout");

            // Trouver l'ID de l'élément ajouté
            for (OffreStage os : liste) {
                if ("TITRE_TEST_UNITAIRE_UNIQUE".equals(os.getTitre())) {
                    idTest = os.getId();
                    break;
                }
            }
            assertTrue(idTest != -1, "L'offre de test doit être présente dans la base");
            System.out.println("ID trouvé : " + idTest);
        });
    }

    @Test
    @Order(3)
    void testGetById() {
        System.out.println("-> Test de getById()");
        assertTrue(idTest != -1, "L'ID de test doit exister");
        assertDoesNotThrow(() -> {
            OffreStage os = service.getById(idTest);
            assertNotNull(os, "L'offre récupérée ne doit pas être nulle");
            assertEquals("TITRE_TEST_UNITAIRE_UNIQUE", os.getTitre());
        });
    }

    @Test
    @Order(4)
    void testModifier() {
        System.out.println("-> Test de modifier()");
        assertTrue(idTest != -1, "L'ID de test doit exister");
        assertDoesNotThrow(() -> {
            OffreStage os = service.getById(idTest);
            os.setTitre("TITRE_TEST_UNITAIRE_MODIFIE");
            service.modifier(os);

            // Vérifier la modif
            OffreStage osModifie = service.getById(idTest);
            assertEquals("TITRE_TEST_UNITAIRE_MODIFIE", osModifie.getTitre());
        });
    }

    @Test
    @Order(5)
    void testSupprimer() {
        System.out.println("-> Test de supprimer()");
        assertTrue(idTest != -1, "L'ID de test doit exister");
        assertDoesNotThrow(() -> {
            service.supprimer(idTest);

            // Vérifier la suppression
            OffreStage os = service.getById(idTest);
            assertNull(os, "L'offre doit être nulle après la suppression");
        });
    }
}
