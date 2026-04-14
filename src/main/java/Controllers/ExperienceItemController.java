package Controllers;

import Entities.Experience;
import Services.ServiceExperience;
import utils.MyDatabase;
import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.shape.Line;
import java.sql.SQLException;

public class ExperienceItemController {

    @FXML private Label lblTitre;
    @FXML private Label lblBadge;
    @FXML private Label lblPeriode;
    @FXML private Label lblEtablissement;
    @FXML private Label lblDescription;
    @FXML private Line timelineConnector;

    private Experience experience;
    private Runnable onDeleteCallback;
    private ServiceExperience serviceExperience;

    public void setData(Experience experience, boolean isLast, Runnable onDeleteCallback) {
        this.experience = experience;
        this.onDeleteCallback = onDeleteCallback;
        this.serviceExperience = new ServiceExperience(MyDatabase.getInstance().getConnection());

        lblTitre.setText(experience.getDomaine());
        lblBadge.setText(experience.getType());
        lblPeriode.setText("📅 " + experience.getAnnee());
        lblEtablissement.setText("🏫 " + (experience.getEtablissement() != null ? experience.getEtablissement() : ""));
        lblDescription.setText(experience.getDescription());

        if (isLast) {
            timelineConnector.setVisible(false);
        }

        // Apply badge color based on type
        if ("Formation".equals(experience.getType())) {
            lblBadge.setStyle("-fx-background-color: #e8f5e9; -fx-text-fill: #2ecc71;");
        } else {
            lblBadge.setStyle("-fx-background-color: #e3f2fd; -fx-text-fill: #3498db;");
        }
    }

    @FXML
    private void handleDelete() {
        try {
            serviceExperience.supprimer(experience.getId());
            if (onDeleteCallback != null) {
                onDeleteCallback.run();
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
