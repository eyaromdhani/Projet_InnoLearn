package services;

import interfaces.IService;
import models.Question;
import utils.MyDataBase;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceQuestion implements IService<Question> {

    private Connection connection;

    public ServiceQuestion() {
        connection = MyDataBase.getInstance().getConnection();
    }

    @Override
    public void ajouter(Question question) throws SQLException {
        String req = "INSERT INTO question (question_text, correct_answer, points, type, formulaire_id) VALUES (?, ?, ?, ?, ?)";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, question.getQuestionText());
        ps.setString(2, question.getCorrectAnswer());
        ps.setInt(3, question.getPoints());
        ps.setString(4, question.getType());
        ps.setInt(5, question.getFormulaireId());
        ps.executeUpdate();
        System.out.println("Question ajoutée !");
    }

    @Override
    public void modifier(Question question) throws SQLException {
        String req = "UPDATE question SET question_text = ?, correct_answer = ?, points = ?, type = ?, formulaire_id = ? WHERE id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, question.getQuestionText());
        ps.setString(2, question.getCorrectAnswer());
        ps.setInt(3, question.getPoints());
        ps.setString(4, question.getType());
        ps.setInt(5, question.getFormulaireId());
        ps.setInt(6, question.getId());
        ps.executeUpdate();
        System.out.println("Question modifiée !");
    }

    @Override
    public void supprimer(int id) throws SQLException {
        String req = "DELETE FROM question WHERE id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, id);
        ps.executeUpdate();
        System.out.println("Question supprimée !");
    }

    @Override
    public List<Question> afficher() throws SQLException {
        List<Question> list = new ArrayList<>();
        String req = "SELECT * FROM question";
        Statement st = connection.createStatement();
        ResultSet rs = st.executeQuery(req);
        while (rs.next()) {
            list.add(new Question(
                    rs.getInt("id"),
                    rs.getString("question_text"),
                    rs.getString("correct_answer"),
                    rs.getInt("points"),
                    rs.getString("type"),
                    rs.getInt("formulaire_id")
            ));
        }
        return list;
    }

    public List<Question> getQuestionsByFormulaire(int formulaireId) throws SQLException {
        List<Question> list = new ArrayList<>();
        String req = "SELECT * FROM question WHERE formulaire_id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, formulaireId);
        ResultSet rs = ps.executeQuery();
        while (rs.next()) {
            list.add(new Question(
                    rs.getInt("id"),
                    rs.getString("question_text"),
                    rs.getString("correct_answer"),
                    rs.getInt("points"),
                    rs.getString("type"),
                    rs.getInt("formulaire_id")
            ));
        }
        return list;
    }
}
