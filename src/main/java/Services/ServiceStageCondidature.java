package Services;

import Entities.StageCondidature;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceStageCondidature implements ServiceStageCondidatureInterface {
    private Connection conn;

    public ServiceStageCondidature(Connection conn) {
        this.conn = conn;
    }

    public void ajouter(StageCondidature sc) throws SQLException {
        String req = "INSERT INTO stagecondidature (type_request, titre, description, domaine, competences, cv, lettre_motivation, date_publication, statut, id_etudiant, id_offre) "
                + "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setString(1, sc.getType_request());
            pst.setString(2, sc.getTitre());
            pst.setString(3, sc.getDescription());
            pst.setString(4, sc.getDomaine());
            pst.setString(5, sc.getCompetences());
            pst.setString(6, sc.getCv());
            pst.setString(7, sc.getLettre_motivation());
            pst.setDate(8, sc.getDate_publication());
            pst.setString(9, sc.getStatut());

            if (sc.getId_etudiant() != null) pst.setInt(10, sc.getId_etudiant());
            else pst.setNull(10, Types.INTEGER);

            if (sc.getId_offre() != null) pst.setInt(11, sc.getId_offre());
            else pst.setNull(11, Types.INTEGER);

            pst.executeUpdate();
            System.out.println("Candidature soumise avec succès !");
        }
    }

    public void supprimer(int id) throws SQLException {
        String req = "DELETE FROM stagecondidature WHERE id = ?";
        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, id);
            int rows = pst.executeUpdate();
            if (rows > 0) System.out.println("Candidature " + id + " supprimée.");
        }
    }

    public void modifier(StageCondidature sc) throws SQLException {
        String req = "UPDATE stagecondidature SET type_request = ?, titre = ?, description = ?, "
                + "domaine = ?, competences = ?, cv = ?, lettre_motivation = ?, "
                + "date_publication = ?, statut = ?, id_etudiant = ?, id_offre = ? "
                + "WHERE id = ?";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setString(1, sc.getType_request());
            pst.setString(2, sc.getTitre());
            pst.setString(3, sc.getDescription());
            pst.setString(4, sc.getDomaine());
            pst.setString(5, sc.getCompetences());
            pst.setString(6, sc.getCv());
            pst.setString(7, sc.getLettre_motivation());
            pst.setDate(8, sc.getDate_publication());
            pst.setString(9, sc.getStatut());

            // Gestion des clés étrangères (nullables)
            if (sc.getId_etudiant() != null) {
                pst.setInt(10, sc.getId_etudiant());
            } else {
                pst.setNull(10, Types.INTEGER);
            }

            if (sc.getId_offre() != null) {
                pst.setInt(11, sc.getId_offre());
            } else {
                pst.setNull(11, Types.INTEGER);
            }

            // L'ID pour la clause WHERE (indispensable !)
            pst.setInt(12, sc.getId());

            int rows = pst.executeUpdate();
            if (rows > 0) {
                System.out.println("Candidature mise à jour avec succès !");
            } else {
                System.out.println("Aucune candidature trouvée avec l'ID : " + sc.getId());
            }
        }
    }
    public List<StageCondidature> afficherAll() throws SQLException {
        List<StageCondidature> liste = new ArrayList<>();
        String req = "SELECT * FROM stagecondidature";

        try (Statement ste = conn.createStatement();
             ResultSet rs = ste.executeQuery(req)) {

            while (rs.next()) {
                StageCondidature sc = new StageCondidature();
                sc.setId(rs.getInt("id"));
                sc.setType_request(rs.getString("type_request"));
                sc.setTitre(rs.getString("titre"));
                sc.setDescription(rs.getString("description"));
                sc.setDomaine(rs.getString("domaine"));
                sc.setCompetences(rs.getString("competences"));
                sc.setCv(rs.getString("cv"));
                sc.setLettre_motivation(rs.getString("lettre_motivation"));
                sc.setDate_publication(rs.getDate("date_publication"));
                sc.setStatut(rs.getString("statut"));
                sc.setId_etudiant(rs.getObject("id_etudiant", Integer.class));
                sc.setId_offre(rs.getObject("id_offre", Integer.class));

                liste.add(sc);
            }
        }


        return liste;
    }

    public List<StageCondidature> afficherParRecruteur(int idRecruteur) throws SQLException {
        List<StageCondidature> liste = new ArrayList<>();
        String req = "SELECT sc.* FROM stagecondidature sc " +
                     "JOIN offrestage o ON sc.id_offre = o.id " +
                     "WHERE o.id_recruteur = ?";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, idRecruteur);
            try (ResultSet rs = pst.executeQuery()) {
                while (rs.next()) {
                    StageCondidature sc = new StageCondidature();
                    sc.setId(rs.getInt("id"));
                    sc.setType_request(rs.getString("type_request"));
                    sc.setTitre(rs.getString("titre"));
                    sc.setDescription(rs.getString("description"));
                    sc.setDomaine(rs.getString("domaine"));
                    sc.setCompetences(rs.getString("competences"));
                    sc.setCv(rs.getString("cv"));
                    sc.setLettre_motivation(rs.getString("lettre_motivation"));
                    sc.setDate_publication(rs.getDate("date_publication"));
                    sc.setStatut(rs.getString("statut"));
                    sc.setId_etudiant(rs.getObject("id_etudiant", Integer.class));
                    sc.setId_offre(rs.getObject("id_offre", Integer.class));
                    liste.add(sc);
                }
            }
        }
        return liste;
    }

    public List<StageCondidature> afficherDemandes() throws SQLException {
        List<StageCondidature> liste = new ArrayList<>();
        String req = "SELECT * FROM stagecondidature WHERE type_request = 'DEMANDE'";

        try (Statement ste = conn.createStatement();
             ResultSet rs = ste.executeQuery(req)) {

            while (rs.next()) {
                StageCondidature sc = new StageCondidature();
                sc.setId(rs.getInt("id"));
                sc.setType_request(rs.getString("type_request"));
                sc.setTitre(rs.getString("titre"));
                sc.setDescription(rs.getString("description"));
                sc.setDomaine(rs.getString("domaine"));
                sc.setCompetences(rs.getString("competences"));
                sc.setCv(rs.getString("cv"));
                sc.setLettre_motivation(rs.getString("lettre_motivation"));
                sc.setDate_publication(rs.getDate("date_publication"));
                sc.setStatut(rs.getString("statut"));
                sc.setId_etudiant(rs.getObject("id_etudiant", Integer.class));
                sc.setId_offre(rs.getObject("id_offre", Integer.class));

                liste.add(sc);
            }
        }
        return liste;
    }


    public StageCondidature getById(int id) throws SQLException {
        String req = "SELECT * FROM stagecondidature WHERE id = ?";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, id);

            try (ResultSet rs = pst.executeQuery()) {
                if (rs.next()) {
                    StageCondidature sc = new StageCondidature();
                    sc.setId(rs.getInt("id"));
                    sc.setType_request(rs.getString("type_request"));
                    sc.setTitre(rs.getString("titre"));
                    sc.setDescription(rs.getString("description"));
                    sc.setDomaine(rs.getString("domaine"));
                    sc.setCompetences(rs.getString("competences"));
                    sc.setCv(rs.getString("cv"));
                    sc.setLettre_motivation(rs.getString("lettre_motivation"));
                    sc.setDate_publication(rs.getDate("date_publication"));
                    sc.setStatut(rs.getString("statut"));

                    // Utilisation de getObject pour gérer les valeurs NULL des clés étrangères
                    sc.setId_etudiant(rs.getObject("id_etudiant", Integer.class));
                    sc.setId_offre(rs.getObject("id_offre", Integer.class));

                    return sc;
                }
            }
        }
        return null;
    }

    public StageCondidature getProfileEtudiant(int idEtudiant) throws SQLException {
        String req = "SELECT * FROM stagecondidature WHERE id_etudiant = ? AND type_request = 'DEMANDE' LIMIT 1";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, idEtudiant);

            try (ResultSet rs = pst.executeQuery()) {
                if (rs.next()) {
                    StageCondidature sc = new StageCondidature();
                    sc.setId(rs.getInt("id"));
                    sc.setType_request(rs.getString("type_request"));
                    sc.setTitre(rs.getString("titre"));
                    sc.setDescription(rs.getString("description"));
                    sc.setDomaine(rs.getString("domaine"));
                    sc.setCompetences(rs.getString("competences"));
                    sc.setCv(rs.getString("cv"));
                    sc.setLettre_motivation(rs.getString("lettre_motivation"));
                    sc.setDate_publication(rs.getDate("date_publication"));
                    sc.setStatut(rs.getString("statut"));
                    sc.setId_etudiant(rs.getObject("id_etudiant", Integer.class));
                    sc.setId_offre(rs.getObject("id_offre", Integer.class));
                    return sc;
                }
            }
        }
        return null;
    }
}