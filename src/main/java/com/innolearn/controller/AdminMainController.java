package com.innolearn.controller;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.layout.StackPane;
import javafx.stage.Stage;

import java.io.IOException;
import java.net.URL;
import java.util.ResourceBundle;

public class AdminMainController implements Initializable {

    @FXML
    private StackPane adminContentArea;

    @FXML
    private Button btnProjects;

    @FXML
    private Button btnDashboard;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        // Load projects list by default
        loadView("/com/innolearn/AdminProjectList.fxml");
    }

    @FXML
    private void handleDashboard(ActionEvent event) {
        setActiveButton(btnDashboard);
        // loadView("/com/innolearn/AdminDashboard.fxml");
    }

    @FXML
    private void handleProjects(ActionEvent event) {
        setActiveButton(btnProjects);
        loadView("/com/innolearn/AdminProjectList.fxml");
    }

    @FXML
    private void handleLogout(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/RoleSelection.fxml"));
            Parent root = loader.load();
            Stage stage = (Stage) adminContentArea.getScene().getWindow();
            stage.setScene(new Scene(root, 1000, 700));
            stage.centerOnScreen();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void loadView(String fxmlPath) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
            Node view = loader.load();
            adminContentArea.getChildren().setAll(view);
        } catch (IOException e) {
            e.printStackTrace();
            System.err.println("Could not load FXML: " + fxmlPath);
        }
    }

    private void setActiveButton(Button activeBtn) {
        btnDashboard.getStyleClass().remove("active");
        btnProjects.getStyleClass().remove("active");
        // Add others as needed
        
        activeBtn.getStyleClass().add("active");
    }
}
