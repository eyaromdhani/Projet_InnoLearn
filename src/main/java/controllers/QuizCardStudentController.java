package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import models.Formulaire;

import java.io.IOException;

public class QuizCardStudentController {

    @FXML
    private Label titleLabel;

    @FXML
    private Label descriptionLabel;

    @FXML
    private Label timeLabel;

    @FXML
    private Label questionCountLabel;

    private Formulaire currentQuiz;
    private services.ServiceQuestion serviceQuestion = new services.ServiceQuestion();

    public void setQuizData(Formulaire f) {
        this.currentQuiz = f;
        titleLabel.setText(f.getTitre());
        descriptionLabel.setText(f.getDescription());
        timeLabel.setText(f.getTempsLimite() + " min");
        
        try {
            int count = serviceQuestion.getQuestionsByFormulaire(f.getId()).size();
            questionCountLabel.setText(count + " questions");
        } catch (Exception e) {
            questionCountLabel.setText("0 questions");
        }
    }

    @FXML
    private void handleStartQuiz() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/StudentQuestionView.fxml"));
            Parent root = loader.load();
            
            StudentQuestionViewController controller = loader.getController();
            controller.loadQuestions(currentQuiz);
            
            Stage stage = (Stage) titleLabel.getScene().getWindow();
            stage.setTitle("Questions - " + currentQuiz.getTitre());
            stage.setScene(new Scene(root));
            stage.show();
            
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
