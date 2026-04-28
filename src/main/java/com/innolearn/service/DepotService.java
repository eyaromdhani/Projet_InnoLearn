package com.innolearn.service;
 
import com.innolearn.model.Depot;
import com.innolearn.util.DatabaseConnection;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class DepotService {

    public List<Depot> getAllDepots() throws SQLException {
        List<Depot> depots = new ArrayList<>();
        String sql = "SELECT * FROM depots ORDER BY uploaded_at DESC";
        
        try (Connection conn = DatabaseConnection.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {
            
            while (rs.next()) {
                depots.add(mapResultSetToDepot(rs));
            }
        }
        return depots;
    }

    public List<Depot> getDepotsByProject(int projectId) throws SQLException {
        List<Depot> depots = new ArrayList<>();
        String sql = "SELECT * FROM depots WHERE project_id = ? ORDER BY uploaded_at DESC";
        
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            
            pstmt.setInt(1, projectId);
            try (ResultSet rs = pstmt.executeQuery()) {
                while (rs.next()) {
                    depots.add(mapResultSetToDepot(rs));
                }
            }
        }
        return depots;
    }

    public void addDepot(Depot depot) throws SQLException {
        String sql = "INSERT INTO depots (title, description, type, file_path, file_size, file_type, uploaded_at, project_id, student_name, download_count, user_id, todo_status, ai_result, ai_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            
            pstmt.setString(1, depot.getTitle());
            pstmt.setString(2, depot.getDescription());
            pstmt.setString(3, depot.getType());
            pstmt.setString(4, depot.getFilePath());
            pstmt.setString(5, depot.getFileSize());
            pstmt.setString(6, depot.getFileType());
            pstmt.setTimestamp(7, new Timestamp(System.currentTimeMillis()));
            pstmt.setInt(8, depot.getProjectId());
            pstmt.setString(9, depot.getStudentName());
            pstmt.setInt(10, 0);
            if (depot.getUserId() != null) {
                pstmt.setInt(11, depot.getUserId());
            } else {
                pstmt.setNull(11, Types.INTEGER);
            }
            pstmt.setString(12, depot.getTodoStatus() != null ? depot.getTodoStatus() : "Doing");
            pstmt.setString(13, depot.getAiResult());
            if (depot.getAiScore() != null) {
                pstmt.setInt(14, depot.getAiScore());
            } else {
                pstmt.setNull(14, Types.INTEGER);
            }
            
            pstmt.executeUpdate();
        }
    }

    public void updateTodoStatus(int depotId, String status) throws SQLException {
        String sql = "UPDATE depots SET todo_status = ? WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, status);
            pstmt.setInt(2, depotId);
            pstmt.executeUpdate();
        }
    }

    public void deleteDepot(int id) throws SQLException {
        String sql = "DELETE FROM depots WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, id);
            pstmt.executeUpdate();
        }
    }

    private Depot mapResultSetToDepot(ResultSet rs) throws SQLException {
        Depot depot = new Depot(
            rs.getInt("id"),
            decodeUTF8(rs.getBytes("title")),
            decodeUTF8(rs.getBytes("description")),
            rs.getString("type"),
            rs.getString("file_path"),
            rs.getString("file_size"),
            rs.getString("file_type"),
            rs.getTimestamp("uploaded_at"),
            rs.getInt("project_id"),
            rs.getString("student_name"),
            rs.getInt("download_count"),
            (Integer) rs.getObject("user_id"),
            rs.getString("todo_status") != null ? rs.getString("todo_status") : "Doing"
        );
        depot.setAiResult(decodeUTF8(rs.getBytes("ai_result")));
        depot.setAiScore((Integer) rs.getObject("ai_score"));
        return depot;
    }

    private String decodeUTF8(byte[] bytes) {
        if (bytes == null) return "";
        return new String(bytes, java.nio.charset.StandardCharsets.UTF_8);
    }
}
