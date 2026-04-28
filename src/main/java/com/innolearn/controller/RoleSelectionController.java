package com.innolearn.controller;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import java.io.IOException;

public class RoleSelectionController {

    @FXML
    private VBox studentCard;

    @FXML
    private VBox adminCard;

    @FXML
    private void handleStudentRole() {
        switchScene("/com/innolearn/MainView.fxml", "InnoLearn - Portail Étudiant");
    }

    @FXML
    private void handleAdminRole() {
        switchScene("/com/innolearn/AdminMainView.fxml", "InnoLearn - Portail Administrateur");
    }

    private void switchScene(String fxmlPath, String title) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
            Parent root = loader.load();
            
            Stage stage = (Stage) studentCard.getScene().getWindow();
            Scene scene = new Scene(root, 1200, 800); // Admin design might need more space
            scene.getStylesheets().add(getClass().getResource("/com/innolearn/style.css").toExternalForm());
            
            stage.setTitle(title);
            stage.setScene(scene);
            stage.centerOnScreen();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
