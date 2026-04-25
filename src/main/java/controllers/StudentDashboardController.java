package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.layout.FlowPane;
import javafx.scene.control.TextField;
import javafx.scene.layout.VBox;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import models.Formulaire;
import services.ServiceFormulaire;

import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;

public class StudentDashboardController implements Initializable {

    @FXML
    private FlowPane quizGrid;

    @FXML
    private TextField searchField;

    private ServiceFormulaire serviceFormulaire = new ServiceFormulaire();

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        loadQuizzes();
    }

    private void loadQuizzes() {
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
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/QuizCardStudent.fxml"));
            VBox card = loader.load();
            
            QuizCardStudentController controller = loader.getController();
            controller.setQuizData(f);
            
            quizGrid.getChildren().add(card);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGoToLibrary() {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/StudentLibrary.fxml"));
            Stage stage = (Stage) quizGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
