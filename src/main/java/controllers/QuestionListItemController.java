package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import models.Question;
import services.ServiceQuestion;

import java.sql.SQLException;

public class QuestionListItemController {

    @FXML
    private Label lblIndex;

    @FXML
    private Label lblQuestionText;

    @FXML
    private Label lblType;

    @FXML
    private Label lblPoints;

    @FXML
    private Label lblAnswer;

    private Question currentQuestion;
    private ManageQuestionsController parentController;
    private ServiceQuestion serviceQuestion = new ServiceQuestion();

    public void setData(Question question, int index, ManageQuestionsController parentController) {
        this.currentQuestion = question;
        this.parentController = parentController;
        
        lblIndex.setText(String.valueOf(index));
        lblQuestionText.setText(question.getQuestionText());
        lblType.setText("🏷️ " + (question.getType() != null ? question.getType() : "Inconnu"));
        lblPoints.setText("⭐ " + question.getPoints() + " Points");
        
        String answer = question.getCorrectAnswer();
        if (answer != null && answer.length() > 50) {
            answer = answer.substring(0, 47) + "...";
        }
        lblAnswer.setText("✔ " + (answer != null ? answer : "Aucune"));
    }

    @FXML
    private void handleEdit() {
        if (parentController != null) {
            parentController.editQuestion(currentQuestion);
        }
    }

    @FXML
    private void handleDelete() {
        try {
            serviceQuestion.supprimer(currentQuestion.getId());
            if (parentController != null) {
                parentController.reloadQuestions();
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
