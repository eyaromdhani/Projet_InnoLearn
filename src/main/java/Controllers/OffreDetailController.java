package Controllers;

import Entities.OffreStage;
import Entities.StageCondidature;
import Services.ServiceStageCondidature;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.layout.VBox;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.scene.control.TextArea;
import utils.MyDatabase;
import java.io.File;
import java.sql.Date;
import java.sql.SQLException;
import java.time.LocalDate;

public class OffreDetailController {

    @FXML private Label lblTitle;
    @FXML private Label lblCompany;
    @FXML private Label lblDomaine;
    @FXML private Label lblLieu;
    @FXML private Label lblDuree;
    @FXML private Label lblDate;
    @FXML private Label lblDescription;
    @FXML private Label lblCompetences;
    @FXML private VBox applySection;

    // Apply Form Fields
    @FXML private TextField txtApplyTitle;
    @FXML private TextField txtApplyDomain;
    @FXML private TextField txtApplyCompetences;
    @FXML private TextArea txtApplyProfileDesc;
    @FXML private TextArea txtApplyMotivation;
    @FXML private Label lblCVName;
    @FXML private Button btnApply;

    private String selectedCVPath = "cv_student_1.pdf"; // Default mock

    private OffreStage currentOffre;
    private StagesController parentController;
    private ServiceStageCondidature serviceCandidature;
    private final int MOCK_STUDENT_ID = 10;

    public void setOffre(OffreStage os, StagesController parent) {
        this.currentOffre = os;
        this.parentController = parent;
        this.serviceCandidature = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());

        // Logic for recruiter view: if parent is null, it's called from Dashboard
        if (parent == null && applySection != null) {
            applySection.setVisible(false);
            applySection.setManaged(false);
        }

        if (lblTitle != null) lblTitle.setText(os.getTitre());
        if (lblCompany != null) lblCompany.setText(os.getEntreprise());
        if (lblDomaine != null) lblDomaine.setText(os.getDomaine());
        if (lblLieu != null) lblLieu.setText(os.getLieu());
        if (lblDuree != null) lblDuree.setText(os.getDuree() + " Mois");
        
        if (lblDate != null) {
            if (os.getDate_publication() != null) {
                lblDate.setText("Publié le " + os.getDate_publication().toLocalDateTime().toLocalDate().toString());
            } else {
                lblDate.setText("Date non spécifiée");
            }
        }

        if (lblDescription != null) lblDescription.setText(os.getDescription());
        if (lblCompetences != null) lblCompetences.setText(os.getCompetences());
        
        // Pre-fill apply form
        if (txtApplyTitle != null) txtApplyTitle.setText("Candidature : " + os.getTitre());
        
        try {
            // Fetch student profile (MOCK_STUDENT_ID = 10)
            StageCondidature profile = serviceCandidature.getProfileEtudiant(MOCK_STUDENT_ID);
            if (profile != null) {
                if (txtApplyDomain != null) txtApplyDomain.setText(profile.getDomaine());
                if (txtApplyCompetences != null) txtApplyCompetences.setText(profile.getCompetences());
                if (txtApplyProfileDesc != null) txtApplyProfileDesc.setText(profile.getDescription());
                if (txtApplyMotivation != null) txtApplyMotivation.setText(profile.getLettre_motivation());
                
                // If profile has a CV, update the default path
                if (profile.getCv() != null) {
                    selectedCVPath = profile.getCv();
                    if (lblCVName != null) {
                        File f = new File(selectedCVPath);
                        lblCVName.setText(f.exists() ? f.getName() : "CV du profil");
                    }
                }
            } else {
                // Fallback to offer data if no profile found
                if (txtApplyDomain != null) txtApplyDomain.setText(os.getDomaine());
                if (txtApplyCompetences != null) txtApplyCompetences.setText(os.getCompetences());
            }
        } catch (SQLException e) {
            System.err.println("Error fetching profile for pre-fill: " + e.getMessage());
            // Fallback
            if (txtApplyDomain != null) txtApplyDomain.setText(os.getDomaine());
            if (txtApplyCompetences != null) txtApplyCompetences.setText(os.getCompetences());
        }
    }

    @FXML
    private void handleUploadCV() {
        javafx.stage.FileChooser fileChooser = new javafx.stage.FileChooser();
        fileChooser.setTitle("Choisir votre CV (PDF)");
        fileChooser.getExtensionFilters().add(
            new javafx.stage.FileChooser.ExtensionFilter("Fichiers PDF", "*.pdf")
        );
        
        java.io.File selectedFile = fileChooser.showOpenDialog(btnApply.getScene().getWindow());
        
        if (selectedFile != null) {
            lblCVName.setText(selectedFile.getName());
            selectedCVPath = selectedFile.getAbsolutePath();
        }
    }

    @FXML
    private void handleBack(ActionEvent event) {
        if (parentController != null) {
            parentController.showOffresList();
        } else {
            // Navigate back to Recruiter Dashboard
            try {
                javafx.scene.Parent root = javafx.fxml.FXMLLoader.load(getClass().getResource("/fxml/RecruiterDashboard.fxml"));
                javafx.stage.Stage stage = (javafx.stage.Stage) lblTitle.getScene().getWindow();
                stage.getScene().setRoot(root);
            } catch (java.io.IOException e) {
                e.printStackTrace();
            }
        }
    }

    @FXML
    private void handleApply(ActionEvent event) {
        String title = txtApplyTitle.getText();
        String domain = txtApplyDomain.getText();
        String comps = txtApplyCompetences.getText();
        String profDesc = txtApplyProfileDesc.getText();
        String motivation = txtApplyMotivation.getText();
        
        if (title.isEmpty() || domain.isEmpty() || motivation.isEmpty()) {
            showAlert(Alert.AlertType.WARNING, "Attention", "Veuillez remplir les champs obligatoires (*).");
            return;
        }

        try {
            StageCondidature candidature = new StageCondidature();
            candidature.setType_request("CANDIDATURE");
            candidature.setTitre(title);
            candidature.setDescription(profDesc);
            candidature.setDomaine(domain);
            candidature.setCompetences(comps);
            candidature.setCv(selectedCVPath); 
            candidature.setLettre_motivation(motivation);
            candidature.setDate_publication(Date.valueOf(LocalDate.now()));
            candidature.setStatut("EN_ATTENTE");
            candidature.setId_etudiant(MOCK_STUDENT_ID);
            candidature.setId_offre(currentOffre.getId());

            serviceCandidature.ajouter(candidature);
            
            showAlert(Alert.AlertType.INFORMATION, "Succès", "Votre candidature a été envoyée avec succès !");
            btnApply.setDisable(true);
            btnApply.setText("Candidature Envoyée");
            
        } catch (SQLException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur", "Une erreur est survenue lors de l'envoi de votre candidature.");
        }
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
