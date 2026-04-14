package Controllers;

import Entities.Experience;
import Entities.StageCondidature;
import Services.ServiceExperience;
import Services.ServiceStageCondidature;
import utils.MyDatabase;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.control.Label;
import javafx.scene.layout.VBox;
import javafx.scene.shape.Circle;
import java.net.URL;
import java.sql.SQLException;
import java.util.List;

public class DemandDetailController {

    @FXML private Circle circleStatus;
    @FXML private Label lblStatusTop;
    @FXML private Label lblFullTitle;
    @FXML private Label lblDomaine;
    @FXML private Label lblDescription;
    @FXML private Label lblCompetences;
    @FXML private VBox experienceTimeline;
    @FXML private Label lblDateSidebar;
    @FXML private Label lblIdSidebar;
    @FXML private Label lblStatusSidebar;
    @FXML private javafx.scene.layout.HBox btnViewCV;

    private StageCondidature demand;
    private StagesController parentController;
    private ServiceExperience serviceExperience;

    public void setDemand(StageCondidature demand, StagesController parentController) {
        this.demand = demand;
        this.parentController = parentController;
        this.serviceExperience = new ServiceExperience(MyDatabase.getInstance().getConnection());

        lblFullTitle.setText("Candidature : " + demand.getTitre());
        lblDomaine.setText(demand.getDomaine());
        lblStatusTop.setText(demand.getStatut());
        lblStatusSidebar.setText(demand.getStatut());
        lblDateSidebar.setText("Envoyé le " + demand.getDate_publication());
        lblIdSidebar.setText("ID : #" + demand.getId());
        lblDescription.setText(demand.getDescription());
        lblCompetences.setText(demand.getCompetences());

        // Status circle color
        if (circleStatus != null) {
            if ("ACTIF".equalsIgnoreCase(demand.getStatut()) || "Acceptée".equalsIgnoreCase(demand.getStatut())) {
                circleStatus.setFill(javafx.scene.paint.Color.web("#2ecc71"));
            } else {
                circleStatus.setFill(javafx.scene.paint.Color.web("#f39c12"));
            }
        }

        if (btnViewCV != null) {
            btnViewCV.setOnMouseClicked(e -> handleViewCV());
        }

        loadExperiences();
    }

    private void handleViewCV() {
        if (demand.getCv() != null && !demand.getCv().isEmpty()) {
            try {
                java.io.File file = new java.io.File(demand.getCv());
                if (file.exists()) {
                    java.awt.Desktop.getDesktop().open(file);
                } else {
                    System.err.println("CV file not found: " + demand.getCv());
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    private void loadExperiences() {
        experienceTimeline.getChildren().clear();
        try {
            if (demand.getId_etudiant() != null) {
                List<Experience> experiences = serviceExperience.getParEtudiant(demand.getId_etudiant());
                for (int i = 0; i < experiences.size(); i++) {
                    FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/ExperienceItem.fxml"));
                    Node item = loader.load();
                    
                    ExperienceItemController controller = loader.getController();
                    boolean isLast = (i == experiences.size() - 1);
                    // Pass null for delete callback since we are in "View only" mode
                    controller.setData(experiences.get(i), isLast, null);
                    
                    experienceTimeline.getChildren().add(item);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleBack() {
        if (parentController != null) {
            parentController.showOffresList();
        }
    }
}
