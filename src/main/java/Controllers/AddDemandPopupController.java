package Controllers;

import Entities.StageCondidature;
import Services.ServiceStageCondidature;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Label;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import utils.MyDatabase;

import java.sql.Date;
import java.sql.SQLException;
import java.time.LocalDate;

public class AddDemandPopupController {

    @FXML private TextField txtTitle;
    @FXML private TextField txtDomain;
    @FXML private TextField txtCompetences;
    @FXML private TextArea  txtDescription;
    @FXML private TextArea  txtMotivation;
    @FXML private Label     lblCVName;

    // Inline error labels
    @FXML private Label errTitle;
    @FXML private Label errDomain;
    @FXML private Label errDescription;

    // Character counter
    @FXML private Label charCounter;

    private ServiceStageCondidature serviceCandidature;
    private StagesController parentController;
    private static final int MOCK_STUDENT_ID = 10;
    private static final int MAX_DESC = 500;
    private String selectedCVPath = null;

    public void setParentController(StagesController parentController) {
        this.parentController = parentController;
        this.serviceCandidature = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());
        
        preFillFromProfile();
    }
    
    private void preFillFromProfile() {
        try {
            StageCondidature profile = serviceCandidature.getProfileEtudiant(MOCK_STUDENT_ID);
            if (profile != null) {
                if (profile.getDomaine() != null) txtDomain.setText(profile.getDomaine());
                if (profile.getCompetences() != null) txtCompetences.setText(profile.getCompetences());
                if (profile.getDescription() != null) txtDescription.setText(profile.getDescription());
                if (profile.getLettre_motivation() != null) txtMotivation.setText(profile.getLettre_motivation());
                
                if (profile.getCv() != null) {
                    selectedCVPath = profile.getCv();
                    java.io.File f = new java.io.File(selectedCVPath);
                    if (f.exists()) {
                        lblCVName.setText(f.getName());
                    }
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @FXML
    public void initialize() {
        // Character counter for description
        txtDescription.textProperty().addListener((obs, oldText, newText) -> {
            int len = newText.length();
            charCounter.setText(len + " / " + MAX_DESC);

            // Color feedback
            charCounter.getStyleClass().removeAll("warning", "danger");
            if (len > MAX_DESC) {
                charCounter.getStyleClass().add("danger");
            } else if (len > (MAX_DESC * 0.8)) {
                charCounter.getStyleClass().add("warning");
            }
        });

        // Clear errors on focus
        attachClearError(txtTitle,  errTitle);
        attachClearError(txtDomain, errDomain);
        txtDescription.focusedProperty().addListener((obs, wasFocused, isFocused) -> {
            if (isFocused) clearError(txtDescription, errDescription);
        });
    }
    
    @FXML
    private void handleUploadCV(ActionEvent event) {
        javafx.stage.FileChooser fileChooser = new javafx.stage.FileChooser();
        fileChooser.setTitle("Choisir votre CV (PDF)");
        fileChooser.getExtensionFilters().add(
            new javafx.stage.FileChooser.ExtensionFilter("Fichiers PDF", "*.pdf")
        );
        
        java.io.File selectedFile = fileChooser.showOpenDialog(txtTitle.getScene().getWindow());
        
        if (selectedFile != null) {
            lblCVName.setText(selectedFile.getName());
            selectedCVPath = selectedFile.getAbsolutePath();
        }
    }

    // ─── Publish handler ──────────────────────────────────────────────────────
    @FXML
    private void handlePublish(ActionEvent event) {
        if (!validateFields()) return;

        try {
            StageCondidature sc = new StageCondidature();
            sc.setType_request("DEMANDE");
            sc.setTitre(txtTitle.getText().trim());
            sc.setDomaine(txtDomain.getText().trim());
            sc.setCompetences(txtCompetences.getText().trim());
            sc.setDescription(txtDescription.getText().trim());
            sc.setLettre_motivation(txtMotivation.getText().trim());
            sc.setCv(selectedCVPath);
            sc.setId_etudiant(MOCK_STUDENT_ID);
            sc.setStatut("EN_ATTENTE");
            sc.setDate_publication(Date.valueOf(LocalDate.now()));

            serviceCandidature.ajouter(sc);

            showAlert(Alert.AlertType.INFORMATION, "Succès", "Votre demande a été publiée avec succès !");

            if (parentController != null) {
                parentController.handleShowDemandes();
            }
            handleCancel(null);

        } catch (SQLException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de publier la demande : " + e.getMessage());
        }
    }

    @FXML
    private void handleCancel(ActionEvent event) {
        Stage stage = (Stage) txtTitle.getScene().getWindow();
        stage.close();
    }

    // ─── Inline validation ────────────────────────────────────────────────────
    private boolean validateFields() {
        boolean valid = true;

        // Titre — obligatoire, min 10 car.
        String title = txtTitle.getText().trim();
        if (title.isEmpty()) {
            showError(txtTitle, errTitle, "⚠ Le titre est obligatoire.");
            valid = false;
        } else if (title.length() < 10) {
            showError(txtTitle, errTitle, "⚠ Le titre doit contenir au moins 10 caractères.");
            valid = false;
        } else {
            clearError(txtTitle, errTitle);
        }

        // Domaine — obligatoire, min 3 car.
        String domain = txtDomain.getText().trim();
        if (domain.isEmpty()) {
            showError(txtDomain, errDomain, "⚠ Le domaine est obligatoire.");
            valid = false;
        } else if (domain.length() < 3) {
            showError(txtDomain, errDomain, "⚠ Le domaine doit contenir au moins 3 caractères.");
            valid = false;
        } else {
            clearError(txtDomain, errDomain);
        }

        // Description — optionnelle, mais max 500 car.
        String desc = txtDescription.getText();
        if (desc.length() > MAX_DESC) {
            showError(txtDescription, errDescription,
                    "⚠ Description trop longue (" + desc.length() + " / " + MAX_DESC + " caractères max).");
            valid = false;
        } else {
            clearError(txtDescription, errDescription);
        }

        return valid;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private void showError(javafx.scene.control.Control field, Label errLabel, String message) {
        errLabel.setText(message);
        errLabel.setManaged(true);
        errLabel.setVisible(true);
        field.getStyleClass().removeAll("field-error", "field-ok");
        field.getStyleClass().add("field-error");
    }

    private void clearError(javafx.scene.control.Control field, Label errLabel) {
        errLabel.setManaged(false);
        errLabel.setVisible(false);
        field.getStyleClass().removeAll("field-error", "field-ok");
    }

    private void attachClearError(TextField field, Label errLabel) {
        field.focusedProperty().addListener((obs, wasFocused, isFocused) -> {
            if (isFocused) clearError(field, errLabel);
        });
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.show();
    }
}
