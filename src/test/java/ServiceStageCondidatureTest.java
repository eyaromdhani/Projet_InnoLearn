import Entities.StageCondidature;
import Services.ServiceStageCondidature;
import utils.MyDatabase;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.MethodOrderer;
import org.junit.jupiter.api.TestMethodOrder;
import org.junit.jupiter.api.Order;

import java.sql.Connection;
import java.sql.Date;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;

@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class ServiceStageCondidatureTest {

    private ServiceStageCondidature service;
    private static int idTest = -1;

    @BeforeEach
    void setUp() {
        System.out.println("Initialisation de ServiceStageCondidature avec vraie base...");
        Connection conn = MyDatabase.getInstance().getConnection();
        service = new ServiceStageCondidature(conn);
    }

    @AfterEach
    void tearDown() {
        System.out.println("Fin du test.");
    }

    @Test
    @Order(1)
    void testAjouter() {
        System.out.println("-> Test de ajouter()");
        StageCondidature sc = new StageCondidature();
        sc.setType_request("TEST_STAGE");
        sc.setTitre("CONDIDATURE_TEST_UNITAIRE_UNIQUE");
        sc.setDescription("Recherche stage test");
        sc.setDomaine("Dev");
        sc.setCompetences("JUnit");
        sc.setCv("cv.pdf");
        sc.setLettre_motivation("Lettre de motivation...");
        sc.setDate_publication(new Date(System.currentTimeMillis()));
        sc.setStatut("En attente");
        sc.setId_etudiant(null);
        sc.setId_offre(null);

        assertDoesNotThrow(() -> service.ajouter(sc), "L'ajout ne doit pas lever d'exception");
    }

    @Test
    @Order(2)
    void testAfficherAllAndFindId() {
        System.out.println("-> Test de afficherAll() et récupération de l'ID");
        assertDoesNotThrow(() -> {
            List<StageCondidature> liste = service.afficherAll();
            assertNotNull(liste);
            
            for (StageCondidature sc : liste) {
                if ("CONDIDATURE_TEST_UNITAIRE_UNIQUE".equals(sc.getTitre())) {
                    idTest = sc.getId();
                    break;
                }
            }
            assertTrue(idTest != -1, "La candidature de test doit être présente");
            System.out.println("ID trouvé : " + idTest);
        });
    }

    @Test
    @Order(3)
    void testGetById() {
        System.out.println("-> Test de getById()");
        assertTrue(idTest != -1, "L'ID de test doit exister");
        assertDoesNotThrow(() -> {
            StageCondidature sc = service.getById(idTest);
            assertNotNull(sc, "La candidature récupérée ne doit pas être nulle");
            assertEquals("CONDIDATURE_TEST_UNITAIRE_UNIQUE", sc.getTitre());
        });
    }

    @Test
    @Order(4)
    void testModifier() {
        System.out.println("-> Test de modifier()");
        assertTrue(idTest != -1, "L'ID de test doit exister");
        assertDoesNotThrow(() -> {
            StageCondidature sc = service.getById(idTest);
            sc.setTitre("CONDIDATURE_TEST_UNITAIRE_MODIFIE");
            service.modifier(sc);

            StageCondidature scModifie = service.getById(idTest);
            assertEquals("CONDIDATURE_TEST_UNITAIRE_MODIFIE", scModifie.getTitre());
        });
    }

    @Test
    @Order(5)
    void testSupprimer() {
        System.out.println("-> Test de supprimer()");
        assertTrue(idTest != -1, "L'ID de test doit exister");
        assertDoesNotThrow(() -> {
            service.supprimer(idTest);
            StageCondidature sc = service.getById(idTest);
            assertNull(sc, "La candidature doit être complètement supprimée");
        });
    }
}
