package Test;

import Entities.OffreStage;
import Entities.StageCondidature;
import Services.ServiceOffreStage;
import Services.ServiceOffreStageInterface;
import Services.ServiceStageCondidature;
import utils.MyDatabase;

import java.sql.Date;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.util.List;

public class Main {
    public static void main(String[] args) {
        try {
            MyDatabase db = MyDatabase.getInstance();
            ServiceOffreStageInterface sos = new ServiceOffreStage(db.getConnection());
            ServiceStageCondidature ssc = new ServiceStageCondidature(db.getConnection());



            System.out.println("\n--- Test OffreStage ---");

            OffreStage newOffre = new OffreStage("Dev Java", "Stage PFE", "Esprit", "Tunis", "IT", "Java", 6, new Timestamp(System.currentTimeMillis()), "Ouvert", null);
            sos.ajouter(newOffre);

            List<OffreStage> offres = sos.afficherAll();
            int lastOffreId = offres.get(offres.size() - 1).getId();
            System.out.println("Dernière offre ajoutée ID: " + lastOffreId);

            OffreStage oToUpdate = sos.getById(lastOffreId);
            oToUpdate.setTitre("Dev Java Senior");
            sos.modifier(oToUpdate);


            System.out.println("\n--- Test StageCondidature ---");

            StageCondidature newCond = new StageCondidature("PFE", "Ma Candidature", "Motivation...", "IT", "SQL", "cv.pdf", "lettre.pdf", new Date(System.currentTimeMillis()), "En attente", null, lastOffreId);
            ssc.ajouter(newCond);

            List<StageCondidature> conds = ssc.afficherAll();
            int lastCondId = conds.get(conds.size() - 1).getId();

            StageCondidature cToUpdate = ssc.getById(lastCondId);
            cToUpdate.setStatut("Acceptée");
            ssc.modifier(cToUpdate);


            System.out.println("\n--- Nettoyage (Suppression) ---");
            ssc.supprimer(lastCondId);
            sos.supprimer(lastOffreId);

            System.out.println("\n--- TOUS LES TESTS SONT RÉUSSIS ---");

        } catch (SQLException e) {
            System.err.println("Erreur SQL durant les tests : " + e.getMessage());
            e.printStackTrace();
        }
    }
}