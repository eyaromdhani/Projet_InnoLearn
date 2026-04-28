package com.innolearn.controller;
 
import com.innolearn.model.Project;
import com.innolearn.service.ProjectService;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.VBox;
import java.sql.SQLException;

public class ProjectCardController {

    @FXML private Label lblTitle;
    @FXML private Label lblHeaderTitle;
    @FXML private Label lblDescription;
    @FXML private Label lblDifficulty;
    @FXML private Label lblTag;
    @FXML private VBox vboxHeader;

    private Project project;
    private ProjectListController parentController;
    private ProjectService projectService = new ProjectService();

    public void setData(Project project, ProjectListController parentController) {
        this.project = project;
        this.parentController = parentController;
        
        lblTitle.setText(project.getTitle());
        lblHeaderTitle.setText(project.getTitle());
        lblDescription.setText(project.getDescription());
        lblDifficulty.setText(project.getDifficulty() != null ? project.getDifficulty() : "Intermediate");
        lblTag.setText(project.getStatus() != null ? project.getStatus() : "COURS");
        
        // Dynamic styling for header colors based on difficulty
        String headerClass = "card-header-blue";
        if ("Avancé".equals(project.getDifficulty()) || "Advanced".equals(project.getDifficulty())) {
            headerClass = "card-header-purple";
        } else if ("Débutant".equals(project.getDifficulty()) || "Beginner".equals(project.getDifficulty())) {
            headerClass = "card-header-pink";
        }
        
        vboxHeader.getStyleClass().setAll("card-header", headerClass);
    }

    @FXML
    private void handleView() {
        parentController.handleViewDepots(project);
    }

    @FXML
    private void handleEdit() {
        parentController.handleEditProject(project);
    }

    @FXML
    private void handleMindMap() {
        parentController.handleMindMap(project);
    }

    @FXML
    private void handleDelete() {
        try {
            projectService.deleteProject(project.getId());
            parentController.loadProjects();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
