package Controllers;

import Entities.OffreStage;
import Entities.StageCondidature;
import Services.ServiceOffreStage;
import Services.ServiceStageCondidature;
import javafx.beans.property.ReadOnlyObjectWrapper;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.HBox;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import utils.MyDatabase;

import java.io.IOException;

import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;
import java.util.stream.Collectors;

public class AdminDashboardController implements Initializable {

    // TabPane
    @FXML private TabPane mainTabPane;

    // Offres List
    @FXML private ListView<OffreStage> listOffres;
    @FXML private TextField txtSearchOffres;

    // Candidatures List
    @FXML private ListView<StageCondidature> listCandidatures;
    @FXML private TextField txtSearchCandidatures;

    private ServiceOffreStage serviceOffre;
    private ServiceStageCondidature serviceCandidature;
    
    private List<OffreStage> allOffres;
    private List<StageCondidature> allCandidatures;

    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        serviceOffre = new ServiceOffreStage(MyDatabase.getInstance().getConnection());
        serviceCandidature = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());

        setupOffresTable();
        setupCandidaturesTable();
        
        loadData();

        // Search listeners
        txtSearchOffres.textProperty().addListener((obs, old, newVal) -> filterOffres(newVal));
        txtSearchCandidatures.textProperty().addListener((obs, old, newVal) -> filterCandidatures(newVal));
    }

    private void setupOffresTable() {
        listOffres.setCellFactory(param -> new ListCell<>() {
            @Override
            protected void updateItem(OffreStage item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setGraphic(null);
                    setText(null);
                    setStyle("-fx-background-color: transparent;");
                } else {
                    HBox root = new HBox(15);
                    root.setStyle("-fx-background-color: white; -fx-background-radius: 10px; -fx-padding: 15; -fx-border-color: #f1f4f9; -fx-border-radius: 10px; -fx-border-width: 2px;");
                    root.setAlignment(javafx.geometry.Pos.CENTER_LEFT);

                    Label lblId = new Label("#" + item.getId());
                    lblId.setStyle("-fx-font-weight: bold; -fx-text-fill: #95a5a6; -fx-font-size: 14px; -fx-min-width: 40px;");

                    VBox infoVBox = new VBox(5);
                    infoVBox.setPrefWidth(250);
                    Label lblTitre = new Label(item.getTitre());
                    lblTitre.setStyle("-fx-font-weight: bold; -fx-font-size: 16px; -fx-text-fill: #2c3e50;");
                    Label lblEntreprise = new Label(item.getEntreprise() + " | " + item.getDomaine());
                    lblEntreprise.setStyle("-fx-text-fill: #7f8c8d; -fx-font-size: 12px;");
                    infoVBox.getChildren().addAll(lblTitre, lblEntreprise);

                    Label lblDate = new Label(item.getDate_publication() != null ? item.getDate_publication().toLocalDateTime().toLocalDate().toString() : "-");
                    lblDate.setStyle("-fx-text-fill: #95a5a6; -fx-font-size: 13px; -fx-pref-width: 100px;");

                    Label lblStatus = new Label(item.getStatut());
                    lblStatus.getStyleClass().add("status-pill");
                    if (item.getStatut() != null && (item.getStatut().equalsIgnoreCase("Ouverte") || item.getStatut().equalsIgnoreCase("ACTIF"))) {
                        lblStatus.getStyleClass().add("status-pill-open");
                    } else {
                        lblStatus.getStyleClass().add("status-pill-closed");
                    }
                    lblStatus.setPrefWidth(100);

                    // Actions
                    Button editBtn = new Button("Modifier");
                    editBtn.setStyle("-fx-background-color: #3498db; -fx-text-fill: white; -fx-font-weight: bold; -fx-background-radius: 5; -fx-cursor: hand; -fx-padding: 5 15;");
                    editBtn.setOnAction(e -> handleEditOffre(item));

                    Button deleteBtn = new Button("Supprimer");
                    deleteBtn.setStyle("-fx-background-color: #e74c3c; -fx-text-fill: white; -fx-font-weight: bold; -fx-background-radius: 5; -fx-cursor: hand; -fx-padding: 5 15;");
                    deleteBtn.setOnAction(e -> handleDeleteOffre(item));

                    HBox actionsHBox = new HBox(10, editBtn, deleteBtn);
                    actionsHBox.setAlignment(javafx.geometry.Pos.CENTER_RIGHT);
                    HBox.setHgrow(actionsHBox, javafx.scene.layout.Priority.ALWAYS);

                    root.getChildren().addAll(lblId, infoVBox, lblDate, lblStatus, actionsHBox);
                    setGraphic(root);
                    setStyle("-fx-background-color: transparent; -fx-padding: 0 0 10 0;");
                }
            }
        });
    }

    private void setupCandidaturesTable() {
        listCandidatures.setCellFactory(param -> new ListCell<>() {
            @Override
            protected void updateItem(StageCondidature item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setGraphic(null);
                    setText(null);
                    setStyle("-fx-background-color: transparent;");
                } else {
                    HBox root = new HBox(15);
                    root.setStyle("-fx-background-color: white; -fx-background-radius: 10px; -fx-padding: 15; -fx-border-color: #f1f4f9; -fx-border-radius: 10px; -fx-border-width: 2px;");
                    root.setAlignment(javafx.geometry.Pos.CENTER_LEFT);

                    Label lblId = new Label("#" + item.getId());
                    lblId.setStyle("-fx-font-weight: bold; -fx-text-fill: #95a5a6; -fx-font-size: 14px; -fx-min-width: 40px;");

                    VBox infoVBox = new VBox(5);
                    infoVBox.setPrefWidth(250);
                    Label lblTitre = new Label(item.getTitre());
                    lblTitre.setStyle("-fx-font-weight: bold; -fx-font-size: 16px; -fx-text-fill: #2c3e50;");
                    Label lblIds = new Label("Étudiant: " + item.getId_etudiant() + " | Offre: " + item.getId_offre());
                    lblIds.setStyle("-fx-text-fill: #7f8c8d; -fx-font-size: 12px;");
                    infoVBox.getChildren().addAll(lblTitre, lblIds);

                    Label lblStatus = new Label(item.getStatut());
                    lblStatus.getStyleClass().add("status-pill");
                    if (item.getStatut() != null && item.getStatut().equalsIgnoreCase("Acceptée")) {
                        lblStatus.getStyleClass().add("status-pill-open");
                    } else if (item.getStatut() != null && item.getStatut().equalsIgnoreCase("EN_ATTENTE")) {
                        lblStatus.getStyleClass().add("status-pill-pending");
                    } else {
                        lblStatus.getStyleClass().add("status-pill-closed");
                    }
                    lblStatus.setPrefWidth(120);

                    // Actions
                    Button deleteBtn = new Button("Supprimer");
                    deleteBtn.setStyle("-fx-background-color: #e74c3c; -fx-text-fill: white; -fx-font-weight: bold; -fx-background-radius: 5; -fx-cursor: hand; -fx-padding: 5 15;");
                    deleteBtn.setOnAction(e -> handleDeleteCandidature(item));

                    HBox actionsHBox = new HBox(10, deleteBtn);
                    actionsHBox.setAlignment(javafx.geometry.Pos.CENTER_RIGHT);
                    HBox.setHgrow(actionsHBox, javafx.scene.layout.Priority.ALWAYS);

                    root.getChildren().addAll(lblId, infoVBox, lblStatus, actionsHBox);
                    setGraphic(root);
                    setStyle("-fx-background-color: transparent; -fx-padding: 0 0 10 0;");
                }
            }
        });
    }

    private void loadData() {
        try {
            allOffres = serviceOffre.afficherAll();
            listOffres.setItems(FXCollections.observableArrayList(allOffres));

            allCandidatures = serviceCandidature.afficherAll();
            listCandidatures.setItems(FXCollections.observableArrayList(allCandidatures));
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void filterOffres(String query) {
        if (allOffres == null) return;
        List<OffreStage> filtered = allOffres.stream()
                .filter(o -> o.getTitre().toLowerCase().contains(query.toLowerCase()) || 
                             o.getEntreprise().toLowerCase().contains(query.toLowerCase()))
                .collect(Collectors.toList());
        listOffres.setItems(FXCollections.observableArrayList(filtered));
    }

    private void filterCandidatures(String query) {
        if (allCandidatures == null) return;
        List<StageCondidature> filtered = allCandidatures.stream()
                .filter(c -> c.getTitre().toLowerCase().contains(query.toLowerCase()))
                .collect(Collectors.toList());
        listCandidatures.setItems(FXCollections.observableArrayList(filtered));
    }

    @FXML
    void handleNewOffre(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/AddOffreForm.fxml"));
            Parent root = loader.load();
            
            Stage stage = new Stage();
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.initStyle(javafx.stage.StageStyle.TRANSPARENT);
            stage.initOwner(listOffres.getScene().getWindow());
            
            Scene scene = new Scene(root);
            scene.setFill(javafx.scene.paint.Color.TRANSPARENT);
            scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
            
            stage.setScene(scene);
            stage.showAndWait();
            
            loadData(); // Refresh table
        } catch (IOException e) {
            e.printStackTrace();
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur");
            alert.setContentText("Impossible d'ouvrir le formulaire : " + e.getMessage());
            alert.showAndWait();
        }
    }

    @FXML
    void handleExportPDF(ActionEvent event) {
        System.out.println("Exporting to PDF...");
    }

    @FXML
    void handleNewCandidature(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/AddCandidatureForm.fxml"));
            Parent root = loader.load();

            AddCandidatureController controller = loader.getController();
            controller.setOnSaveCallback(this::loadData);

            Stage stage = new Stage();
            stage.setTitle("Ajouter une Candidature");
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.initOwner(listCandidatures.getScene().getWindow());
            stage.setScene(new Scene(root));
            stage.getScene().getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
            stage.showAndWait();
        } catch (IOException e) {
            e.printStackTrace();
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur");
            alert.setContentText("Impossible d'ouvrir le formulaire : " + e.getMessage());
            alert.showAndWait();
        }
    }

    private void handleEditOffre(OffreStage item) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/AddOffreForm.fxml"));
            Parent root = loader.load();
            
            AddOffreController controller = loader.getController();
            controller.setOffre(item);
            
            Stage stage = new Stage();
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.initStyle(javafx.stage.StageStyle.TRANSPARENT);
            stage.initOwner(listOffres.getScene().getWindow());
            
            Scene scene = new Scene(root);
            scene.setFill(javafx.scene.paint.Color.TRANSPARENT);
            scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
            
            stage.setScene(scene);
            stage.showAndWait();
            
            loadData(); // Refresh table
        } catch (IOException e) {
            e.printStackTrace();
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur");
            alert.setContentText("Impossible d'ouvrir le formulaire : " + e.getMessage());
            alert.showAndWait();
        }
    }

    private void handleDeleteOffre(OffreStage item) {
        try {
            serviceOffre.supprimer(item.getId());
            loadData();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void handleDeleteCandidature(StageCondidature item) {
        try {
            serviceCandidature.supprimer(item.getId());
            loadData();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
