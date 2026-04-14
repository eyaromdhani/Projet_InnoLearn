package Controllers;

import Entities.OffreStage;
import Services.ServiceOffreStage;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.shape.SVGPath;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.stage.StageStyle;
import utils.MyDatabase;

import java.io.IOException;
import java.sql.SQLException;
import java.util.List;
import java.util.stream.Collectors;

public class RecruiterOffresController {

    @FXML private FlowPane cardsContainerMesOffres;
    @FXML private FlowPane cardsContainerAutresOffres;

    private ServiceOffreStage serviceMethod = new ServiceOffreStage(MyDatabase.getInstance().getConnection());
    private final int MOCK_RECRUITER_ID = 8; // Simulated Logged in recruiter

    @FXML
    public void initialize() {
        loadData();
    }

    private void loadData() {
        try {
            List<OffreStage> tous = serviceMethod.afficherAll();
            
            // Mes offres
            List<OffreStage> mesOffres = tous.stream()
                    .filter(o -> o.getId_recruteur() != null && o.getId_recruteur() == MOCK_RECRUITER_ID)
                    .collect(Collectors.toList());
            
            // Autres offres
            List<OffreStage> autresOffres = tous.stream()
                    .filter(o -> o.getId_recruteur() == null || o.getId_recruteur() != MOCK_RECRUITER_ID)
                    .collect(Collectors.toList());
            
            updateDisplayMesOffres(mesOffres);
            updateDisplayAutresOffres(autresOffres);
            
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de charger les offres.", e.getMessage());
        }
    }

    private void updateDisplayMesOffres(List<OffreStage> offres) {
        cardsContainerMesOffres.getChildren().clear();

        // Add "Add New" Card
        VBox addCard = createAddCard();
        cardsContainerMesOffres.getChildren().add(addCard);

        int colorIndex = 0;
        String[] colors = {"recruiter-card-blue", "recruiter-card-purple", "recruiter-card-pink", "recruiter-card-cyan"};

        for (OffreStage o : offres) {
            VBox card = createOfferCard(o, colors[colorIndex % colors.length], true);
            cardsContainerMesOffres.getChildren().add(card);
            colorIndex++;
        }
    }
    
    private void updateDisplayAutresOffres(List<OffreStage> offres) {
        cardsContainerAutresOffres.getChildren().clear();

        int colorIndex = 0;
        String[] colors = {"recruiter-card-blue", "recruiter-card-purple", "recruiter-card-pink", "recruiter-card-cyan"};

        for (OffreStage o : offres) {
            VBox card = createOfferCard(o, colors[colorIndex % colors.length], false);
            cardsContainerAutresOffres.getChildren().add(card);
            colorIndex++;
        }
    }

    private VBox createAddCard() {
        VBox card = new VBox(15);
        card.getStyleClass().addAll("recruiter-offer-card");
        card.setStyle("-fx-background-color: white; -fx-border-color: #6358ff; -fx-border-style: dashed; -fx-border-width: 2px; -fx-border-radius: 15px; -fx-background-radius: 15px;");
        card.setAlignment(javafx.geometry.Pos.CENTER);
        card.setPrefSize(280, 200);
        card.setCursor(javafx.scene.Cursor.HAND);

        SVGPath plusIcon = new SVGPath();
        plusIcon.setContent("M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z");
        plusIcon.setFill(Color.web("#6358ff"));
        plusIcon.setScaleX(1.5);
        plusIcon.setScaleY(1.5);

        Label label = new Label("Publier une Offre");
        label.setStyle("-fx-text-fill: #6358ff; -fx-font-weight: bold; -fx-font-size: 16px;");

        card.getChildren().addAll(plusIcon, label);
        card.setOnMouseClicked(e -> handleAjouter(null));

        return card;
    }

    private VBox createOfferCard(OffreStage o, String colorClass, boolean isOwner) {
        VBox card = new VBox(15);
        card.getStyleClass().addAll("recruiter-offer-card", colorClass);
        card.setPrefSize(280, 200);
        
        HBox tagBox = new HBox();
        Label tag = new Label(o.getDomaine());
        tag.getStyleClass().add("card-tag");
        tagBox.getChildren().add(tag);
        
        VBox content = new VBox(5);
        Label title = new Label(o.getTitre());
        title.getStyleClass().add("card-title-white");
        title.setWrapText(true);
        title.setMaxHeight(50);
        
        Label company = new Label(o.getEntreprise());
        company.getStyleClass().add("card-company-white");
        
        content.getChildren().addAll(title, company);
        
        // Full card is clickable to see details
        card.setCursor(javafx.scene.Cursor.HAND);
        card.setOnMouseClicked(e -> {
            // Traverse the parent hierarchy to see if a Button was clicked
            javafx.scene.Node target = (javafx.scene.Node) e.getTarget();
            boolean isButton = false;
            while (target != null && target != card) {
                if (target instanceof Button) {
                    isButton = true;
                    break;
                }
                target = target.getParent();
            }
            if (!isButton) {
                openOffreDetailView(o);
            }
        });
        
        Region spacer = new Region();
        VBox.setVgrow(spacer, Priority.ALWAYS);
        
        HBox footer = new HBox(10);
        footer.setAlignment(javafx.geometry.Pos.CENTER_RIGHT);
        
        if (isOwner) {
            Button btnEdit = new Button("Modifier");
            btnEdit.setStyle("-fx-background-color: white; -fx-text-fill: #2c3e50; -fx-background-radius: 15px; -fx-padding: 5 15; -fx-font-weight: bold;");
            btnEdit.setCursor(javafx.scene.Cursor.HAND);
            btnEdit.setOnAction(e -> openEditPopup(o));

            Button btnDelete = new Button();
            btnDelete.setStyle("-fx-background-color: #ff4757; -fx-background-radius: 50%; -fx-min-width: 30px; -fx-min-height: 30px;");
            SVGPath trashIcon = new SVGPath();
            trashIcon.setContent("M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z");
            trashIcon.setFill(Color.WHITE);
            trashIcon.setScaleX(0.7);
            trashIcon.setScaleY(0.7);
            btnDelete.setGraphic(trashIcon);
            btnDelete.setCursor(javafx.scene.Cursor.HAND);
            btnDelete.setOnAction(e -> handleSupprimer(o));
            
            footer.getChildren().addAll(btnDelete, btnEdit);
        } else {
            Button btnView = new Button("Consulter");
            btnView.setStyle("-fx-background-color: white; -fx-text-fill: #2c3e50; -fx-background-radius: 15px; -fx-padding: 5 15; -fx-font-weight: bold;");
            btnView.setCursor(javafx.scene.Cursor.HAND);
            btnView.setOnAction(e -> openOffreDetailView(o));
            
            footer.getChildren().add(btnView);
        }
        
        card.getChildren().addAll(tagBox, content, spacer, footer);
        
        return card;
    }

    @FXML
    void handleAjouter(ActionEvent event) {
        openEditPopup(null);
    }

    private void openEditPopup(OffreStage o) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/AddOffreForm.fxml"));
            Parent root = loader.load();
            
            AddOffreController controller = loader.getController();
            controller.setOffre(o);
            
            Stage stage = new Stage();
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.initStyle(StageStyle.TRANSPARENT);
            stage.initOwner(cardsContainerMesOffres.getScene().getWindow());
            
            Scene scene = new Scene(root);
            scene.setFill(Color.TRANSPARENT);
            scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
            
            stage.setScene(scene);
            stage.showAndWait();
            
            loadData(); // Refresh list after popup closes
        } catch (IOException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible d'ouvrir le formulaire.", e.getMessage());
        }
    }
    
    // Ouvre la vue "Détails de l'offre" en mode lecture seule pour les "Autres offres"
    private void openOffreDetailView(OffreStage o) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/OffreDetail.fxml"));
            Parent root = loader.load();
            
            OffreDetailController controller = loader.getController();
            // Le parent = null va cacher le panneau Apply automatiquement
            controller.setOffre(o, null);
            
            Scene currentScene = cardsContainerMesOffres.getScene();
            currentScene.setRoot(root);
            
        } catch (IOException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible d'ouvrir les détails de l'offre.", e.getMessage());
        }
    }

    private void handleSupprimer(OffreStage o) {
        Alert confirm = new Alert(Alert.AlertType.CONFIRMATION);
        confirm.setTitle("Suppression");
        confirm.setHeaderText("Confirmer la suppression");
        confirm.setContentText("Voulez-vous vraiment supprimer l'offre : " + o.getTitre() + " ?");
        
        if (confirm.showAndWait().get() == ButtonType.OK) {
            try {
                serviceMethod.supprimer(o.getId());
                loadData();
            } catch (SQLException e) {
                showAlert(Alert.AlertType.ERROR, "Erreur", "Erreur lors de la suppression.", e.getMessage());
            }
        }
    }

    @FXML
    void handleActualiser(ActionEvent event) {
        loadData();
    }

    @FXML
    void handleBack(ActionEvent event) {
        try {
            javafx.scene.Parent root = javafx.fxml.FXMLLoader.load(getClass().getResource("/fxml/RecruiterDashboard.fxml"));
            javafx.scene.Scene scene = ((javafx.scene.Node) event.getSource()).getScene();
            javafx.stage.Stage stage = (javafx.stage.Stage) scene.getWindow();
            stage.getScene().setRoot(root);
        } catch (java.io.IOException e) {
            e.printStackTrace();
        }
    }

    private void showAlert(Alert.AlertType type, String title, String header, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
