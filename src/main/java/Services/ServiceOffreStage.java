package Services;

import Entities.OffreStage;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceOffreStage implements ServiceOffreStageInterface {
    private Connection conn;

    public ServiceOffreStage(Connection conn) {
        this.conn = conn;
    }

    public void ajouter(OffreStage os) throws SQLException {
        String req = "INSERT INTO offrestage (titre, description, entreprise, lieu, domaine, competences, duree, date_publication, statut, id_recruteur) "
                + "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setString(1, os.getTitre());
            pst.setString(2, os.getDescription());
            pst.setString(3, os.getEntreprise());
            pst.setString(4, os.getLieu());
            pst.setString(5, os.getDomaine());
            pst.setString(6, os.getCompetences());
            pst.setInt(7, os.getDuree());
            if (os.getDate_publication() != null) {
                pst.setTimestamp(8, os.getDate_publication());
            } else {
                pst.setNull(8, Types.TIMESTAMP);
            }
            pst.setString(9, os.getStatut());

            if (os.getId_recruteur() != null) {
                pst.setInt(10, os.getId_recruteur());
            } else {
                pst.setNull(10, Types.INTEGER);
            }

            pst.executeUpdate();
            System.out.println("Offre de stage ajoutée avec succès !");
        }
    }

    public List<OffreStage> afficherAll() throws SQLException {
        List<OffreStage> liste = new ArrayList<>();
        String req = "SELECT * FROM offrestage";

        try (Statement ste = conn.createStatement();
             ResultSet rs = ste.executeQuery(req)) {

            while (rs.next()) {
                OffreStage os = new OffreStage();
                os.setId(rs.getInt("id"));
                os.setTitre(rs.getString("titre"));
                os.setDescription(rs.getString("description"));
                os.setEntreprise(rs.getString("entreprise"));
                os.setLieu(rs.getString("lieu"));
                os.setDomaine(rs.getString("domaine"));
                os.setCompetences(rs.getString("competences"));
                os.setDuree(rs.getInt("duree"));
                os.setDate_publication(rs.getTimestamp("date_publication"));
                os.setStatut(rs.getString("statut"));
                os.setId_recruteur(rs.getObject("id_recruteur", Integer.class));

                liste.add(os);
            }
        }
        return liste;
    }

    public OffreStage getById(int id) throws SQLException {
        String req = "SELECT * FROM offrestage WHERE id = ?";
        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, id);
            try (ResultSet rs = pst.executeQuery()) {
                if (rs.next()) {
                    OffreStage os = new OffreStage();
                    os.setId(rs.getInt("id"));
                    os.setTitre(rs.getString("titre"));
                    os.setDescription(rs.getString("description"));
                    os.setEntreprise(rs.getString("entreprise"));
                    os.setLieu(rs.getString("lieu"));
                    os.setDomaine(rs.getString("domaine"));
                    os.setCompetences(rs.getString("competences"));
                    os.setDuree(rs.getInt("duree"));
                    os.setDate_publication(rs.getTimestamp("date_publication"));
                    os.setStatut(rs.getString("statut"));
                    os.setId_recruteur(rs.getObject("id_recruteur", Integer.class));
                    return os;
                }
            }
        }
        return null;
    }

    public void modifier(OffreStage os) throws SQLException {
        String req = "UPDATE offrestage SET titre=?, description=?, entreprise=?, lieu=?, domaine=?, "
                + "competences=?, duree=?, date_publication=?, statut=?, id_recruteur=? WHERE id=?";

        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setString(1, os.getTitre());
            pst.setString(2, os.getDescription());
            pst.setString(3, os.getEntreprise());
            pst.setString(4, os.getLieu());
            pst.setString(5, os.getDomaine());
            pst.setString(6, os.getCompetences());
            pst.setInt(7, os.getDuree());
            if (os.getDate_publication() != null) {
                pst.setTimestamp(8, os.getDate_publication());
            } else {
                pst.setNull(8, Types.TIMESTAMP);
            }
            pst.setString(9, os.getStatut());

            if (os.getId_recruteur() != null) {
                pst.setInt(10, os.getId_recruteur());
            } else {
                pst.setNull(10, Types.INTEGER);
            }

            pst.setInt(11, os.getId());

            int rows = pst.executeUpdate();
            if (rows > 0) {
                System.out.println("Offre mise à jour avec succès !");
            } else {
                System.out.println("Aucune offre trouvée avec l'ID : " + os.getId());
            }
        }
    }

    public void supprimer(int id) throws SQLException {
        String req = "DELETE FROM offrestage WHERE id = ?";
        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, id);
            int rows = pst.executeUpdate();
            if (rows > 0) {
                System.out.println("Offre " + id + " supprimée avec succès.");
            } else {
                System.out.println("Aucune offre trouvée avec l'ID : " + id);
            }
        }
    }
}