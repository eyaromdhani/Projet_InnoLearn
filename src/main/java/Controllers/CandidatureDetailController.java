package Controllers;

import Entities.StageCondidature;
import Services.ServiceStageCondidature;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.stage.Stage;
import utils.MyDatabase;

import java.sql.SQLException;
import java.text.SimpleDateFormat;

public class CandidatureDetailController {

    @FXML private Label   lblCandidatName;
    @FXML private Label   lblCandidatSubtitle;
    @FXML private Label   lblStatut;
    @FXML private Label   lblDomaine;
    @FXML private Label   lblDate;
    @FXML private Label   lblIdOffre;
    @FXML private Label   lblCompetences;
    @FXML private TextArea txtDescription;
    @FXML private TextArea txtMotivation;
    @FXML private Button  btnAccepter;
    @FXML private Button  btnRefuser;

    private StageCondidature candidature;
    private ServiceStageCondidature service;
    private Runnable onActionCallback;

    public void setCandidature(StageCondidature sc) {
        this.candidature = sc;
        this.service = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());
        populate(sc);
    }

    /** Called after setCandidature to refresh the parent list when an action is taken. */
    public void setOnActionCallback(Runnable callback) {
        this.onActionCallback = callback;
    }

    // ─── Populate UI ──────────────────────────────────────────────────────────
    private void populate(StageCondidature sc) {
        lblCandidatName.setText("Candidat #" + sc.getId_etudiant());
        lblCandidatSubtitle.setText("Candidature pour : " + safe(sc.getTitre()));
        lblDomaine.setText(safe(sc.getDomaine(), "Domaine non renseigné"));
        lblIdOffre.setText("Offre #" + (sc.getId_offre() != null ? sc.getId_offre() : "—"));

        if (sc.getDate_publication() != null) {
            SimpleDateFormat sdf = new SimpleDateFormat("dd MMM yyyy");
            lblDate.setText(sdf.format(sc.getDate_publication()));
        } else {
            lblDate.setText("Date inconnue");
        }

        lblCompetences.setText(safe(sc.getCompetences(), "Aucune compétence renseignée"));
        txtDescription.setText(safe(sc.getDescription(), "Aucune description fournie."));
        txtMotivation.setText(safe(sc.getLettre_motivation(), "Aucune lettre de motivation fournie."));

        applyStatutStyle(sc.getStatut());
    }

    private void applyStatutStyle(String statut) {
        if (statut == null) statut = "EN_ATTENTE";
        lblStatut.setText(statut);

        switch (statut.toLowerCase()) {
            case "acceptée":
            case "acceptee":
                lblStatut.setStyle("-fx-background-color: rgba(16,185,129,0.25); -fx-text-fill: #10b981; " +
                        "-fx-font-weight: bold; -fx-font-size: 12px; -fx-padding: 6 14; -fx-background-radius: 20px;");
                // Disable accepter if already accepted
                btnAccepter.setDisable(true);
                break;
            case "refusée":
            case "refusee":
                lblStatut.setStyle("-fx-background-color: rgba(239,68,68,0.22); -fx-text-fill: #ef4444; " +
                        "-fx-font-weight: bold; -fx-font-size: 12px; -fx-padding: 6 14; -fx-background-radius: 20px;");
                btnRefuser.setDisable(true);
                break;
            default:
                lblStatut.setStyle("-fx-background-color: rgba(255,255,255,0.2); -fx-text-fill: white; " +
                        "-fx-font-weight: bold; -fx-font-size: 12px; -fx-padding: 6 14; -fx-background-radius: 20px;");
        }
    }

    // ─── Actions ──────────────────────────────────────────────────────────────
    @FXML
    private void handleAccepter(ActionEvent event) {
        updateStatut("Acceptée");
    }

    @FXML
    private void handleRefuser(ActionEvent event) {
        updateStatut("Refusée");
    }

    private void updateStatut(String newStatut) {
        try {
            candidature.setStatut(newStatut);
            service.modifier(candidature);

            // Refresh badge on popup
            applyStatutStyle(newStatut);
            lblStatut.setText(newStatut);

            showAlert(Alert.AlertType.INFORMATION, "Succès",
                    "Candidature " + newStatut.toLowerCase() + " avec succès.");

            // Refresh parent card list
            if (onActionCallback != null) onActionCallback.run();

        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Échec de la mise à jour : " + e.getMessage());
        }
    }

    @FXML
    private void handleClose(ActionEvent event) {
        ((Stage) lblCandidatName.getScene().getWindow()).close();
    }

    // ─── Utils ────────────────────────────────────────────────────────────────
    private String safe(String s) {
        return (s != null && !s.isBlank()) ? s : "—";
    }

    private String safe(String s, String fallback) {
        return (s != null && !s.isBlank()) ? s : fallback;
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
