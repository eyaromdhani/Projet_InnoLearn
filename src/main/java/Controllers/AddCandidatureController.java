package Controllers;

import Entities.StageCondidature;
import Services.ServiceStageCondidature;
import javafx.collections.FXCollections;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.stage.Stage;
import utils.MyDatabase;

import java.sql.Date;
import java.sql.SQLException;
import java.time.LocalDate;

public class AddCandidatureController {

    @FXML private TextField          txtTitre;
    @FXML private TextField          txtIdEtudiant;
    @FXML private TextField          txtIdOffre;
    @FXML private TextField          txtDomaine;
    @FXML private ComboBox<String>   comboStatut;
    @FXML private TextArea           txtMotivation;
    @FXML private Button             btnSave;

    // Inline error labels
    @FXML private Label errTitre;
    @FXML private Label errIdEtudiant;
    @FXML private Label errIdOffre;
    @FXML private Label errStatut;

    private ServiceStageCondidature service =
            new ServiceStageCondidature(MyDatabase.getInstance().getConnection());
    private Runnable onSaveCallback;

    @FXML
    public void initialize() {
        comboStatut.setItems(FXCollections.observableArrayList(
                "EN_ATTENTE", "acceptée", "refusée"
        ));
        comboStatut.setValue("EN_ATTENTE");

        // Clear errors on focus
        attachClearError(txtTitre,      errTitre);
        attachClearError(txtIdEtudiant, errIdEtudiant);
        attachClearError(txtIdOffre,    errIdOffre);
    }

    public void setOnSaveCallback(Runnable callback) {
        this.onSaveCallback = callback;
    }

    // ─── Save handler ─────────────────────────────────────────────────────────
    @FXML
    void handleSave(ActionEvent event) {
        if (!validateFields()) return;

        try {
            int idEtudiant = Integer.parseInt(txtIdEtudiant.getText().trim());

            Integer idOffre = null;
            String idOffreStr = txtIdOffre.getText().trim();
            if (!idOffreStr.isEmpty()) {
                idOffre = Integer.parseInt(idOffreStr);
            }

            StageCondidature c = new StageCondidature();
            c.setTitre(txtTitre.getText().trim());
            c.setId_etudiant(idEtudiant);
            c.setId_offre(idOffre);
            c.setDomaine(txtDomaine.getText().trim());
            c.setStatut(comboStatut.getValue());
            c.setLettre_motivation(txtMotivation.getText().trim());
            c.setDate_publication(Date.valueOf(LocalDate.now()));
            c.setType_request("CANDIDATURE");
            c.setCompetences("");
            c.setDescription("");

            service.ajouter(c);
            showAlert(Alert.AlertType.INFORMATION, "Succès", "Candidature ajoutée avec succès !");

            if (onSaveCallback != null) onSaveCallback.run();
            ((Stage) btnSave.getScene().getWindow()).close();

        } catch (NumberFormatException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "ID Étudiant / ID Offre doivent être des entiers.");
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur base de données", e.getMessage());
        }
    }

    @FXML
    void handleCancel(ActionEvent event) {
        ((Stage) btnSave.getScene().getWindow()).close();
    }

    // ─── Inline validation ────────────────────────────────────────────────────
    private boolean validateFields() {
        boolean valid = true;

        // Titre — obligatoire, min 3 car.
        String titre = txtTitre.getText().trim();
        if (titre.isEmpty()) {
            showError(txtTitre, errTitre, "⚠ Le titre est obligatoire.");
            valid = false;
        } else if (titre.length() < 3) {
            showError(txtTitre, errTitre, "⚠ Le titre doit contenir au moins 3 caractères.");
            valid = false;
        } else {
            clearError(txtTitre, errTitre);
        }

        // ID Étudiant — obligatoire, entier > 0
        String idEtStr = txtIdEtudiant.getText().trim();
        if (idEtStr.isEmpty()) {
            showError(txtIdEtudiant, errIdEtudiant, "⚠ L'ID étudiant est obligatoire.");
            valid = false;
        } else {
            try {
                int idEt = Integer.parseInt(idEtStr);
                if (idEt <= 0) {
                    showError(txtIdEtudiant, errIdEtudiant, "⚠ L'ID doit être un entier positif.");
                    valid = false;
                } else {
                    clearError(txtIdEtudiant, errIdEtudiant);
                }
            } catch (NumberFormatException e) {
                showError(txtIdEtudiant, errIdEtudiant, "⚠ Veuillez saisir un nombre entier.");
                valid = false;
            }
        }

        // ID Offre — optionnel, mais doit être entier > 0 si renseigné
        String idOfStr = txtIdOffre.getText().trim();
        if (!idOfStr.isEmpty()) {
            try {
                int idOf = Integer.parseInt(idOfStr);
                if (idOf <= 0) {
                    showError(txtIdOffre, errIdOffre, "⚠ L'ID offre doit être positif.");
                    valid = false;
                } else {
                    clearError(txtIdOffre, errIdOffre);
                }
            } catch (NumberFormatException e) {
                showError(txtIdOffre, errIdOffre, "⚠ L'ID offre doit être un entier.");
                valid = false;
            }
        } else {
            clearError(txtIdOffre, errIdOffre);
        }

        // Statut — obligatoire
        if (comboStatut.getValue() == null || comboStatut.getValue().isEmpty()) {
            showError(comboStatut, errStatut, "⚠ Veuillez sélectionner un statut.");
            valid = false;
        } else {
            clearError(comboStatut, errStatut);
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
        alert.showAndWait();
    }
}
