package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import models.Formulaire;
import models.Question;
import services.GroqService;
import services.ServiceQuestion;
import javafx.application.Platform;
import java.util.ArrayList;

import utils.AlertUtils;
import utils.ValidationUtils;

import java.io.IOException;
import java.sql.SQLException;
import java.util.List;

public class ManageQuestionsController {

    @FXML
    private Label lblQuizTitle;
    @FXML
    private Label lblQuizDesc;
    @FXML
    private Label lblQuizTime;
    @FXML
    private Label lblQuestionCount;

    @FXML
    private Label formIcon;
    @FXML
    private Label formTitle;
    @FXML
    private TextArea tfQuestionText;
    @FXML
    private ComboBox<String> cbType;
    @FXML
    private TextField tfPoints;
    @FXML
    private TextField tfCorrectAnswer;
    @FXML
    private Button btnSubmit;
    @FXML
    private Button btnCancelEdit;

    @FXML
    private VBox listContainer;
    @FXML
    private Label lblCharCount;
    @FXML
    private TextArea taAIContext;
    @FXML
    private ProgressIndicator aiLoader;
    @FXML
    private Button btnGenerateAI;
    @FXML
    private VBox aiPreviewContainer;
    @FXML
    private VBox aiDraftsList;

    private Formulaire currentQuiz;
    private Question editingQuestion = null;
    private ServiceQuestion serviceQuestion = new ServiceQuestion();
    private GroqService groqService = new GroqService();

    @FXML
    public void initialize() {
        cbType.getItems().addAll("Choix Multiple", "Vrai/Faux", "Texte");
        cbType.getSelectionModel().selectFirst();

        // Professional Touch: Character count listener
        taAIContext.textProperty().addListener((observable, oldValue, newValue) -> {
            int count = newValue != null ? newValue.length() : 0;
            lblCharCount.setText(count + " caractères");
            if (count > 2000) {
                lblCharCount.setStyle("-fx-text-fill: #dc2626;");
            } else {
                lblCharCount.setStyle("-fx-text-fill: #94A3B8;");
            }
        });
    }

    public void initData(Formulaire f) {
        this.currentQuiz = f;
        lblQuizTitle.setText("Quiz : " + f.getTitre());
        lblQuizDesc.setText(f.getDescription());
        lblQuizTime.setText("⏱ " + f.getTempsLimite() + " minutes");
        reloadQuestions();
    }

    public void reloadQuestions() {
            listContainer.getChildren().clear();
        try {
            List<Question> questions = serviceQuestion.getQuestionsByFormulaire(currentQuiz.getId());
            lblQuestionCount.setText("❓ " + questions.size() + " questions");
            
            int index = 1;
            for (Question q : questions) {
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/QuestionListItem.fxml"));
                HBox item = loader.load();
                
                QuestionListItemController controller = loader.getController();
                controller.setData(q, index++, this);
                
                listContainer.getChildren().add(item);
            }
        } catch (SQLException | IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleEnregistrer() {
        // Reset styles
        ValidationUtils.clearErrorStyle(tfQuestionText);
        ValidationUtils.clearErrorStyle(tfPoints);
        ValidationUtils.clearErrorStyle(tfCorrectAnswer);

        String text = tfQuestionText.getText();
        String type = cbType.getValue();
        String pointsStr = tfPoints.getText();
        String answer = tfCorrectAnswer.getText();

        // Validation Question Text
        if (!ValidationUtils.isValidLength(text, 5, 1000)) {
            ValidationUtils.setErrorStyle(tfQuestionText);
            AlertUtils.showError("Validation échouée", "Le texte de la question doit contenir entre 5 et 1000 caractères.");
            return;
        }

        // Validation Points
        if (!ValidationUtils.isPositive(pointsStr)) {
            ValidationUtils.setErrorStyle(tfPoints);
            AlertUtils.showError("Validation échouée", "Les points doivent être un nombre entier positif.");
            return;
        }

        // Validation Answer
        if (ValidationUtils.isEmpty(answer)) {
            ValidationUtils.setErrorStyle(tfCorrectAnswer);
            AlertUtils.showError("Validation échouée", "La réponse correcte est obligatoire.");
            return;
        }

        int points = Integer.parseInt(pointsStr);

        try {
            if (editingQuestion == null) {
                // Ajouter
                Question q = new Question(text, answer, points, type, currentQuiz.getId());
                serviceQuestion.ajouter(q);
                AlertUtils.showInfo("Succès", "Question ajoutée !");
            } else {
                // Modifier
                editingQuestion.setQuestionText(text);
                editingQuestion.setCorrectAnswer(answer);
                editingQuestion.setPoints(points);
                editingQuestion.setType(type);
                serviceQuestion.modifier(editingQuestion);
                AlertUtils.showInfo("Succès", "Question modifiée !");
            }

            handleCancelEdit(); // Reset form
            reloadQuestions();
            
        } catch (SQLException e) {
            e.printStackTrace();
            AlertUtils.showError("Erreur SQL", "Impossible d'enregistrer la question: " + e.getMessage());
        }
    }

    public void editQuestion(Question q) {
        this.editingQuestion = q;
        
        formIcon.setText("🖊️");
        formTitle.setText("Modifier la Question");
        btnSubmit.setText("💾 Enregistrer la Modification");
        btnCancelEdit.setVisible(true);
        btnCancelEdit.setManaged(true);
        
        tfQuestionText.setText(q.getQuestionText());
        cbType.setValue(q.getType());
        tfPoints.setText(String.valueOf(q.getPoints()));
        tfCorrectAnswer.setText(q.getCorrectAnswer());
    }

    @FXML
    private void handleCancelEdit() {
        this.editingQuestion = null;
        
        formIcon.setText("+");
        formTitle.setText("Ajouter une Question");
        btnSubmit.setText("💾 Enregistrer la Question");
        btnCancelEdit.setVisible(false);
        btnCancelEdit.setManaged(false);
        
        tfQuestionText.clear();
        cbType.getSelectionModel().selectFirst();
        tfPoints.clear();
        tfCorrectAnswer.clear();
        
        ValidationUtils.clearErrorStyle(tfQuestionText);
        ValidationUtils.clearErrorStyle(tfPoints);
        ValidationUtils.clearErrorStyle(tfCorrectAnswer);
    }

    @FXML
    private void handleRetour() {
        try {
            Stage stage = (Stage) btnSubmit.getScene().getWindow();
            Parent root = FXMLLoader.load(getClass().getResource("/AdminDashboard.fxml"));
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGenerateAI() {
        String context = taAIContext.getText();
        if (context == null || context.trim().isEmpty()) {
            AlertUtils.showError("Erreur AI", "Veuillez saisir un texte source (résumé, cours...) pour générer des questions.");
            return;
        }

        aiLoader.setVisible(true);
        aiLoader.setManaged(true);
        btnGenerateAI.setDisable(true);
        aiPreviewContainer.setVisible(false);
        aiPreviewContainer.setManaged(false);
        aiDraftsList.getChildren().clear();

        groqService.generateQuestions(context, currentQuiz.getId())
                .thenAccept(questions -> {
                    Platform.runLater(() -> {
                        try {
                            int index = 1;
                            for (Question q : questions) {
                                FXMLLoader loader = new FXMLLoader(getClass().getResource("/GeneratePreviewItem.fxml"));
                                VBox item = loader.load();
                                GeneratePreviewItemController controller = loader.getController();
                                controller.setData(q, index++, this);
                                aiDraftsList.getChildren().add(item);
                            }
                            aiPreviewContainer.setVisible(true);
                            aiPreviewContainer.setManaged(true);
                        } catch (IOException e) {
                            e.printStackTrace();
                            AlertUtils.showError("Erreur UI", "Impossible de charger l'aperçu des questions.");
                        } finally {
                            aiLoader.setVisible(false);
                            aiLoader.setManaged(false);
                            btnGenerateAI.setDisable(false);
                        }
                    });
                })
                .exceptionally(ex -> {
                    Platform.runLater(() -> {
                        aiLoader.setVisible(false);
                        aiLoader.setManaged(false);
                        btnGenerateAI.setDisable(false);
                        AlertUtils.showError("Erreur AI", "La génération a échoué: " + ex.getMessage());
                    });
                    return null;
                });
    }

    public void addQuestionFromAI(Question q) {
        try {
            serviceQuestion.ajouter(q);
            AlertUtils.showInfo("Succès AI", "Question ajoutée avec succès !");
            reloadQuestions();
            
            // Remove the card from preview once added
            aiDraftsList.getChildren().removeIf(node -> {
                VBox card = (VBox) node;
                // Simple check if this card contains the text of the added question
                // (In a real app, we might use a more robust way to match)
                return card.toString().contains(q.getQuestionText());
            });

            if (aiDraftsList.getChildren().isEmpty()) {
                aiPreviewContainer.setVisible(false);
                aiPreviewContainer.setManaged(false);
            }
        } catch (SQLException e) {
            e.printStackTrace();
            AlertUtils.showError("Erreur SQL", "Impossible d'ajouter la question: " + e.getMessage());
        }
    }
}
