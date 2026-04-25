package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Priority;
import javafx.scene.layout.Region;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import models.Formulaire;
import models.QuizResult;
import services.PDFService;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.io.IOException;
import java.time.Duration;
import java.time.LocalDateTime;

public class QuizResultViewController {

    @FXML private Label scoreLabel;
    @FXML private Label totalLabel;
    @FXML private Label statusBadge;
    @FXML private Label feedbackTitle;
    @FXML private Label quizTitleLabel;
    @FXML private javafx.scene.shape.Circle scoreCircle;
    @FXML private javafx.scene.layout.VBox detailsContainer;

    private QuizResult result;
    private Formulaire quiz;
    private PDFService pdfService = new PDFService();

    public static class AnswerDetail {
        public models.Question question;
        public String userAnswer;
        public boolean isCorrect;

        public AnswerDetail(models.Question question, String userAnswer, boolean isCorrect) {
            this.question = question;
            this.userAnswer = userAnswer;
            this.isCorrect = isCorrect;
        }
    }

    public void setResultData(QuizResult result, Formulaire quiz, java.util.List<AnswerDetail> details) {
        this.result = result;
        this.quiz = quiz;

        double percentage = (double) result.getScore() / result.getTotalPoints() * 100;
        
        scoreLabel.setText(String.valueOf(result.getScore()));
        totalLabel.setText("sur " + result.getTotalPoints());
        quizTitleLabel.setText("Vous avez complété le quiz " + quiz.getTitre());

        if (percentage >= 80) {
            statusBadge.setText("EXCELLENT !");
            statusBadge.setStyle("-fx-background-color: #dcfce7; -fx-text-fill: #059669;");
            feedbackTitle.setText("Quelle performance !");
            scoreCircle.setStroke(javafx.scene.paint.Color.web("#10B981"));
        } else if (percentage >= 50) {
            statusBadge.setText("RÉUSSI !");
            statusBadge.setStyle("-fx-background-color: #dcfce7; -fx-text-fill: #059669;");
            feedbackTitle.setText("Bon travail !");
            scoreCircle.setStroke(javafx.scene.paint.Color.web("#10B981"));
        } else {
            statusBadge.setText("ÉCHEC");
            statusBadge.setStyle("-fx-background-color: #fee2e2; -fx-text-fill: #dc2626;");
            feedbackTitle.setText("Continuez vos efforts !");
            scoreCircle.setStroke(javafx.scene.paint.Color.web("#EF4444"));
        }

        buildDetailsUI(details);
    }

    private void buildDetailsUI(java.util.List<AnswerDetail> details) {
        detailsContainer.getChildren().clear();
        int index = 1;
        for (AnswerDetail detail : details) {
            VBox card = new VBox();
            card.setSpacing(10);
            card.getStyleClass().add(detail.isCorrect ? "bg-success-light" : "bg-danger-light");
            card.setStyle("-fx-padding: 20;");

            HBox header = new HBox();
            header.setAlignment(javafx.geometry.Pos.CENTER_LEFT);
            header.setSpacing(10);

            Label qIndex = new Label("Question " + index++);
            qIndex.setStyle("-fx-font-weight: bold; -fx-text-fill: #1E293B;");
            
            Region spacer = new Region();
            HBox.setHgrow(spacer, javafx.scene.layout.Priority.ALWAYS);

            Label badge = new Label(detail.isCorrect ? "Correct" : "Incorrect");
            badge.setStyle("-fx-background-color: " + (detail.isCorrect ? "#10B981" : "#EF4444") + "; -fx-text-fill: white; -fx-padding: 4 12; -fx-background-radius: 20; -fx-font-size: 11px; -fx-font-weight: bold;");

            header.getChildren().addAll(qIndex, spacer, badge);

            Label qText = new Label(detail.question.getQuestionText());
            qText.setStyle("-fx-font-size: 15px; -fx-text-fill: #334155;");
            qText.setWrapText(true);

            HBox answersRow = new HBox();
            answersRow.setSpacing(30);
            answersRow.setPadding(new javafx.geometry.Insets(10, 0, 0, 0));

            VBox userAnsBox = new VBox(2);
            Label userLabel = new Label("Votre réponse :");
            userLabel.setStyle("-fx-font-size: 11px; -fx-text-fill: #64748B;");
            Label userVal = new Label(detail.userAnswer.isEmpty() ? "(Vide)" : detail.userAnswer);
            userVal.setStyle("-fx-font-weight: bold; -fx-text-fill: " + (detail.isCorrect ? "#059669" : "#dc2626") + ";");
            userAnsBox.getChildren().addAll(userLabel, userVal);

            answersRow.getChildren().add(userAnsBox);

            if (!detail.isCorrect) {
                VBox correctAnsBox = new VBox(2);
                Label correctLabel = new Label("Réponse correcte :");
                correctLabel.setStyle("-fx-font-size: 11px; -fx-text-fill: #64748B;");
                Label correctVal = new Label(detail.question.getCorrectAnswer());
                correctVal.setStyle("-fx-font-weight: bold; -fx-text-fill: #059669;");
                correctAnsBox.getChildren().addAll(correctLabel, correctVal);
                answersRow.getChildren().add(correctAnsBox);
            }

            card.getChildren().addAll(header, qText, answersRow);
            detailsContainer.getChildren().add(card);
        }
    }

    @FXML
    private void handleDownloadPDF() {
        pdfService.generateQuizResultPDF(result, quiz);
    }

    @FXML
    private void handleBackToDashboard() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/StudentDashboard.fxml"));
            Parent root = loader.load();
            Stage stage = (Stage) scoreLabel.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
