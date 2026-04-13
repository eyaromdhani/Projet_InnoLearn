import models.Formulaire;
import models.Question;
import services.ServiceFormulaire;
import services.ServiceQuestion;

import java.sql.SQLException;
import java.util.List;
import java.util.Scanner;

public class Main {
    public static void main(String[] args) {
        ServiceFormulaire sf = new ServiceFormulaire();
        ServiceQuestion sq = new ServiceQuestion();

        try {
            // 1. Ajouter un Formulaire de test (Simple : titre + description)
            System.out.println("--- Test Ajout Formulaire ---");
            Formulaire f1 = new Formulaire("Workshop JDBC Final", "Synchronisation avec les noms Symfony", 3600, "Education");
            sf.ajouter(f1);

            // 2. Afficher la liste
            System.out.println("\n--- Liste des Formulaires ---");
            List<Formulaire> list = sf.afficher();
            list.forEach(System.out::println);

            // 3. Test Ajout Question avec les nouveaux noms
            if (!list.isEmpty()) {
                int firstId = list.get(list.size() - 1).getId(); // Get the last added ID
                System.out.println("\n--- Test Ajout Question pour ID " + firstId + " ---");

                Question q1 = new Question("Quel est le pattern de connexion JDBC ?", "Singleton", 5, "QCM", firstId);
                sq.ajouter(q1);

                System.out.println("\n--- Liste des Questions ---");
                sq.afficher().forEach(System.out::println);

                // 4. Supprimer une question par son ID
                Scanner scanner = new Scanner(System.in);
                System.out.print("\nEntrez l'ID de la question à supprimer : ");
                int idToDelete = scanner.nextInt();
                sq.supprimer(idToDelete);
                System.out.println("✅ Question id=" + idToDelete + " supprimée !");

                System.out.println("\n--- Questions restantes ---");
                sq.afficher().forEach(System.out::println);
            }

        } catch (SQLException e) {
            System.err.println("❌ Erreur JDBC : " + e.getMessage());
        }
    }
}
