package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import models.Formulaire;
import services.ServiceFormulaire;

import java.io.IOException;
import java.sql.SQLException;

public class AddQuizController {

    @FXML
    private TextField tfTitre;

    @FXML
    private TextArea taDescription;

    @FXML
    private TextField tfTempsLimite;

    @FXML
    private TextField tfCategory;

    private ServiceFormulaire serviceFormulaire = new ServiceFormulaire();

    @FXML
    private void handleRetour() {
        retournerDashboard();
    }

    @FXML
    private void handleEnregistrer() {
        String titre = tfTitre.getText();
        String description = taDescription.getText();
        String tempsLimiteStr = tfTempsLimite.getText();
        String category = tfCategory.getText();

        if (titre.isEmpty() || description.isEmpty() || tempsLimiteStr.isEmpty() || category.isEmpty()) {
            showAlert("Erreur", "Veuillez remplir tous les champs.");
            return;
        }

        int tempsLimite;
        try {
            tempsLimite = Integer.parseInt(tempsLimiteStr);
        } catch (NumberFormatException e) {
            showAlert("Erreur", "Le temps limite doit être un nombre valide.");
            return;
        }

        Formulaire formulaire = new Formulaire(titre, description, tempsLimite, category);
        
        try {
            serviceFormulaire.ajouter(formulaire);
            retournerDashboard();
        } catch (SQLException e) {
            e.printStackTrace();
            showAlert("Erreur DB", "Impossible d'ajouter le quiz: " + e.getMessage());
        }
    }

    private void retournerDashboard() {
        try {
            Stage stage = (Stage) tfTitre.getScene().getWindow();
            Parent root = FXMLLoader.load(getClass().getResource("/AdminDashboard.fxml"));
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void showAlert(String title, String content) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
