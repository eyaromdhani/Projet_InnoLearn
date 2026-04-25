package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Label;
import models.Question;

public class GeneratePreviewItemController {

    @FXML
    private Label lblIndex;
    @FXML
    private Label lblQuestionText;
    @FXML
    private Label lblPoints;
    @FXML
    private Label lblAnswer;

    private Question question;
    private ManageQuestionsController parentController;

    public void setData(Question q, int index, ManageQuestionsController parent) {
        this.question = q;
        this.parentController = parent;
        lblIndex.setText("IA SUGGESTION #" + index);
        lblQuestionText.setText(q.getQuestionText());
        lblPoints.setText(q.getPoints() + " Points");
        lblAnswer.setText(q.getCorrectAnswer());
    }

    @FXML
    private void handleUseThisQuestion() {
        parentController.addQuestionFromAI(question);
    }

    public Question getQuestion() {
        return question;
    }
}
