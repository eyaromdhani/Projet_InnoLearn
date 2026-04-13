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

    private ServiceFormulaire serviceFormulaire = new ServiceFormulaire();

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        loadQuizzes();
        setupFilters();
    }

    public void loadQuizzes() {
        quizGrid.getChildren().clear();
        try {
            List<Formulaire> formulaires = serviceFormulaire.afficher();
            for (Formulaire f : formulaires) {
                addQuizCard(f);
            }
        } catch (SQLException e) {
            e.printStackTrace();
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
        // Add categories from DB or static
        categoryFilter.getItems().addAll("Toutes les catégories", "Réseau", "Sécurité", "Développement", "Cloud");
        
        searchField.textProperty().addListener((observable, oldValue, newValue) -> {
            filterQuizzes(newValue);
        });
    }

    private void filterQuizzes(String query) {
        quizGrid.getChildren().clear();
        try {
            List<Formulaire> filtered = serviceFormulaire.rechercherParTitre(query);
            for (Formulaire f : filtered) {
                addQuizCard(f);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
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
}
