package com.innolearn.controller;
 
import com.innolearn.model.Depot;
import com.innolearn.model.Project;
import com.innolearn.service.CohereService;
import com.innolearn.service.DepotService;
import com.innolearn.service.ProjectService;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.CheckBoxListCell;
import javafx.stage.Stage;
import javafx.stage.FileChooser;
import javafx.util.StringConverter;
import javafx.beans.property.BooleanProperty;
import javafx.beans.property.SimpleBooleanProperty;
import java.io.File;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;

public class DepotFormController {

    @FXML private TextField txtTitle;
    @FXML private TextField txtStudent;
    @FXML private ComboBox<String> comboType;
    @FXML private TextField txtFilePath;
    @FXML private TextField txtNewTask;
    @FXML private ListView<TaskItem> listTodos;

    // Dynamic Header & Button
    @FXML private Label lblHeaderTitle;
    @FXML private Label lblHeaderSubtitle;
    @FXML private Button btnSave;

    // Error Labels
    @FXML private Label errTitle;
    @FXML private Label errStudent;
    @FXML private Label errFilePath;

    private DepotService depotService = new DepotService();
    private CohereService cohereService = new CohereService();
    private ProjectService projectService = new ProjectService();
    private int projectId;
    private Project currentProject;
    private Runnable onSaveCallback;
    private ObservableList<TaskItem> tasks = FXCollections.observableArrayList();

    private static final String ERROR_STYLE  = "-fx-background-color: white; -fx-padding: 12 15; -fx-background-radius: 10; -fx-border-color: #ef4444; -fx-border-radius: 10; -fx-border-width: 2; -fx-font-size: 14px;";
    private static final String NORMAL_STYLE = "-fx-background-color: white; -fx-padding: 12 15; -fx-background-radius: 10; -fx-border-color: #e5e7eb; -fx-border-radius: 10; -fx-border-width: 1.5; -fx-font-size: 14px;";

    @FXML
    public void initialize() {
        comboType.getItems().addAll("Code", "PDF", "Vidéo", "Archive ZIP", "Autre");
        comboType.getSelectionModel().select(0);

        listTodos.setItems(tasks);
        listTodos.setCellFactory(CheckBoxListCell.forListView(TaskItem::doneProperty, new StringConverter<TaskItem>() {
            @Override
            public String toString(TaskItem object) {
                return object.getText();
            }
            @Override
            public TaskItem fromString(String string) {
                return null;
            }
        }));
    }

    public void setProjectId(int projectId) {
        this.projectId = projectId;
        try {
            this.currentProject = projectService.getAllProjects().stream()
                    .filter(p -> p.getId() == projectId)
                    .findFirst()
                    .orElse(null);
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public void setOnSaveCallback(Runnable callback) {
        this.onSaveCallback = callback;
    }

    @FXML
    private void handleAddTask() {
        String text = txtNewTask.getText().trim();
        if (!text.isEmpty()) {
            tasks.add(new TaskItem(text));
            txtNewTask.clear();
        }
    }

    @FXML
    private void handleBrowse() {
        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Sélectionner un fichier");
        File file = fileChooser.showOpenDialog(txtTitle.getScene().getWindow());
        if (file != null) {
            txtFilePath.setText(file.getAbsolutePath());
        }
    }

    @FXML
    private void handleSave() {
        if (!validateInputs()) return;

        btnSave.setDisable(true);
        btnSave.setText("⏳ Analyse IA en cours...");

        new Thread(() -> {
            try {
                // Prepare context for AI
                List<String> checklist = tasks.stream()
                        .map(t -> t.getText() + " - " + (t.isDone() ? "[DONE]" : "[NOT DONE]"))
                        .collect(Collectors.toList());

                String projectTitle = currentProject != null ? currentProject.getTitle() : "Projet";
                String projectDesc = currentProject != null ? currentProject.getDescription() : "";
                String projectDiff = currentProject != null ? currentProject.getDifficulty() : "Inconnue";
                String projectStatus = currentProject != null ? currentProject.getStatus() : "En cours";

                // Call AI
                String aiResponse = cohereService.analyzeDepotCompletion(
                        projectTitle, projectDesc, projectDiff, projectStatus,
                        txtTitle.getText().trim(), comboType.getValue(),
                        txtStudent.getText().trim(), checklist
                );

                // Parse response
                int score = 0;
                try {
                    String[] lines = aiResponse.split("\n");
                    for (String line : lines) {
                        if (line.startsWith("SCORE:")) {
                            score = Integer.parseInt(line.replace("SCORE:", "").trim().replaceAll("[^0-9]", ""));
                        }
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }

                // Save to DB
                Depot d = new Depot();
                d.setTitle(txtTitle.getText().trim());
                d.setStudentName(txtStudent.getText().trim());
                d.setType(comboType.getValue());
                d.setFilePath(txtFilePath.getText().trim());
                d.setProjectId(projectId);
                d.setUploadedAt(new Timestamp(System.currentTimeMillis()));
                d.setAiResult(aiResponse);
                d.setAiScore(score);
                d.setTodoStatus(score >= 100 ? "Done" : "Doing");

                depotService.addDepot(d);

                javafx.application.Platform.runLater(() -> {
                    if (onSaveCallback != null) onSaveCallback.run();
                    closeWindow();
                });

            } catch (Exception e) {
                e.printStackTrace();
                javafx.application.Platform.runLater(() -> {
                    showAlert("Erreur", "Une erreur est survenue lors de l'analyse ou de l'enregistrement.");
                    btnSave.setDisable(false);
                    btnSave.setText("✚  Soumettre le Dépôt");
                });
            }
        }).start();
    }

    private boolean validateInputs() {
        resetStyles();
        boolean valid = true;
        if (txtTitle.getText().trim().isEmpty()) {
            txtTitle.setStyle(ERROR_STYLE);
            valid = false;
        }
        if (txtStudent.getText().trim().isEmpty()) {
            txtStudent.setStyle(ERROR_STYLE);
            valid = false;
        }
        if (txtFilePath.getText().trim().isEmpty()) {
            txtFilePath.setStyle(ERROR_STYLE);
            valid = false;
        }
        return valid;
    }

    private void resetStyles() {
        txtTitle.setStyle(NORMAL_STYLE);
        txtStudent.setStyle(NORMAL_STYLE);
        txtFilePath.setStyle(NORMAL_STYLE);
    }

    @FXML
    private void handleCancel() {
        closeWindow();
    }

    private void closeWindow() {
        ((Stage) txtTitle.getScene().getWindow()).close();
    }

    private void showAlert(String title, String content) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }

    // Helper class for checklist items
    public static class TaskItem {
        private final String text;
        private final BooleanProperty done = new SimpleBooleanProperty(false);

        public TaskItem(String text) {
            this.text = text;
        }

        public String getText() { return text; }
        public boolean isDone() { return done.get(); }
        public BooleanProperty doneProperty() { return done; }
    }
}
