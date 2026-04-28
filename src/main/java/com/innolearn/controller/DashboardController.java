package com.innolearn.controller;
 
import com.innolearn.model.Project;
import com.innolearn.service.ProjectService;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import java.sql.SQLException;
import java.util.List;

public class DashboardController {

    @FXML private Label statProjects;
    @FXML private Label statCourses;
    @FXML private Label statCompletion;

    private ProjectService projectService = new ProjectService();

    @FXML
    public void initialize() {
        try {
            updateDashboardStats();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void updateDashboardStats() throws SQLException {
        List<Project> projects = projectService.getAllProjects();
        if (statProjects != null) {
            statProjects.setText(String.valueOf(projects.size()));
        }
        if (statCourses != null) statCourses.setText("5");
        if (statCompletion != null) statCompletion.setText("85%");
    }
}
