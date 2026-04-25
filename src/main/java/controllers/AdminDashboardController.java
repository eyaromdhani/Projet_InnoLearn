package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.ComboBox;
import javafx.scene.control.TextField;
import javafx.scene.layout.FlowPane;
import javafx.scene.layout.VBox;
import javafx.stage.Modality;
import javafx.stage.Stage;
import models.Formulaire;
import services.ServiceFormulaire;

import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;

public class AdminDashboardController implements Initializable {

    @FXML
    private FlowPane quizGrid;

    @FXML
    private TextField searchField;

    @FXML
    private ComboBox<String> categoryFilter;

    @FXML
    private ComboBox<String> sortByCombo;

    private ServiceFormulaire serviceFormulaire = new ServiceFormulaire();
    private List<Formulaire> allQuizzes = new java.util.ArrayList<>();

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        setupFilters();
        loadQuizzes();
    }

    public void loadQuizzes() {
        try {
            allQuizzes = serviceFormulaire.afficher();
            refreshQuizzes();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void refreshQuizzes() {
        quizGrid.getChildren().clear();
        
        String query = (searchField.getText() == null) ? "" : searchField.getText().toLowerCase();
        String category = categoryFilter.getValue();
        String sortBy = sortByCombo.getValue();

        List<Formulaire> filteredList = allQuizzes.stream()
                .filter(f -> f.getTitre().toLowerCase().contains(query))
                .filter(f -> category == null || category.equals("Toutes les catégories") || f.getCategory().equals(category))
                .collect(java.util.stream.Collectors.toList());

        // Sorting logic
        if (sortBy != null) {
            switch (sortBy) {
                case "Temps (Moins long)":
                    filteredList.sort((a, b) -> Integer.compare(a.getTempsLimite(), b.getTempsLimite()));
                    break;
                case "Temps (Plus long)":
                    filteredList.sort((a, b) -> Integer.compare(b.getTempsLimite(), a.getTempsLimite()));
                    break;
                case "Nouveautés":
                    filteredList.sort((a, b) -> Integer.compare(b.getId(), a.getId()));
                    break;
                case "Plus anciens":
                    filteredList.sort((a, b) -> Integer.compare(a.getId(), b.getId()));
                    break;
            }
        }

        for (Formulaire f : filteredList) {
            addQuizCard(f);
        }
    }

    private void addQuizCard(Formulaire f) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/QuizCardAdmin.fxml"));
            VBox card = loader.load();
            
            QuizCardAdminController controller = loader.getController();
            controller.setQuizData(f, this);
            
            quizGrid.getChildren().add(card);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void setupFilters() {
        categoryFilter.getItems().addAll("Toutes les catégories", "Réseau", "Sécurité", "Développement", "Cloud");
        sortByCombo.getItems().addAll("Nouveautés", "Plus anciens", "Temps (Moins long)", "Temps (Plus long)");
        
        searchField.textProperty().addListener((obs, old, val) -> refreshQuizzes());
        categoryFilter.valueProperty().addListener((obs, old, val) -> refreshQuizzes());
        sortByCombo.valueProperty().addListener((obs, old, val) -> refreshQuizzes());
    }

    private void filterQuizzes(String query) {
        // Redundant with refreshQuizzes, but keeping for compatibility if needed elsewhere
        refreshQuizzes();
    }

    @FXML
    private void handleCreateQuiz() {
        try {
            Stage stage = (Stage) quizGrid.getScene().getWindow();
            Parent root = FXMLLoader.load(getClass().getResource("/AddQuiz.fxml"));
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void openFormulaireDialog(Formulaire f) {
        try {
            if (f == null) {
                // Fallback just in case
                handleCreateQuiz();
                return;
            }
            
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/EditQuiz.fxml"));
            Parent root = loader.load();
            
            EditQuizController controller = loader.getController();
            controller.initData(f);

            Stage stage = (Stage) quizGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
            
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGoToBooks() {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/ManageBooks.fxml"));
            Stage stage = (Stage) quizGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
