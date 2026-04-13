package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import models.Formulaire;
import services.ServiceFormulaire;

import java.sql.SQLException;

public class QuizCardAdminController {

    @FXML
    private Label titleLabel;

    @FXML
    private Label descriptionLabel;

    @FXML
    private Label timeLabel;

    @FXML
    private Label questionCountLabel;

    private Formulaire currentQuiz;
    private AdminDashboardController parentController;
    private ServiceFormulaire serviceFormulaire = new ServiceFormulaire();
    private services.ServiceQuestion serviceQuestion = new services.ServiceQuestion();

    public void setQuizData(Formulaire f, AdminDashboardController parent) {
        this.currentQuiz = f;
        this.parentController = parent;
        
        titleLabel.setText(f.getTitre());
        descriptionLabel.setText(f.getDescription());
        timeLabel.setText(f.getTempsLimite() + " min");
        
        try {
            int count = serviceQuestion.getQuestionsByFormulaire(f.getId()).size();
            questionCountLabel.setText(count + " Questions");
        } catch (SQLException e) {
            questionCountLabel.setText("0 Questions");
        }
    }

    @FXML
    private void handleEdit() {
        parentController.openFormulaireDialog(currentQuiz);
    }

    @FXML
    private void handleDelete() {
        try {
            serviceFormulaire.supprimer(currentQuiz.getId());
            parentController.loadQuizzes();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleViewQuestions() {
        try {
            javafx.fxml.FXMLLoader loader = new javafx.fxml.FXMLLoader(getClass().getResource("/ManageQuestions.fxml"));
            javafx.scene.Parent root = loader.load();
            
            ManageQuestionsController controller = loader.getController();
            controller.initData(currentQuiz);
            
            titleLabel.getScene().setRoot(root);
        } catch (Exception e) {
            System.err.println("Erreur de chargement: " + e.getMessage());
            e.printStackTrace();
        }
    }
}
