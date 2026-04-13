package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import models.Formulaire;
import models.Question;
import services.ServiceQuestion;

import java.io.IOException;
import java.sql.SQLException;
import java.util.List;

public class StudentQuestionViewController {

    @FXML
    private Label quizTitleLabel;

    @FXML
    private Label quizDescLabel;

    @FXML
    private VBox questionsContainer;

    private ServiceQuestion serviceQuestion = new ServiceQuestion();

    public void loadQuestions(Formulaire f) {
        quizTitleLabel.setText(f.getTitre());
        quizDescLabel.setText(f.getDescription());
        questionsContainer.getChildren().clear();

        try {
            List<Question> questions = serviceQuestion.getQuestionsByFormulaire(f.getId());
            if (questions.isEmpty()) {
                Label emptyLabel = new Label("Aucune question disponible pour ce quiz.");
                emptyLabel.setStyle("-fx-font-size: 16px; -fx-text-fill: #64748B; -fx-padding: 20;");
                questionsContainer.getChildren().add(emptyLabel);
                return;
            }

            int index = 1;
            for (Question q : questions) {
                VBox qBox = createQuestionCard(q, index++);
                questionsContainer.getChildren().add(qBox);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private VBox createQuestionCard(Question q, int index) {
        VBox card = new VBox();
        card.setSpacing(10);
        card.getStyleClass().add("quiz-card");
        card.setStyle("-fx-padding: 20;");

        Label qTitle = new Label("Question " + index + " (" + q.getPoints() + " points)");
        qTitle.setStyle("-fx-font-size: 14px; -fx-font-weight: bold; -fx-text-fill: #6366F1;");

        Label qText = new Label(q.getQuestionText());
        qText.setStyle("-fx-font-size: 18px; -fx-text-fill: #1E293B;");
        qText.setWrapText(true);

        Label qType = new Label("Type: " + q.getType());
        qType.setStyle("-fx-font-size: 12px; -fx-text-fill: #94A3B8;");

        card.getChildren().addAll(qTitle, qText, qType);
        return card;
    }

    @FXML
    private void handleBack() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/StudentDashboard.fxml"));
            Parent root = loader.load();
            
            Stage stage = (Stage) quizTitleLabel.getScene().getWindow();
            stage.setTitle("Espace Étudiant - InnoLearn");
            stage.setScene(new Scene(root));
            
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
