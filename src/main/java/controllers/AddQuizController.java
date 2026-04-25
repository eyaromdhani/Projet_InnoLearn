package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import models.Formulaire;
import services.ServiceFormulaire;
import utils.AlertUtils;
import utils.ValidationUtils;

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
        // Reset styles
        ValidationUtils.clearErrorStyle(tfTitre);
        ValidationUtils.clearErrorStyle(taDescription);
        ValidationUtils.clearErrorStyle(tfTempsLimite);
        ValidationUtils.clearErrorStyle(tfCategory);

        String titre = tfTitre.getText();
        String description = taDescription.getText();
        String tempsLimiteStr = tfTempsLimite.getText();
        String category = tfCategory.getText();

        // Validation Title
        if (!ValidationUtils.isValidLength(titre, 3, 100)) {
            ValidationUtils.setErrorStyle(tfTitre);
            AlertUtils.showError("Validation échouée", "Le titre doit contenir entre 3 et 100 caractères.");
            return;
        }

        // Validation Description
        if (!ValidationUtils.isValidLength(description, 10, 500)) {
            ValidationUtils.setErrorStyle(taDescription);
            AlertUtils.showError("Validation échouée", "La description doit contenir entre 10 et 500 caractères.");
            return;
        }

        // Validation Category
        if (ValidationUtils.isEmpty(category)) {
            ValidationUtils.setErrorStyle(tfCategory);
            AlertUtils.showError("Validation échouée", "La catégorie est obligatoire.");
            return;
        }

        // Validation Time Limit
        if (!ValidationUtils.isPositive(tempsLimiteStr)) {
            ValidationUtils.setErrorStyle(tfTempsLimite);
            AlertUtils.showError("Validation échouée", "Le temps limite doit être un nombre positif (minutes).");
            return;
        }

        int tempsLimite = Integer.parseInt(tempsLimiteStr);
        Formulaire formulaire = new Formulaire(titre, description, tempsLimite, category);
        
        try {
            serviceFormulaire.ajouter(formulaire);
            AlertUtils.showInfo("Succès", "Quiz ajouté avec succès !");
            retournerDashboard();
        } catch (SQLException e) {
            e.printStackTrace();
            AlertUtils.showError("Erreur SQL", "Impossible d'ajouter le quiz: " + e.getMessage());
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
}
