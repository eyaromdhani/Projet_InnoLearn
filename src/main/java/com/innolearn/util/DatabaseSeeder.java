package com.innolearn.util;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.sql.Statement;

public class DatabaseSeeder {

    public static void main(String[] args) {
        seed();
    }

    public static void seed() {
        try (Connection conn = DatabaseConnection.getConnection()) {
            Statement stmt = conn.createStatement();

            // 1. Desactiver les contraintes de cles etrangeres pour nettoyer proprement
            stmt.execute("SET FOREIGN_KEY_CHECKS = 0");
            stmt.execute("TRUNCATE TABLE depots");
            stmt.execute("TRUNCATE TABLE project");
            stmt.execute("SET FOREIGN_KEY_CHECKS = 1");

            System.out.println("Base de donnees nettoyee.");

            // 2. Inserer les projets (5 PROJETS INFORMATIQUE SANS ACCENTS)
            String insertProject = "INSERT INTO project (id, title, description, status, start_date, end_date, created_at, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            try (PreparedStatement pstmt = conn.prepareStatement(insertProject)) {
                
                String[][] projectData = {
                    {"1", "Application Gestion Desktop Java", "Gestion de bibliotheque scolaire en JavaFX.", "actif", "Intermediaire"},
                    {"2", "API REST Spring Boot", "Backend pour systeme de reservation de vols.", "actif", "Expert"},
                    {"3", "Structure de Donnees C++", "Implementation des arbres et tris.", "brouillon", "Debutant"},
                    {"4", "Application Mobile Flutter", "App de livraison de nourriture en temps reel.", "actif", "Intermediaire"},
                    {"5", "Dashboard React et Node", "Tableau de bord analytics pour entreprise.", "actif", "Expert"}
                };

                for (String[] p : projectData) {
                    pstmt.setInt(1, Integer.parseInt(p[0]));
                    pstmt.setString(2, p[1]);
                    pstmt.setString(3, p[2]);
                    pstmt.setString(4, p[3]);
                    pstmt.setDate(5, new java.sql.Date(System.currentTimeMillis()));
                    pstmt.setDate(6, new java.sql.Date(System.currentTimeMillis() + 1000000000L));
                    pstmt.setTimestamp(7, new java.sql.Timestamp(System.currentTimeMillis()));
                    pstmt.setString(8, p[4]);
                    pstmt.executeUpdate();
                }
            }

            // 3. Inserer les depots (MINIMUM 5 PAR PROJET)
            String insertDepot = "INSERT INTO depots (title, description, type, uploaded_at, project_id, student_name, todo_status, ai_score, ai_result, file_path, file_size, file_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            try (PreparedStatement pstmt = conn.prepareStatement(insertDepot)) {
                
                String[] types = {"Code", "Design", "Documentation", "Test", "Rapport"};
                String[] names = {"Ahmed", "Sami", "Yassine", "Mariem", "Linda"};

                for (int pid = 1; pid <= 5; pid++) {
                    for (int d = 1; d <= 5; d++) {
                        pstmt.setString(1, "Livrable " + d + " - Projet " + pid);
                        pstmt.setString(2, "Description detaillee du livrable numero " + d);
                        pstmt.setString(3, types[d-1]);
                        pstmt.setTimestamp(4, new java.sql.Timestamp(System.currentTimeMillis()));
                        pstmt.setInt(5, pid);
                        pstmt.setString(6, names[d-1]);
                        pstmt.setString(7, (d % 2 == 0) ? "Done" : "Doing");
                        pstmt.setInt(8, 60 + (pid * 5) + (d * 2));
                        pstmt.setString(9, "FEEDBACK: Travail correct sur la partie " + types[d-1]);
                        pstmt.setString(10, "uploads/file_" + pid + "_" + d + ".zip");
                        pstmt.setString(11, (10 * d) + " KB");
                        pstmt.setString(12, "zip");
                        pstmt.executeUpdate();
                    }
                }
            }





            System.out.println("Donnees inserees avec succes (SANS ACCENTS).");

        } catch (SQLException e) {
            System.err.println("ERREUR SQL: " + e.getMessage());
            e.printStackTrace();
        }
    }
}

