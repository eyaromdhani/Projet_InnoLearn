package com.innolearn.controller;
 
import com.innolearn.model.Project;
import com.innolearn.service.ProjectService;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.stage.Stage;
import javafx.application.Platform;
import java.sql.SQLException;
import java.sql.Date;
import java.time.LocalDate;
import com.innolearn.service.CohereService;

public class ProjectFormController {

    @FXML private TextField txtTitle;
    @FXML private TextArea txtDescription;
    @FXML private ComboBox<String> comboStatus;
    @FXML private DatePicker dateStart;
    @FXML private DatePicker dateEnd;

    // Dynamic Header & Button
    @FXML private Label lblHeaderTitle;
    @FXML private Label lblHeaderSubtitle;
    @FXML private Button btnSave;

    // Error Labels
    @FXML private Label errTitle;
    @FXML private Label errDescription;
    @FXML private Label errDate;

    private ProjectService projectService = new ProjectService();
    private CohereService cohereService = new CohereService();
    private Project projectToEdit;
    private Runnable onSaveCallback;

    // Shared error border style
    private static final String ERROR_STYLE  = "-fx-background-color: white; -fx-padding: 12 15; -fx-background-radius: 10; -fx-border-color: #ef4444; -fx-border-radius: 10; -fx-border-width: 2; -fx-font-size: 14px;";
    private static final String NORMAL_STYLE = "-fx-background-color: white; -fx-padding: 12 15; -fx-background-radius: 10; -fx-border-color: #e5e7eb; -fx-border-radius: 10; -fx-border-width: 1.5; -fx-font-size: 14px;";
    private static final String TEXTAREA_ERROR_STYLE  = "-fx-control-inner-background: white; -fx-background-radius: 10; -fx-border-color: #ef4444; -fx-border-radius: 10; -fx-border-width: 2; -fx-font-size: 14px;";
    private static final String TEXTAREA_NORMAL_STYLE = "-fx-control-inner-background: white; -fx-background-radius: 10; -fx-border-color: #e5e7eb; -fx-border-radius: 10; -fx-border-width: 1.5; -fx-font-size: 14px;";

    @FXML
    public void initialize() {
        comboStatus.getItems().addAll("actif", "non actif", "brouillon");
        comboStatus.getSelectionModel().select(0);
        dateStart.setValue(LocalDate.now());
    }

    /**
     * Call this with a project to enter EDIT mode; call with null for ADD mode.
     */
    public void setProject(Project project) {
        this.projectToEdit = project;
        if (project != null) {
            // EDIT MODE
            lblHeaderTitle.setText("Modifier le Projet");
            lblHeaderSubtitle.setText("Modifiez les informations ci-dessous et enregistrez vos changements.");
            btnSave.setText("✏  Enregistrer les modifications");

            txtTitle.setText(project.getTitle());
            txtDescription.setText(project.getDescription());
            comboStatus.getSelectionModel().select(project.getStatus());
            if (project.getStartDate() != null) dateStart.setValue(project.getStartDate().toLocalDate());
            if (project.getEndDate()   != null) dateEnd.setValue(project.getEndDate().toLocalDate());
        }
        // else: already in ADD mode from initialize defaults
    }

    public void setOnSaveCallback(Runnable callback) {
        this.onSaveCallback = callback;
    }

    @FXML
    private void handleSave() {
        if (!validateInputs()) return;

        btnSave.setDisable(true);
        btnSave.setText("Classification en cours...");

        final String title = txtTitle.getText().trim();
        final String desc = txtDescription.getText().trim();
        final String status = comboStatus.getValue();
        final LocalDate sDate = dateStart.getValue();
        final LocalDate eDate = dateEnd.getValue();

        new Thread(() -> {
            Project p = (projectToEdit == null) ? new Project() : projectToEdit;
            p.setTitle(title);
            p.setDescription(desc);
            p.setStatus(status);
            
            // Auto-classify using Cohere AI synchronously on background thread
            String classification = cohereService.classifyProjectDifficulty(title, desc);
            p.setDifficulty(classification);

            Platform.runLater(() -> {
                try {
                    p.setStartDate(Date.valueOf(sDate));
                    if (eDate != null) p.setEndDate(Date.valueOf(eDate));

                    if (projectToEdit == null) {
                        projectService.addProject(p);
                    } else {
                        projectService.updateProject(p);
                    }

                    if (onSaveCallback != null) onSaveCallback.run();
                    closeWindow();
                } catch (SQLException e) {
                    btnSave.setDisable(false);
                    btnSave.setText(projectToEdit == null ? "✚  Ajouter le Projet" : "✏  Enregistrer les modifications");
                    showAlert("Erreur base de données", "Impossible d'enregistrer le projet :\n" + e.getMessage());
                }
            });
        }).start();
    }

    private boolean validateInputs() {
        resetStyles();
        boolean valid = true;

        // Title
        String title = txtTitle.getText();
        if (title == null || title.trim().isEmpty()) {
            errTitle.setText("⚠  Le titre est obligatoire.");
            txtTitle.setStyle(ERROR_STYLE);
            valid = false;
        } else if (title.trim().length() < 3) {
            errTitle.setText("⚠  Le titre doit avoir au moins 3 caractères.");
            txtTitle.setStyle(ERROR_STYLE);
            valid = false;
        } else {
            errTitle.setText("");
        }

        // Description
        String desc = txtDescription.getText();
        if (desc == null || desc.trim().isEmpty()) {
            errDescription.setText("⚠  La description est obligatoire.");
            txtDescription.setStyle(TEXTAREA_ERROR_STYLE);
            valid = false;
        } else {
            errDescription.setText("");
        }

        // Start date
        if (dateStart.getValue() == null) {
            errDate.setText("⚠  La date de début est obligatoire.");
            valid = false;
        } else if (dateEnd.getValue() != null && dateEnd.getValue().isBefore(dateStart.getValue())) {
            errDate.setText("⚠  La date de fin ne peut pas être avant la date de début.");
            valid = false;
        } else {
            errDate.setText("");
        }

        return valid;
    }

    private void resetStyles() {
        txtTitle.setStyle(NORMAL_STYLE);
        txtDescription.setStyle(TEXTAREA_NORMAL_STYLE);
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
}
