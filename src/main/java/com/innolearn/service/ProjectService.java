package com.innolearn.service;
 
import com.innolearn.model.Project;
import com.innolearn.util.DatabaseConnection;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ProjectService {

    public List<Project> getAllProjects() throws SQLException {
        List<Project> projects = new ArrayList<>();
        String sql = "SELECT * FROM project ORDER BY created_at DESC";
        
        try (Connection conn = DatabaseConnection.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {
            
            while (rs.next()) {
                projects.add(mapResultSetToProject(rs));
            }
        }
        return projects;
    }

    public void addProject(Project project) throws SQLException {
        String sql = "INSERT INTO project (title, description, status, start_date, end_date, created_at, difficulty, generated_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            
            pstmt.setString(1, project.getTitle());
            pstmt.setString(2, project.getDescription());
            pstmt.setString(3, project.getStatus());
            pstmt.setDate(4, project.getStartDate());
            pstmt.setDate(5, project.getEndDate());
            pstmt.setTimestamp(6, new Timestamp(System.currentTimeMillis()));
            pstmt.setString(7, project.getDifficulty());
            pstmt.setString(8, project.getGeneratedImage());
            
            pstmt.executeUpdate();
        }
    }

    public void updateProject(Project project) throws SQLException {
        String sql = "UPDATE project SET title=?, description=?, status=?, start_date=?, end_date=?, updated_at=?, difficulty=? WHERE id=?";
        
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            
            pstmt.setString(1, project.getTitle());
            pstmt.setString(2, project.getDescription());
            pstmt.setString(3, project.getStatus());
            pstmt.setDate(4, project.getStartDate());
            pstmt.setDate(5, project.getEndDate());
            pstmt.setTimestamp(6, new Timestamp(System.currentTimeMillis()));
            pstmt.setString(7, project.getDifficulty());
            pstmt.setInt(8, project.getId());
            
            pstmt.executeUpdate();
        }
    }

    public void deleteProject(int id) throws SQLException {
        String sql = "DELETE FROM project WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, id);
            pstmt.executeUpdate();
        }
    }

    private Project mapResultSetToProject(ResultSet rs) throws SQLException {
        return new Project(
            rs.getInt("id"),
            decodeUTF8(rs.getBytes("title")),
            decodeUTF8(rs.getBytes("description")),
            rs.getString("status"),
            rs.getDate("start_date"),
            rs.getDate("end_date"),
            rs.getTimestamp("created_at"),
            rs.getTimestamp("updated_at"),
            rs.getString("generated_image"),
            rs.getString("difficulty")
        );
    }

    private String decodeUTF8(byte[] bytes) {
        if (bytes == null) return "";
        return new String(bytes, java.nio.charset.StandardCharsets.UTF_8);
    }
}
