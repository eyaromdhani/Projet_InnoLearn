package Controllers;

import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Label;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Pane;
import javafx.scene.layout.StackPane;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import javafx.fxml.FXMLLoader;
import java.io.IOException;
import java.net.URL;
import java.util.ResourceBundle;

public class NavbarController implements Initializable {

    @FXML private Label linkAccueil;
    @FXML private Label linkCours;
    @FXML private Label linkProjets;
    @FXML private Label linkEvenements;
    @FXML private Label linkStages;
    @FXML private Label linkLivres;
    @FXML private Label linkQuiz;
    @FXML private HBox logoContainer;
    @FXML private StackPane profileInitials;

    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        // Initialization logic if needed
    }

    @FXML
    private void handleProfileClick() {
        javafx.scene.control.ContextMenu contextMenu = new javafx.scene.control.ContextMenu();
        
        javafx.scene.control.MenuItem studentItem = new javafx.scene.control.MenuItem("Espace Étudiant");
        studentItem.setOnAction(e -> handleStagesClick());
        
        javafx.scene.control.MenuItem recruiterItem = new javafx.scene.control.MenuItem("Espace Recruteur");
        recruiterItem.setOnAction(e -> navigateTo("/fxml/RecruiterDashboard.fxml"));
        
        javafx.scene.control.MenuItem adminItem = new javafx.scene.control.MenuItem("Administration");
        adminItem.setOnAction(e -> navigateTo("/fxml/AdminDashboard.fxml"));
        
        contextMenu.getItems().addAll(studentItem, recruiterItem, new javafx.scene.control.SeparatorMenuItem(), adminItem);
        
        contextMenu.show(profileInitials, javafx.geometry.Side.BOTTOM, 0, 10);
    }

    private void navigateTo(String fxmlPath) {
        try {
            Parent root = FXMLLoader.load(getClass().getResource(fxmlPath));
            Stage stage = (Stage) profileInitials.getScene().getWindow();
            stage.getScene().setRoot(root);
        } catch (IOException e) {
            e.printStackTrace();
            System.err.println("Error navigating to " + fxmlPath + ": " + e.getMessage());
        }
    }

    @FXML
    private void handleLogoClick() {
        try {
            // Load Home.fxml
            Parent root = FXMLLoader.load(getClass().getResource("/fxml/Home.fxml"));
            
            // Get the current stage
            Stage stage = (Stage) logoContainer.getScene().getWindow();
            
            // Set the new scene root
            stage.getScene().setRoot(root);
            
        } catch (IOException e) {
            e.printStackTrace();
            System.err.println("Error loading Home.fxml: " + e.getMessage());
        }
    }

    @FXML
    private void handleStagesClick() {
        try {
            // Load Stages.fxml
            Parent root = FXMLLoader.load(getClass().getResource("/fxml/Stages.fxml"));
            
            // Get the current stage
            Stage stage = (Stage) logoContainer.getScene().getWindow();
            
            // Set the new scene root
            stage.getScene().setRoot(root);
            
        } catch (IOException e) {
            e.printStackTrace();
            System.err.println("Error loading Stages.fxml: " + e.getMessage());
        }
    }

    /**
     * Highlights the active link in the navbar.
     * @param activeLinkName The name of the link to highlight (e.g., "Stages").
     */
    public void setActiveLink(String activeLinkName) {
        resetLinks();
        switch (activeLinkName) {
            case "Accueil": linkAccueil.getStyleClass().add("nav-link-active"); break;
            case "Cours": linkCours.getStyleClass().add("nav-link-active"); break;
            case "Projets": linkProjets.getStyleClass().add("nav-link-active"); break;
            case "Evenements": linkEvenements.getStyleClass().add("nav-link-active"); break;
            case "Stages": linkStages.getStyleClass().add("nav-link-active"); break;
            case "Livres": linkLivres.getStyleClass().add("nav-link-active"); break;
            case "Quiz": linkQuiz.getStyleClass().add("nav-link-active"); break;
        }
    }

    private void resetLinks() {
        linkAccueil.getStyleClass().remove("nav-link-active");
        linkCours.getStyleClass().remove("nav-link-active");
        linkProjets.getStyleClass().remove("nav-link-active");
        linkEvenements.getStyleClass().remove("nav-link-active");
        linkStages.getStyleClass().remove("nav-link-active");
        linkLivres.getStyleClass().remove("nav-link-active");
        linkQuiz.getStyleClass().remove("nav-link-active");
    }
}
