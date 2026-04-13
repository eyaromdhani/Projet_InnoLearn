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
import services.ServiceQuestion;

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

    private Formulaire currentQuiz;
    private Question editingQuestion = null;
    private ServiceQuestion serviceQuestion = new ServiceQuestion();

    @FXML
    public void initialize() {
        cbType.getItems().addAll("Choix Multiple", "Vrai/Faux", "Texte");
        cbType.getSelectionModel().selectFirst();
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
        String text = tfQuestionText.getText();
        String type = cbType.getValue();
        String pointsStr = tfPoints.getText();
        String answer = tfCorrectAnswer.getText();

        if (text.isEmpty() || pointsStr.isEmpty() || answer.isEmpty()) {
            showAlert("Erreur", "Veuillez remplir tous les champs.");
            return;
        }

        int points;
        try {
            points = Integer.parseInt(pointsStr);
        } catch (NumberFormatException e) {
            showAlert("Erreur", "Les points doivent être un nombre entier.");
            return;
        }

        try {
            if (editingQuestion == null) {
                // Ajouter
                Question q = new Question(text, answer, points, type, currentQuiz.getId());
                serviceQuestion.ajouter(q);
            } else {
                // Modifier
                editingQuestion.setQuestionText(text);
                editingQuestion.setCorrectAnswer(answer);
                editingQuestion.setPoints(points);
                editingQuestion.setType(type);
                serviceQuestion.modifier(editingQuestion);
            }

            handleCancelEdit(); // Reset form
            reloadQuestions();
            
        } catch (SQLException e) {
            e.printStackTrace();
            showAlert("Erreur DB", "Impossible d'enregistrer: " + e.getMessage());
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

    private void showAlert(String title, String content) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.show();
    }
}
