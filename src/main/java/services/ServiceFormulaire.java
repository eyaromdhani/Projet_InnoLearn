package services;

import interfaces.IService;
import models.Formulaire;
import utils.MyDataBase;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceFormulaire implements IService<Formulaire> {

    private Connection connection;

    public ServiceFormulaire() {
        connection = MyDataBase.getInstance().getConnection();
    }

    @Override
    public void ajouter(Formulaire formulaire) throws SQLException {
        String req = "INSERT INTO formulaire (titre, description, temps_limite, category) VALUES (?, ?, ?, ?)";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, formulaire.getTitre());
        ps.setString(2, formulaire.getDescription());
        ps.setInt(3, formulaire.getTempsLimite());
        ps.setString(4, formulaire.getCategory());
        ps.executeUpdate();
        System.out.println("Formulaire ajouté !");
    }

    @Override
    public void modifier(Formulaire formulaire) throws SQLException {
        String req = "UPDATE formulaire SET titre = ?, description = ?, temps_limite = ?, category = ? WHERE id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, formulaire.getTitre());
        ps.setString(2, formulaire.getDescription());
        ps.setInt(3, formulaire.getTempsLimite());
        ps.setString(4, formulaire.getCategory());
        ps.setInt(5, formulaire.getId());
        ps.executeUpdate();
        System.out.println("Formulaire modifié !");
    }

    @Override
    public void supprimer(int id) throws SQLException {
        // Supprimer d'abord les questions liées pour éviter les erreurs de contrainte (si CASCADE n'est pas actif)
        try {
            String reqQ = "DELETE FROM question WHERE formulaire_id = ?";
            PreparedStatement psQ = connection.prepareStatement(reqQ);
            psQ.setInt(1, id);
            psQ.executeUpdate();
            System.out.println("Questions associées supprimées !");
        } catch (SQLException e) {
            System.err.println("Note: Erreur lors de la suppression des questions (peut-être déjà vides) : " + e.getMessage());
        }

        try {
            String reqC = "UPDATE cours SET quiz_id = NULL WHERE quiz_id = ?";
            PreparedStatement psC = connection.prepareStatement(reqC);
            psC.setInt(1, id);
            psC.executeUpdate();
            System.out.println("Lien avec les cours supprimé !");
        } catch (SQLException e) {
            System.err.println("Note: Erreur lors de la mise à jour des cours : " + e.getMessage());
        }


        String req = "DELETE FROM formulaire WHERE id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, id);
        ps.executeUpdate();
        System.out.println("Formulaire supprimé !");
    }

    @Override
    public List<Formulaire> afficher() throws SQLException {
        List<Formulaire> list = new ArrayList<>();
        String req = "SELECT * FROM formulaire";
        Statement st = connection.createStatement();
        ResultSet rs = st.executeQuery(req);
        while (rs.next()) {
            list.add(new Formulaire(
                    rs.getInt("id"), 
                    rs.getString("titre"), 
                    rs.getString("description"),
                    rs.getInt("temps_limite"),
                    rs.getString("category")
            ));
        }
        return list;
    }

    public List<Formulaire> rechercherParTitre(String titre) throws SQLException {
        List<Formulaire> list = new ArrayList<>();
        String req = "SELECT * FROM formulaire WHERE titre LIKE ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, "%" + titre + "%");
        ResultSet rs = ps.executeQuery();
        while (rs.next()) {
            list.add(new Formulaire(
                    rs.getInt("id"), 
                    rs.getString("titre"), 
                    rs.getString("description"),
                    rs.getInt("temps_limite"),
                    rs.getString("category")
            ));
        }
        return list;
    }
}
