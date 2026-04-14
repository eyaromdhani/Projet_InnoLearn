package Controllers;

import Entities.StageCondidature;
import Services.ServiceStageCondidature;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.shape.Circle;
import javafx.scene.shape.SVGPath;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.stage.StageStyle;
import utils.MyDatabase;

import java.io.IOException;
import java.sql.SQLException;
import java.util.List;

public class RecruiterCandidaturesController {

    @FXML private FlowPane cardsContainerCandidatures;
    @FXML private FlowPane cardsContainerDemandes;

    private ServiceStageCondidature serviceMethod =
            new ServiceStageCondidature(MyDatabase.getInstance().getConnection());

    @FXML
    public void initialize() {
        loadData();
    }

    public void loadData() {
        try {
            final int MOCK_RECRUITER_ID = 8;
            // Charger les candidatures liées à ce recruteur
            List<StageCondidature> candidatures = serviceMethod.afficherParRecruteur(MOCK_RECRUITER_ID);
            updateDisplay(cardsContainerCandidatures, candidatures);

            // Charger les demandes générales des étudiants
            List<StageCondidature> demandes = serviceMethod.afficherDemandes();
            updateDisplay(cardsContainerDemandes, demandes);

        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de charger les listes.", e.getMessage());
        }
    }

    private void updateDisplay(FlowPane container, List<StageCondidature> liste) {
        container.getChildren().clear();
        for (StageCondidature sc : liste) {
            container.getChildren().add(createCandidatureCard(sc));
        }
    }

    // ─── Card builder ──────────────────────────────────────────────────────

    private VBox createCandidatureCard(StageCondidature sc) {
        VBox card = new VBox(15);
        card.getStyleClass().add("item-card");
        card.setPrefSize(320, 250);
        card.setStyle("-fx-background-color: white; -fx-background-radius: 20px; -fx-padding: 20px; " +
                "-fx-effect: dropshadow(three-pass-box, rgba(0,0,0,0.08), 15, 0, 0, 5);");

        // ── Header: avatar + status pill ──
        HBox header = new HBox(10);
        header.setAlignment(javafx.geometry.Pos.CENTER_LEFT);

        Circle profileCircle = new Circle(20, Color.web("#f1f3f6"));
        SVGPath personIcon = new SVGPath();
        personIcon.setContent("M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 " +
                "3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 " +
                "1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z");
        personIcon.setFill(Color.web("#95a5a6"));
        personIcon.setScaleX(0.8);
        personIcon.setScaleY(0.8);
        StackPane iconStack = new StackPane(profileCircle, personIcon);

        Label statusPill = new Label(sc.getStatut());
        styleStatutPill(statusPill, sc.getStatut());

        Region spacer = new Region();
        HBox.setHgrow(spacer, Priority.ALWAYS);
        header.getChildren().addAll(iconStack, spacer, statusPill);

        // ── Body ──
        VBox content = new VBox(5);
        Label studentName = new Label("Candidat #" + sc.getId_etudiant());
        studentName.setStyle("-fx-font-weight: bold; -fx-font-size: 16px; -fx-text-fill: #2c3e50;");

        String titreSuffix = (sc.getType_request().equalsIgnoreCase("DEMANDE")) ? "Recherche : " : "Pour : ";
        Label offerTitre = new Label(titreSuffix + (sc.getTitre() != null ? sc.getTitre() : "—"));
        offerTitre.setStyle("-fx-text-fill: #7f8c8d; -fx-font-size: 13px;");
        offerTitre.setWrapText(true);
        offerTitre.setMaxHeight(40);
        content.getChildren().addAll(studentName, offerTitre);

        // ── Footer actions ──
        HBox footer = new HBox(10);
        footer.setAlignment(javafx.geometry.Pos.BOTTOM_CENTER);
        VBox.setVgrow(footer, Priority.ALWAYS);

        Button btnView = new Button("Details");
        btnView.getStyleClass().add("btn-secondary");
        btnView.setStyle("-fx-padding: 5 15;");
        btnView.setOnAction(e -> showDetailPopup(sc));

        Button btnRefuse = new Button("Refuser");
        btnRefuse.getStyleClass().add("btn-danger");
        btnRefuse.setStyle("-fx-padding: 5 15;");
        btnRefuse.setOnAction(e -> handleAction(sc, "Refusée"));

        Button btnAccept = new Button("Accepter");
        btnAccept.getStyleClass().add("btn-primary");
        btnAccept.setStyle("-fx-background-color: #2ecc71; -fx-padding: 5 15;");
        btnAccept.setOnAction(e -> handleAction(sc, "Acceptée"));

        footer.getChildren().addAll(btnView, btnRefuse, btnAccept);
        card.getChildren().addAll(header, content, footer);
        return card;
    }

    private void styleStatutPill(Label pill, String statut) {
        if (statut == null) statut = "";
        switch (statut.toLowerCase()) {
            case "acceptée": case "acceptee":
                pill.setStyle("-fx-background-color: #e8f5e9; -fx-text-fill: #2ecc71; " +
                        "-fx-background-radius: 20px; -fx-padding: 4 12; -fx-font-weight: bold;");
                break;
            case "refusée": case "refusee":
                pill.setStyle("-fx-background-color: #ffebee; -fx-text-fill: #e74c3c; " +
                        "-fx-background-radius: 20px; -fx-padding: 4 12; -fx-font-weight: bold;");
                break;
            default:
                pill.setStyle("-fx-background-color: #fff8e1; -fx-text-fill: #f39c12; " +
                        "-fx-background-radius: 20px; -fx-padding: 4 12; -fx-font-weight: bold;");
        }
    }

    // ─── Open detail popup ──────────────────────────────────────────────────

    private void showDetailPopup(StageCondidature sc) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/CandidatureDetail.fxml"));
            Parent root = loader.load();

            CandidatureDetailController ctrl = loader.getController();
            ctrl.setCandidature(sc);
            ctrl.setOnActionCallback(this::loadData);   // refresh cards after Accepter/Refuser

            Stage popup = new Stage();
            popup.initModality(Modality.APPLICATION_MODAL);
            popup.initStyle(StageStyle.TRANSPARENT);
            popup.setTitle("Détails");

            Scene scene = new Scene(root);
            scene.setFill(Color.TRANSPARENT);
            scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
            popup.setScene(scene);
            popup.showAndWait();

        } catch (IOException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible d'ouvrir les détails.", e.getMessage());
        }
    }

    // ─── Quick actions from card buttons ────────────────────────────────────

    private void handleAction(StageCondidature sc, String nouveauStatut) {
        try {
            sc.setStatut(nouveauStatut);
            serviceMethod.modifier(sc);
            showAlert(Alert.AlertType.INFORMATION, "Succès", "Statut mis à jour : " + nouveauStatut, "");
            loadData();
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Échec de la modification.", e.getMessage());
        }
    }

    @FXML
    void handleActualiser(ActionEvent event) {
        loadData();
    }

    @FXML
    void handleBack(ActionEvent event) {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/fxml/RecruiterDashboard.fxml"));
            // L'événement pourrait venir d'un bouton qui se trouve lui-même n'importe où
            Scene scene = ((javafx.scene.Node) event.getSource()).getScene();
            Stage stage = (Stage) scene.getWindow();
            stage.getScene().setRoot(root);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void showAlert(Alert.AlertType type, String title, String header, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(header == null || header.isEmpty() ? null : header);
        alert.setContentText(content);
        alert.showAndWait();
    }
}

