package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import java.io.IOException;

public class SelectionController {

    @FXML
    private VBox adminCard;

    @FXML
    private VBox studentCard;

    @FXML
    private void handleAdminClick() {
        loadDashboard("/AdminDashboard.fxml", "Espace Enseignant - InnoLearn");
    }

    @FXML
    private void handleStudentClick() {
        loadDashboard("/StudentDashboard.fxml", "Espace Étudiant - InnoLearn");
    }

    private void loadDashboard(String fxmlPath, String title) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
            Parent root = loader.load();
            
            Stage stage = (Stage) adminCard.getScene().getWindow();
            stage.setTitle(title);
            stage.setScene(new Scene(root));
            stage.show();
            
        } catch (IOException e) {
            System.err.println("Erreur lors du chargement de " + fxmlPath + " : " + e.getMessage());
            e.printStackTrace();
        }
    }
}
