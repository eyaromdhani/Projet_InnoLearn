package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.scene.control.Alert;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Priority;
import javafx.scene.layout.Region;
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

    @FXML
    private Label timerLabel;

    @FXML
    private Label questionCountLabel;

    @FXML
    private Label timeLimitLabel;

    private ServiceQuestion serviceQuestion = new ServiceQuestion();
    private services.ServiceQuizResult serviceQuizResult = new services.ServiceQuizResult();
    private Formulaire currentQuiz;
    private java.util.Map<models.Question, TextField> answersMap = new java.util.LinkedHashMap<>(); // Preserve order
    
    // Timer fields
    private javafx.animation.Timeline timeline;
    private int secondsRemaining;

    // Fraud detection fields
    private com.google.gson.JsonObject fraudData = new com.google.gson.JsonObject();
    private com.google.gson.JsonArray fraudEvents = new com.google.gson.JsonArray();
    private int tabSwitches = 0;
    private int blurCount = 0;
    private int copyCount = 0;
    private int pasteCount = 0;
    private int contextMenuCount = 0;

    public void loadQuestions(Formulaire f) {
        this.currentQuiz = f;
        quizTitleLabel.setText(f.getTitre());
        quizDescLabel.setText(f.getDescription());
        timeLimitLabel.setText(f.getTempsLimite() + " min");
        
        questionsContainer.getChildren().clear();
        answersMap.clear();
        
        setupMonitoring();
        startTimer(f.getTempsLimite());

        try {
            List<Question> questions = serviceQuestion.getQuestionsByFormulaire(f.getId());
            questionCountLabel.setText(questions.size() + " Questions");
            
            if (questions.isEmpty()) {
                Label emptyLabel = new Label("Aucune question disponible pour ce quiz.");
                emptyLabel.setStyle("-fx-font-size: 16px; -fx-text-fill: white; -fx-padding: 20;");
                questionsContainer.getChildren().add(emptyLabel);
                return;
            }

            int index = 1;
            for (Question q : questions) {
                HBox qBox = createPremiumQuestionCard(q, index++);
                questionsContainer.getChildren().add(qBox);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private HBox createPremiumQuestionCard(Question q, int index) {
        HBox card = new HBox();
        card.setSpacing(25);
        card.getStyleClass().add("quiz-card");
        card.setStyle("-fx-padding: 30; -fx-background-color: white; -fx-background-radius: 20; -fx-effect: dropshadow(three-pass-box, rgba(0,0,0,0.05), 15, 0, 0, 10);");

        // Number Circle (Left)
        Label numberCircle = new Label(String.valueOf(index));
        numberCircle.getStyleClass().add("circle-number");

        // Content (Right)
        VBox content = new VBox();
        content.setSpacing(15);
        HBox.setHgrow(content, Priority.ALWAYS);

        HBox qHeader = new HBox();
        qHeader.setAlignment(Pos.CENTER_LEFT);
        qHeader.setSpacing(15);

        Label qText = new Label(q.getQuestionText());
        qText.setStyle("-fx-font-size: 18px; -fx-font-weight: bold; -fx-text-fill: #1E293B;");
        qText.setWrapText(true);
        HBox.setHgrow(qText, Priority.ALWAYS);

        Label ptsBadge = new Label(q.getPoints() + " pts");
        ptsBadge.getStyleClass().add("badge-points");

        qHeader.getChildren().addAll(qText, ptsBadge);

        TextField answerField = new TextField();
        answerField.setPromptText("Entrez votre réponse ici...");
        answerField.setStyle("-fx-font-size: 15px; -fx-padding: 12; -fx-background-radius: 12; -fx-border-color: #E2E8F0; -fx-border-radius: 12; -fx-background-color: #F8FAFC;");
        
        // Anti-cheat listeners
        answerField.setOnMouseClicked(e -> {
            if (e.getButton() == javafx.scene.input.MouseButton.SECONDARY) {
                contextMenuCount++;
                logFraudEvent("contextmenu");
            }
        });
        answerField.addEventFilter(javafx.scene.input.KeyEvent.KEY_PRESSED, e -> {
            if (e.isControlDown() || e.isMetaDown()) {
                if (e.getCode() == javafx.scene.input.KeyCode.C) { copyCount++; logFraudEvent("copy"); }
                else if (e.getCode() == javafx.scene.input.KeyCode.V) { pasteCount++; logFraudEvent("paste"); }
            }
        });

        answersMap.put(q, answerField);

        content.getChildren().addAll(qHeader, answerField);
        card.getChildren().addAll(numberCircle, content);
        return card;
    }

    private void logFraudEvent(String type) {
        com.google.gson.JsonObject event = new com.google.gson.JsonObject();
        event.addProperty("type", type);
        event.addProperty("timestamp", java.time.OffsetDateTime.now().toString());
        event.add("details", com.google.gson.JsonNull.INSTANCE);
        fraudEvents.add(event);
    }

    private void startTimer(Integer minutes) {
        if (minutes == null || minutes <= 0) {
            timerLabel.setText("∞");
            return;
        }

        secondsRemaining = minutes * 60;
        timeline = new javafx.animation.Timeline(new javafx.animation.KeyFrame(javafx.util.Duration.seconds(1), event -> {
            secondsRemaining--;
            int mins = secondsRemaining / 60;
            int secs = secondsRemaining % 60;
            timerLabel.setText(String.format("%02d:%02d", mins, secs));

            if (secondsRemaining <= 30) {
                timerLabel.setStyle("-fx-text-fill: #EF4444; -fx-font-weight: bold;");
            }

            if (secondsRemaining <= 0) {
                timeline.stop();
                handleSubmitQuiz();
            }
        }));
        timeline.setCycleCount(javafx.animation.Animation.INDEFINITE);
        timeline.play();
    }

    private void setupMonitoring() {
        javafx.application.Platform.runLater(() -> {
            if (timerLabel.getScene() != null) {
                timerLabel.getScene().getWindow().focusedProperty().addListener((obs, oldVal, newVal) -> {
                    if (!newVal) {
                        tabSwitches++;
                        blurCount++;
                        logFraudEvent("blur");
                        logFraudEvent("visibility_hidden");
                        System.out.println("⚠️ Tentative de fraude détectée : Fenêtre quittée");
                    } else {
                        logFraudEvent("visibility_visible");
                    }
                });
            }
        });
    }

    @FXML
    private void handleSubmitQuiz() {
        if (timeline != null) timeline.stop();

        int score = 0;
        int totalPoints = 0;
        java.util.List<QuizResultViewController.AnswerDetail> details = new java.util.ArrayList<>();

        for (java.util.Map.Entry<models.Question, TextField> entry : answersMap.entrySet()) {
            models.Question q = entry.getKey();
            String userAnswer = entry.getValue().getText().trim();
            boolean isCorrect = userAnswer.equalsIgnoreCase(q.getCorrectAnswer().trim());
            
            if (isCorrect) {
                score += q.getPoints();
            }
            totalPoints += q.getPoints();
            
            details.add(new QuizResultViewController.AnswerDetail(q, userAnswer, isCorrect));
        }

        // Prepare suspicious activity string
        String suspDetails = null;
        if (tabSwitches > 0 || blurCount > 0 || copyCount > 0 || pasteCount > 0 || contextMenuCount > 0) {
            fraudData.addProperty("tabSwitches", tabSwitches);
            fraudData.addProperty("blurCount", blurCount);
            fraudData.addProperty("copyCount", copyCount);
            fraudData.addProperty("pasteCount", pasteCount);
            fraudData.addProperty("contextMenuCount", contextMenuCount);
            fraudData.add("events", fraudEvents);
            suspDetails = fraudData.toString();
        }

        try {
            models.QuizResult result = new models.QuizResult("Etudiant", score, totalPoints, currentQuiz.getId());
            result.setSuspiciousActivity(suspDetails);
            serviceQuizResult.ajouter(result);

            // Navigate to Result View with details
            loadResultView(result, details);

        } catch (SQLException e) {
            e.printStackTrace();
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setContentText("Erreur lors de l'enregistrement de votre résultat : " + e.getMessage());
            alert.show();
        }
    }

    private void loadResultView(models.QuizResult result, java.util.List<QuizResultViewController.AnswerDetail> details) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/QuizResultView.fxml"));
            Parent root = loader.load();
            
            QuizResultViewController controller = loader.getController();
            controller.setResultData(result, currentQuiz, details);
            
            Stage stage = (Stage) timerLabel.getScene().getWindow();
            stage.setTitle("Résultats du Quiz - InnoLearn");
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
            // Fallback to simple alert if view fails
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setContentText("Votre score : " + result.getScore() + " / " + result.getTotalPoints());
            alert.showAndWait();
            handleBack();
        }
    }

    @FXML
    private void handleBack() {
        if (timeline != null) timeline.stop();
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/StudentDashboard.fxml"));
            Parent root = loader.load();
            
            Stage stage = (Stage) timerLabel.getScene().getWindow();
            stage.setTitle("Espace Étudiant - InnoLearn");
            stage.setScene(new Scene(root));
            
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
