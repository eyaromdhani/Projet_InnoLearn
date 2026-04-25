package services;

import models.QuizResult;
import utils.MyDataBase;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceQuizResult {

    private Connection connection;

    public ServiceQuizResult() {
        connection = MyDataBase.getInstance().getConnection();
    }

    public void ajouter(QuizResult result) throws SQLException {
        String req = "INSERT INTO quiz_result (student_name, score, total_points, created_at, formulaire_id, suspicious_activity) VALUES (?, ?, ?, ?, ?, ?)";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, result.getStudentName());
        ps.setInt(2, result.getScore());
        ps.setInt(3, result.getTotalPoints());
        ps.setTimestamp(4, Timestamp.valueOf(result.getCreatedAt()));
        ps.setInt(5, result.getFormulaireId());
        ps.setString(6, result.getSuspiciousActivity());
        ps.executeUpdate();
        System.out.println("Quiz result saved successfully!");
    }

    public List<QuizResult> getResultsByStudent(String studentName) throws SQLException {
        List<QuizResult> list = new ArrayList<>();
        String req = "SELECT * FROM quiz_result WHERE student_name = ? ORDER BY created_at DESC";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, studentName);
        ResultSet rs = ps.executeQuery();
        while (rs.next()) {
            list.add(new QuizResult(
                    rs.getInt("id"),
                    rs.getString("student_name"),
                    rs.getInt("score"),
                    rs.getInt("total_points"),
                    rs.getTimestamp("created_at").toLocalDateTime(),
                    rs.getInt("formulaire_id"),
                    rs.getString("suspicious_activity")
            ));
        }
        return list;
    }

    public int getPassCount(int quizId) throws SQLException {
        String req = "SELECT COUNT(*) FROM quiz_result WHERE formulaire_id = ? AND score >= total_points / 2";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, quizId);
        ResultSet rs = ps.executeQuery();
        return rs.next() ? rs.getInt(1) : 0;
    }

    public int getFailCount(int quizId) throws SQLException {
        String req = "SELECT COUNT(*) FROM quiz_result WHERE formulaire_id = ? AND score < total_points / 2";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, quizId);
        ResultSet rs = ps.executeQuery();
        return rs.next() ? rs.getInt(1) : 0;
    }

    public double getAverageScore(int quizId) throws SQLException {
        String req = "SELECT AVG(score) FROM quiz_result WHERE formulaire_id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, quizId);
        ResultSet rs = ps.executeQuery();
        return rs.next() ? rs.getDouble(1) : 0.0;
    }

    public int getSuspiciousCount(int quizId) throws SQLException {
        String req = "SELECT COUNT(*) FROM quiz_result WHERE formulaire_id = ? AND suspicious_activity IS NOT NULL AND suspicious_activity != 'None'";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, quizId);
        ResultSet rs = ps.executeQuery();
        return rs.next() ? rs.getInt(1) : 0;
    }
}
