package Controllers;

import Entities.OffreStage;
import Entities.StageCondidature;
import Services.ServiceOffreStage;
import Services.ServiceStageCondidature;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Label;
import javafx.scene.layout.FlowPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.scene.layout.Region;
import javafx.scene.layout.StackPane;
import javafx.scene.control.ScrollPane;
import javafx.scene.control.Button;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Node;
import javafx.scene.paint.Color;
import javafx.scene.paint.ImagePattern;
import javafx.scene.shape.Circle;
import javafx.scene.shape.SVGPath;
import javafx.scene.image.Image;
import utils.MyDatabase;
import javafx.stage.Stage;

import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;

public class StagesController implements Initializable {

    @FXML private Circle heroCircle;
    @FXML private FlowPane contentArea;
    @FXML private HBox tabOffres;
    @FXML private HBox tabDemandes;
    @FXML private HBox tabProfile;

    @FXML private NavbarController navbarController;
    @FXML private ScrollPane scrollPaneContent;
    
    private Node listViewBackup;

    private ServiceOffreStage serviceOffre;
    private ServiceStageCondidature serviceDemande;

    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        // Highlight active link in navbar
        if (navbarController != null) {
            navbarController.setActiveLink("Stages");
        }

        // Initialize Services
        serviceOffre = new ServiceOffreStage(MyDatabase.getInstance().getConnection());
        serviceDemande = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());

        // Load Hero Image
        try {
            URL imageResource = getClass().getResource("/assets/stages_hero.png");
            if (imageResource != null) {
                heroCircle.setFill(new ImagePattern(new Image(imageResource.toExternalForm())));
            }
        } catch (Exception e) {
            System.err.println("Hero image error: " + e.getMessage());
        }

        // Show Offres by default
        handleShowOffres();
        
        // Backup the list view for later returning
        listViewBackup = scrollPaneContent.getContent();
    }
    
    public void showOffresList() {
        if (listViewBackup != null) {
            scrollPaneContent.setContent(listViewBackup);
            handleShowOffres(); // Refresh
        }
    }

    private void showOffreDetail(OffreStage os) {
        System.out.println("Switching to Detail View for: " + os.getTitre());
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/OffreDetail.fxml"));
            Parent detailView = loader.load();
            
            OffreDetailController controller = loader.getController();
            controller.setOffre(os, this);
            
            scrollPaneContent.setContent(detailView);
            scrollPaneContent.setVvalue(0); // Scroll to top
        } catch (Exception e) {
            System.err.println("Error loading OffreDetail.fxml: " + e.getMessage());
            e.printStackTrace();
        }
    }

    @FXML
    public void handleShowOffres() {
        restoreListView();
        setActiveTab(tabOffres);
        loadOffres();
    }

    @FXML
    public void handleShowDemandes() {
        restoreListView();
        setActiveTab(tabDemandes);
        loadDemandes();
    }

    private void restoreListView() {
        if (listViewBackup != null && scrollPaneContent.getContent() != listViewBackup) {
            scrollPaneContent.setContent(listViewBackup);
        }
    }

    @FXML
    public void handleShowProfile() {
        setActiveTab(tabProfile);
        loadProfile();
    }

    private void setActiveTab(HBox active) {
        // Reset all tabs
        tabOffres.getStyleClass().remove("hero-tab-item-active");
        tabDemandes.getStyleClass().remove("hero-tab-item-active");
        tabProfile.getStyleClass().remove("hero-tab-item-active");
        
        // Activate selected
        active.getStyleClass().add("hero-tab-item-active");
    }

    private void loadProfile() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/Profile.fxml"));
            Parent profileView = loader.load();
            scrollPaneContent.setContent(profileView);
            scrollPaneContent.setVvalue(0);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleAddNewDemand() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/AddDemandPopup.fxml"));
            Parent root = loader.load();
            
            AddDemandPopupController controller = loader.getController();
            controller.setParentController(this);
            
            Stage stage = new Stage();
            stage.setTitle("Nouvelle Demande");
            stage.initModality(javafx.stage.Modality.APPLICATION_MODAL);
            
            javafx.scene.Scene scene = new javafx.scene.Scene(root);
            // Apply CSS if needed
            scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
            
            stage.setScene(scene);
            stage.show();
        } catch (Exception e) {
            e.printStackTrace();
            System.err.println("Error opening AddDemandPopup: " + e.getMessage());
        }
    }

    private void showDemandDetail(StageCondidature sc) {
        System.out.println("Switching to Demand Detail View for Student: " + sc.getId_etudiant());
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/DemandDetail.fxml"));
            Parent detailView = loader.load();
            
            System.out.println("FXML Loaded successfully, setting controller data...");
            DemandDetailController controller = loader.getController();
            controller.setDemand(sc, this);
            
            scrollPaneContent.setContent(detailView);
            scrollPaneContent.setVvalue(0);
            System.out.println("Content successfully updated to Demand Detail View.");
        } catch (Exception e) {
            System.err.println("CRITICAL ERROR loading DemandDetail.fxml: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void loadOffres() {
        contentArea.getChildren().clear();
        try {
            List<OffreStage> offres = serviceOffre.afficherAll();
            for (OffreStage os : offres) {
                VBox card = createCard(
                    os.getTitre(),
                    os.getEntreprise(),
                    os.getDomaine(),
                    os.getLieu(),
                    "M21 16.5c0 .38-.21.71-.53.88l-7.97 4.27a1.006 1.006 0 01-.94 0l-7.97-4.27A1 1 0 013 16.5V7.5c0-.38.21-.71.53-.88l7.97-4.27a1.006 1.006 0 01.94 0l7.97 4.27c.32.17.53.5.53.88v9z", // Briefcase icon
                    "#3498db",
                    "Voir Détails",
                    e -> showOffreDetail(os)
                );
                
                // Set action on whole card as well for better accessibility
                card.setOnMouseClicked(e -> showOffreDetail(os));
                
                contentArea.getChildren().add(card);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void loadDemandes() {
        contentArea.getChildren().clear();
        try {
            // Add a special "Add New Demand" card at the beginning
            VBox addCard = createCard(
                "Publier une Demande",
                "Créez votre profil public",
                "Nouveau",
                "Ajouter",
                "M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z", // Plus icon
                "#6358ff",
                "Commencer",
                e -> handleAddNewDemand()
            );
            addCard.setStyle(addCard.getStyle() + "; -fx-border-style: dashed; -fx-border-width: 2px; -fx-border-color: #6358ff;");
            contentArea.getChildren().add(addCard);

            // Updated to use the filtered method
            List<StageCondidature> demandes = serviceDemande.afficherDemandes();
            for (StageCondidature sc : demandes) {
                VBox card = createCard(
                    sc.getTitre(),
                    "Profil Étudiant",
                    sc.getDomaine(),
                    sc.getStatut(),
                    "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z", // Person icon
                    "#2ecc71",
                    "Voir Détails",
                    e -> showDemandDetail(sc)
                );
                
                card.setOnMouseClicked(e -> showDemandDetail(sc));
                contentArea.getChildren().add(card);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private VBox createCard(String title, String subtitle, String category, String meta, String svgContent, String color, String btnText, javafx.event.EventHandler<javafx.event.ActionEvent> onBtnAction) {
        VBox card = new VBox(15);
        card.getStyleClass().add("item-card");

        // Header: Icon + Badge
        HBox header = new HBox();
        header.setSpacing(10);
        
        StackPane iconContainer = new StackPane();
        iconContainer.getStyleClass().add("item-card-icon-container");
        iconContainer.setStyle("-fx-background-color: " + color + "1A;"); // 10% opacity hex
        
        SVGPath icon = new SVGPath();
        icon.setContent(svgContent);
        icon.setFill(Color.web(color));
        icon.setScaleX(0.8);
        icon.setScaleY(0.8);
        
        iconContainer.getChildren().add(icon);
        
        Region spacer = new Region();
        HBox.setHgrow(spacer, javafx.scene.layout.Priority.ALWAYS);
        
        Label badge = new Label(category);
        badge.getStyleClass().add("item-card-badge");
        
        header.getChildren().addAll(iconContainer, spacer, badge);

        // Content
        VBox metaBox = new VBox(5);
        Label titleLabel = new Label(title);
        titleLabel.getStyleClass().add("item-card-title");
        titleLabel.setWrapText(true);
        
        Label subLabel = new Label(subtitle);
        subLabel.getStyleClass().add("item-card-subtitle");
        
        metaBox.getChildren().addAll(titleLabel, subLabel);

        // Footer: Meta Info (Location or Status)
        HBox footer = new HBox(8);
        footer.setAlignment(javafx.geometry.Pos.CENTER_LEFT);
        SVGPath pinIcon = new SVGPath();
        pinIcon.setContent("M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z");
        pinIcon.setFill(Color.web("#7f8c8d"));
        pinIcon.setScaleX(0.6);
        pinIcon.setScaleY(0.6);
        
        Label footerLabel = new Label(meta);
        footerLabel.setStyle("-fx-text-fill: #7f8c8d; -fx-font-size: 12px;");
        
        footer.getChildren().addAll(pinIcon, footerLabel);

        // Call to action button
        Button btnDetail = new Button(btnText);
        btnDetail.getStyleClass().add("btn-card-detail");
        btnDetail.setMaxWidth(Double.MAX_VALUE); // Full width button
        btnDetail.setCursor(javafx.scene.Cursor.HAND);
        
        if (onBtnAction != null) {
            btnDetail.setOnAction(onBtnAction);
        } else {
            btnDetail.setDisable(true); // Disable if no action
            btnDetail.setOpacity(0.5);
        }

        card.getChildren().addAll(header, metaBox, footer, btnDetail);
        
        return card;
    }
}
