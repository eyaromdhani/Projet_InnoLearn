package com.innolearn.controller;
 
import com.innolearn.model.Depot;
import com.innolearn.model.Project;
import com.innolearn.service.CohereService;
import com.innolearn.service.DepotService;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.scene.layout.StackPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

import java.io.IOException;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONObject;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class DepotListController {

    // ── Table ──────────────────────────────────────────────────────────────────
    @FXML private TableView<Depot>          depotTable;
    @FXML private TableColumn<Depot, String>  colTitle;
    @FXML private TableColumn<Depot, String>  colStudent;
    @FXML private TableColumn<Depot, String>  colType;
    @FXML private TableColumn<Depot, Integer> colScore;
    @FXML private TableColumn<Depot, String>  colResult;
    @FXML private TableColumn<Depot, Timestamp> colDate;
    @FXML private TableColumn<Depot, Void>    colActions;

    // ── Hero ───────────────────────────────────────────────────────────────────
    @FXML private Label  lblProjectTitle;
    @FXML private Label  lblProjectDescription;
    @FXML private Label  lblSummary;
    @FXML private VBox   summaryBox;
    @FXML private Button btnSummarize;

    private final CohereService cohereService = new CohereService();

    // ── Services ───────────────────────────────────────────────────────────────
    private final DepotService  depotService  = new DepotService();

    private Project currentProject;
    private Integer filterProjectId = null;

    @FXML
    public void initialize() {
        colTitle.setCellValueFactory(new PropertyValueFactory<>("title"));
        colStudent.setCellValueFactory(new PropertyValueFactory<>("studentName"));
        colType.setCellValueFactory(new PropertyValueFactory<>("type"));
        colScore.setCellValueFactory(new PropertyValueFactory<>("aiScore"));
        colResult.setCellValueFactory(new PropertyValueFactory<>("aiResult"));
        colDate.setCellValueFactory(new PropertyValueFactory<>("uploadedAt"));

        setupScoreColumn();
        setupResultColumn();
        setupActionsColumn();

        if (filterProjectId == null) {
            loadDepots();
        }
    }

    private void setupScoreColumn() {
        colScore.setCellFactory(column -> new TableCell<Depot, Integer>() {
            @Override
            protected void updateItem(Integer item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setGraphic(null);
                    setText(null);
                } else {
                    Label label = new Label(item + "%");
                    String color = item >= 80 ? "#16a34a" : item >= 50 ? "#a16207" : "#dc2626";
                    label.setStyle("-fx-text-fill: white; -fx-background-color: " + color + "; -fx-padding: 3 8; -fx-background-radius: 10; -fx-font-weight: bold;");
                    setGraphic(label);
                }
            }
        });
    }

    private void setupResultColumn() {
        colResult.setCellFactory(column -> new TableCell<Depot, String>() {
            @Override
            protected void updateItem(String item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setText(null);
                } else {
                    String cleanResult = item.replace("SCORE:", "").replace("NIVEAU:", "").replace("FEEDBACK:", "").trim();
                    if (cleanResult.length() > 50) {
                        cleanResult = cleanResult.substring(0, 47) + "...";
                    }
                    setText(cleanResult);
                    Tooltip tooltip = new Tooltip(item);
                    tooltip.setWrapText(true);
                    tooltip.setPrefWidth(300);
                    setTooltip(tooltip);
                }
            }
        });
    }

    private void setupActionsColumn() {
        colActions.setCellFactory(param -> new TableCell<>() {
            private final Button btnDelete = new Button("🗑");
            {
                btnDelete.getStyleClass().add("btn-delete-action");
                btnDelete.setStyle("-fx-background-color: #fee2e2; -fx-text-fill: #ef4444; -fx-font-weight: bold; -fx-padding: 8 15; -fx-background-radius: 10; -fx-cursor: hand; -fx-border-color: #fecaca; -fx-border-radius: 10;");
                btnDelete.setOnAction(event -> {
                    Depot depot = getTableView().getItems().get(getIndex());
                    try {
                        depotService.deleteDepot(depot.getId());
                        loadDepots();
                    } catch (SQLException e) {
                        e.printStackTrace();
                    }
                });
            }

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                if (empty) {
                    setGraphic(null);
                } else {
                    HBox box = new HBox(btnDelete);
                    box.setAlignment(javafx.geometry.Pos.CENTER);
                    setGraphic(box);
                }
            }
        });
    }

    public void setProject(Project project) {
        this.currentProject = project;
        this.filterProjectId = project.getId();

        if (lblProjectTitle != null)       lblProjectTitle.setText(project.getTitle());
        if (lblProjectDescription != null) lblProjectDescription.setText(project.getDescription());

        loadDepots();
    }

    private void loadDepots() {
        try {
            List<Depot> depots;
            if (filterProjectId == null) {
                depots = depotService.getAllDepots();
            } else {
                depots = depotService.getDepotsByProject(filterProjectId);
            }
            ObservableList<Depot> list = FXCollections.observableArrayList(depots);
            depotTable.setItems(list);
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleBack(javafx.event.ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/ProjectList.fxml"));
            StackPane contentPane = (StackPane) depotTable.getScene().lookup("#contentPane");
            contentPane.getChildren().setAll((javafx.scene.Node) loader.load());
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleSummarize() {
        if (currentProject == null) return;
        
        btnSummarize.setDisable(true);
        btnSummarize.setText("Analyse globale...");
        summaryBox.setVisible(true);
        summaryBox.setManaged(true);
        lblSummary.setText("Génération du résumé par l'IA en cours...");

        new Thread(() -> {
            try {
                List<Depot> depots = depotService.getDepotsByProject(currentProject.getId());
                
                StringBuilder sb = new StringBuilder();
                sb.append("Titre du projet: ").append(currentProject.getTitle()).append("\n");
                sb.append("Description: ").append(currentProject.getDescription()).append("\n");
                sb.append("Niveau: ").append(currentProject.getDifficulty()).append("\n\n");
                sb.append("Soumissions actuelles:\n");
                
                for (Depot d : depots) {
                    sb.append("- ").append(d.getTitle()).append(" (Score: ").append(d.getAiScore()).append("%)\n");
                }
                
                String prompt = "Tu es un tuteur IA. Voici les détails d'un projet étudiant et la liste des livrables (dépôts) soumis jusqu'à présent avec leurs scores de complétion.\n\n" +
                                sb.toString() + "\n\n" +
                                "Fais un résumé très court (3-4 phrases maximum) sur l'état d'avancement global du projet. " +
                                "L'étudiant est-il sur la bonne voie ? Que doit-il améliorer ? Réponds de manière encourageante en français.";
                
                String summary = cohereService.sendMessage(prompt);
                
                Platform.runLater(() -> {
                    lblSummary.setText(summary);
                    btnSummarize.setDisable(false);
                    btnSummarize.setText("🤖 Actualiser le résumé");
                });
            } catch (Exception e) {
                e.printStackTrace();
                Platform.runLater(() -> {
                    lblSummary.setText("Erreur lors de l'analyse : " + e.getMessage());
                    btnSummarize.setDisable(false);
                    btnSummarize.setText("🤖 Réessayer");
                });
            }
        }).start();
    }

    @FXML
    private void handleAddDepot(javafx.event.ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/DepotForm.fxml"));
            VBox form = loader.load();

            DepotFormController controller = loader.getController();
            controller.setProjectId(filterProjectId != null ? filterProjectId : 0);
            controller.setOnSaveCallback(this::loadDepots);

            Stage stage = new Stage();
            stage.setTitle("Nouveau Dépôt");
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.setScene(new Scene(form));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
