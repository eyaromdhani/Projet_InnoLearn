package Controllers;

import Entities.OffreStage;
import Services.ServiceOffreStage;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.*;
import javafx.stage.Stage;
import utils.MyDatabase;

import java.net.URL;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.ResourceBundle;

public class AddOffreController implements Initializable {

    @FXML private TextField  txtTitre;
    @FXML private TextField  txtDomaine;
    @FXML private TextField  txtLieu;
    @FXML private TextField  txtEntreprise;
    @FXML private TextArea   txtDescription;
    @FXML private TextArea   txtCompetences;
    @FXML private TextField  txtDate;
    @FXML private ComboBox<String> cmbStatut;
    @FXML private TextField  txtDuree;
    @FXML private Button     btnSave;
    @FXML private Label      formTitleLabel;

    // Inline error labels
    @FXML private Label errTitre;
    @FXML private Label errDomaine;
    @FXML private Label errLieu;
    @FXML private Label errEntreprise;
    @FXML private Label errDescription;
    @FXML private Label errDuree;

    private ServiceOffreStage serviceOffre;
    private OffreStage offreToEdit;
    private int currentRecruiterId = 8;

    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        serviceOffre = new ServiceOffreStage(MyDatabase.getInstance().getConnection());

        // Auto-fill read-only fields
        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy HH:mm");
        txtDate.setText(sdf.format(new Date()));
        
        cmbStatut.getItems().addAll("Ouverte", "Fermée", "En attente");
        cmbStatut.setValue("Ouverte");

        // Clear errors on user interaction
        attachClearError(txtTitre,       errTitre);
        attachClearError(txtDomaine,     errDomaine);
        attachClearError(txtLieu,        errLieu);
        attachClearError(txtEntreprise,  errEntreprise);
        attachClearError(txtDuree,       errDuree);
        attachClearErrorArea(txtDescription, errDescription);
    }

    // ─── Populate when editing an existing offer ──────────────────────────────
    public void setOffre(OffreStage o) {
        this.offreToEdit = o;
        if (o != null) {
            if (formTitleLabel != null) formTitleLabel.setText("Modifier l'offre de stage");
            txtTitre.setText(o.getTitre());
            txtDomaine.setText(o.getDomaine());
            txtLieu.setText(o.getLieu());
            txtEntreprise.setText(o.getEntreprise());
            txtDescription.setText(o.getDescription());
            txtCompetences.setText(o.getCompetences());
            txtDuree.setText(String.valueOf(o.getDuree()));
            if (o.getStatut() != null && !o.getStatut().isEmpty()) {
                cmbStatut.setValue(o.getStatut());
            } else {
                cmbStatut.setValue("Ouverte");
            }
            if (o.getDate_publication() != null) {
                SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy HH:mm");
                txtDate.setText(sdf.format(o.getDate_publication()));
            }
            if (o.getId_recruteur() != null) {
                this.currentRecruiterId = o.getId_recruteur();
            }
        }
    }

    // ─── Save handler ─────────────────────────────────────────────────────────
    @FXML
    void handleSave(ActionEvent event) {
        if (!validateFields()) return;

        try {
            OffreStage o = (offreToEdit != null) ? offreToEdit : new OffreStage();
            o.setTitre(txtTitre.getText().trim());
            o.setDomaine(txtDomaine.getText().trim());
            o.setLieu(txtLieu.getText().trim());
            o.setEntreprise(txtEntreprise.getText().trim());
            o.setDescription(txtDescription.getText().trim());
            o.setCompetences(txtCompetences.getText().trim());
            o.setDuree(Integer.parseInt(txtDuree.getText().trim()));
            o.setStatut(cmbStatut.getValue());

            if (offreToEdit == null) {
                o.setDate_publication(new Timestamp(System.currentTimeMillis()));
                o.setId_recruteur(currentRecruiterId);
                serviceOffre.ajouter(o);
                showAlert(Alert.AlertType.INFORMATION, "Succès", "L'offre a été publiée avec succès !");
            } else {
                serviceOffre.modifier(o);
                showAlert(Alert.AlertType.INFORMATION, "Succès", "L'offre a été modifiée avec succès !");
            }
            handleRetour(null);

        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur base de données", e.getMessage());
        } catch (NumberFormatException e) {
            showError(txtDuree, errDuree, "⚠ La durée doit être un entier valide.");
        } catch (Exception ex) {
            ex.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur fatale", "Exception inattendue : " + ex.getMessage());
        }
    }

    @FXML
    void handleRetour(ActionEvent event) {
        Stage stage = (Stage) txtTitre.getScene().getWindow();
        stage.close();
    }

    // ─── Inline validation ────────────────────────────────────────────────────
    private boolean validateFields() {
        boolean valid = true;

        // Titre — obligatoire, min 5 car.
        String titre = txtTitre.getText().trim();
        if (titre.isEmpty()) {
            showError(txtTitre, errTitre, "⚠ Le titre est obligatoire.");
            valid = false;
        } else if (titre.length() < 5) {
            showError(txtTitre, errTitre, "⚠ Le titre doit contenir au moins 5 caractères.");
            valid = false;
        } else {
            clearError(txtTitre, errTitre);
        }

        // Domaine — obligatoire
        if (txtDomaine.getText().trim().isEmpty()) {
            showError(txtDomaine, errDomaine, "⚠ Le domaine est obligatoire.");
            valid = false;
        } else {
            clearError(txtDomaine, errDomaine);
        }

        // Lieu — obligatoire
        if (txtLieu.getText().trim().isEmpty()) {
            showError(txtLieu, errLieu, "⚠ Le lieu est obligatoire.");
            valid = false;
        } else {
            clearError(txtLieu, errLieu);
        }

        // Entreprise — obligatoire
        if (txtEntreprise.getText().trim().isEmpty()) {
            showError(txtEntreprise, errEntreprise, "⚠ Le nom de l'entreprise est obligatoire.");
            valid = false;
        } else {
            clearError(txtEntreprise, errEntreprise);
        }

        // Description — obligatoire, min 20 car.
        String desc = txtDescription.getText().trim();
        if (desc.isEmpty()) {
            showError(txtDescription, errDescription, "⚠ La description est obligatoire.");
            valid = false;
        } else if (desc.length() < 20) {
            showError(txtDescription, errDescription, "⚠ Description trop courte (min. 20 caractères).");
            valid = false;
        } else {
            clearError(txtDescription, errDescription);
        }

        // Durée — entier entre 1 et 24
        String dureeStr = txtDuree.getText().trim();
        if (dureeStr.isEmpty()) {
            showError(txtDuree, errDuree, "⚠ La durée est obligatoire.");
            valid = false;
        } else {
            try {
                int duree = Integer.parseInt(dureeStr);
                if (duree < 1 || duree > 24) {
                    showError(txtDuree, errDuree, "⚠ La durée doit être entre 1 et 24 mois.");
                    valid = false;
                } else {
                    clearError(txtDuree, errDuree);
                }
            } catch (NumberFormatException e) {
                showError(txtDuree, errDuree, "⚠ Veuillez saisir un nombre entier.");
                valid = false;
            }
        }

        return valid;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private void showError(Control field, Label errLabel, String message) {
        errLabel.setText(message);
        errLabel.setManaged(true);
        errLabel.setVisible(true);
        field.getStyleClass().removeAll("field-error", "field-ok");
        field.getStyleClass().add("field-error");
    }

    private void clearError(Control field, Label errLabel) {
        errLabel.setManaged(false);
        errLabel.setVisible(false);
        field.getStyleClass().removeAll("field-error", "field-ok");
    }

    private void attachClearError(TextField field, Label errLabel) {
        field.focusedProperty().addListener((obs, wasFocused, isFocused) -> {
            if (isFocused) clearError(field, errLabel);
        });
    }

    private void attachClearErrorArea(TextArea area, Label errLabel) {
        area.focusedProperty().addListener((obs, wasFocused, isFocused) -> {
            if (isFocused) clearError(area, errLabel);
        });
    }

    private void showAlert(Alert.AlertType type, String title, String message) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}
