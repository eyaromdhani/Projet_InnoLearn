package Services;

import Entities.Experience;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceExperience {
    private Connection conn;

    public ServiceExperience(Connection conn) {
        this.conn = conn;
    }

    public void ajouter(Experience e) throws SQLException {
        String req = "INSERT INTO experience (user_id, type, annee, etablissement, domaine, niveau, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try (PreparedStatement pst = conn.prepareStatement(req, Statement.RETURN_GENERATED_KEYS)) {
            pst.setInt(1, e.getUser_id());
            pst.setString(2, e.getType());
            pst.setString(3, e.getAnnee());
            pst.setString(4, e.getEtablissement());
            pst.setString(5, e.getDomaine());
            pst.setString(6, e.getNiveau());
            pst.setString(7, e.getDescription());

            pst.executeUpdate();

            try (ResultSet rs = pst.getGeneratedKeys()) {
                if (rs.next()) {
                    e.setId(rs.getInt(1));
                }
            }
            System.out.println("Expérience ajoutée !");
        }
    }

    public void supprimer(int id) throws SQLException {
        String req = "DELETE FROM experience WHERE id = ?";
        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, id);
            pst.executeUpdate();
        }
    }

    public List<Experience> getParEtudiant(int idEtudiant) throws SQLException {
        List<Experience> liste = new ArrayList<>();
        String req = "SELECT * FROM experience WHERE user_id = ? ORDER BY id DESC";
        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setInt(1, idEtudiant);
            try (ResultSet rs = pst.executeQuery()) {
                while (rs.next()) {
                    Experience e = new Experience();
                    e.setId(rs.getInt("id"));
                    e.setUser_id(rs.getInt("user_id"));
                    e.setType(rs.getString("type"));
                    e.setAnnee(rs.getString("annee"));
                    e.setEtablissement(rs.getString("etablissement"));
                    e.setDomaine(rs.getString("domaine"));
                    e.setNiveau(rs.getString("niveau"));
                    e.setDescription(rs.getString("description"));
                    liste.add(e);
                }
            }
        }
        return liste;
    }

    public void modifier(Experience e) throws SQLException {
        String req = "UPDATE experience SET type=?, annee=?, etablissement=?, domaine=?, niveau=?, description=? WHERE id=?";
        try (PreparedStatement pst = conn.prepareStatement(req)) {
            pst.setString(1, e.getType());
            pst.setString(2, e.getAnnee());
            pst.setString(3, e.getEtablissement());
            pst.setString(4, e.getDomaine());
            pst.setString(5, e.getNiveau());
            pst.setString(6, e.getDescription());
            pst.setInt(7, e.getId());
            pst.executeUpdate();
        }
    }
}
