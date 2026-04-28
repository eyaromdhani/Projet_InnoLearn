package com.innolearn.controller;

import com.innolearn.model.Project;
import com.innolearn.service.ProjectService;
import javafx.beans.property.SimpleStringProperty;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.collections.transformation.FilteredList;
import javafx.collections.transformation.SortedList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.stage.Modality;
import javafx.stage.Stage;
import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.Comparator;
import java.util.ResourceBundle;

public class AdminProjectListController implements Initializable {

    @FXML private TableView<Project> projectTable;
    @FXML private TableColumn<Project, Integer> colId;
    @FXML private TableColumn<Project, Project> colTitle;
    @FXML private TableColumn<Project, String> colLevel;
    @FXML private TableColumn<Project, String> colDate;
    @FXML private TableColumn<Project, Project> colActions;
    @FXML private TextField searchField;
    @FXML private ComboBox<String> sortCombo;

    private final ProjectService projectService = new ProjectService();
    private final ObservableList<Project> projectList = FXCollections.observableArrayList();
    private FilteredList<Project> filteredData;
    private SortedList<Project> sortedData;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        setupTable();
        setupSearchAndSort();
        loadProjects();
    }

    private void setupSearchAndSort() {
        filteredData = new FilteredList<>(projectList, p -> true);
        
        searchField.textProperty().addListener((obs, oldVal, newVal) -> {
            filteredData.setPredicate(project -> {
                if (newVal == null || newVal.isEmpty()) return true;
                String lowerCaseFilter = newVal.toLowerCase();
                return project.getTitle().toLowerCase().contains(lowerCaseFilter) || 
                       project.getDescription().toLowerCase().contains(lowerCaseFilter);
            });
        });

        sortCombo.getItems().addAll("Plus récents", "Plus anciens", "Nom (A-Z)", "Difficulté");
        sortCombo.setOnAction(e -> applySort());
        
        sortedData = new SortedList<>(filteredData);
        sortedData.comparatorProperty().bind(projectTable.comparatorProperty());
        projectTable.setItems(sortedData);
    }

    private void applySort() {
        String sortBy = sortCombo.getValue();
        if (sortBy == null) return;

        Comparator<Project> comparator;
        switch (sortBy) {
            case "Plus récents":
                comparator = (p1, p2) -> p2.getCreatedAt().compareTo(p1.getCreatedAt());
                break;
            case "Plus anciens":
                comparator = Comparator.comparing(Project::getCreatedAt);
                break;
            case "Nom (A-Z)":
                comparator = Comparator.comparing(p -> p.getTitle().toLowerCase());
                break;
            case "Difficulté":
                comparator = Comparator.comparing(Project::getDifficulty);
                break;
            default:
                return;
        }
        
        // We can't easily bind both the table comparator AND our custom one to the same SortedList
        // so we'll just sort the projectList or set a new comparator if it's not bound.
        // Actually, for a table, usually we just let the table handle sorting, but the user asked for a "trie" (sort).
        // Let's manually sort the projectList or just set the comparator on the sortedData if possible.
        projectTable.getSortOrder().clear(); // Clear table's own sorting to use our combo box sort
        sortedData.setComparator(comparator);
    }

    private void setupTable() {
        colId.setCellValueFactory(data -> new javafx.beans.property.SimpleIntegerProperty(data.getValue().getId()).asObject());
        
        // Custom Title + Description Column
        colTitle.setCellValueFactory(data -> new javafx.beans.property.SimpleObjectProperty<>(data.getValue()));
        colTitle.setCellFactory(column -> new TableCell<Project, Project>() {
            @Override
            protected void updateItem(Project project, boolean empty) {
                super.updateItem(project, empty);
                if (empty || project == null) {
                    setGraphic(null);
                } else {
                    VBox box = new VBox(2);
                    Label titleLabel = new Label(project.getTitle());
                    titleLabel.setStyle("-fx-font-weight: 800; -fx-text-fill: -fx-text-main; -fx-font-size: 14px;");
                    
                    String desc = project.getDescription();
                    if (desc != null && desc.length() > 50) desc = desc.substring(0, 47) + "...";
                    Label descLabel = new Label(desc);
                    descLabel.setStyle("-fx-text-fill: -fx-text-muted; -fx-font-size: 11px;");
                    
                    box.getChildren().addAll(titleLabel, descLabel);
                    setGraphic(box);
                }
                setStyle("-fx-alignment: center-left;");
            }
        });

        // Difficulty Badges
        colLevel.setCellValueFactory(data -> new SimpleStringProperty(data.getValue().getDifficulty()));
        colLevel.setCellFactory(column -> new TableCell<Project, String>() {
            @Override
            protected void updateItem(String level, boolean empty) {
                super.updateItem(level, empty);
                if (empty || level == null) {
                    setGraphic(null);
                } else {
                    Label label = new Label(level.toUpperCase());
                    label.getStyleClass().add("badge");
                    
                    switch (level.toLowerCase()) {
                        case "expert": label.getStyleClass().add("badge-expert"); break;
                        case "avancé": 
                        case "avance": label.getStyleClass().add("badge-avance"); break;
                        case "intermédiaire":
                        case "intermediaire": label.getStyleClass().add("badge-intermediaire"); break;
                        default: label.getStyleClass().add("badge-debutant"); break;
                    }
                    setGraphic(label);
                }
                setStyle("-fx-alignment: center;");
            }
        });

        // Date Column
        colDate.setCellValueFactory(data -> {
            if (data.getValue().getCreatedAt() != null) {
                return new SimpleStringProperty(data.getValue().getCreatedAt().toLocalDateTime().toLocalDate().toString());
            }
            return new SimpleStringProperty("-");
        });
        colDate.setStyle("-fx-alignment: center; -fx-text-fill: -fx-text-muted;");

        // Action Column
        colActions.setCellValueFactory(data -> new javafx.beans.property.SimpleObjectProperty<>(data.getValue()));
        colActions.setCellFactory(column -> new TableCell<Project, Project>() {
            @Override
            protected void updateItem(Project project, boolean empty) {
                super.updateItem(project, empty);
                if (empty || project == null) {
                    setGraphic(null);
                } else {
                    HBox box = new HBox(10);
                    box.setAlignment(javafx.geometry.Pos.CENTER);
                    
                    Button editBtn = new Button("Modifier");
                    editBtn.getStyleClass().add("btn-action-edit");
                    editBtn.setOnAction(e -> handleEditProject(project));
                    
                    Button viewBtn = new Button("Voir");
                    viewBtn.getStyleClass().add("btn-action-view");
                    // viewBtn.setOnAction(e -> handleViewProject(project));
                    
                    Button deleteBtn = new Button("🗑");
                    deleteBtn.setStyle("-fx-text-fill: #ef4444; -fx-background-color: transparent; -fx-cursor: hand;");
                    deleteBtn.setOnAction(e -> handleDeleteProject(project));
                    
                    box.getChildren().addAll(editBtn, viewBtn, deleteBtn);
                    setGraphic(box);
                }
            }
        });
    }

    private void loadProjects() {
        try {
            projectList.setAll(projectService.getAllProjects());
            // Table items are already bound to sortedData which is bound to filteredData which is bound to projectList
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleAddProject() {
        showProjectForm(null);
    }

    private void handleEditProject(Project project) {
        showProjectForm(project);
    }

    private void handleDeleteProject(Project project) {
        Alert alert = new Alert(Alert.AlertType.CONFIRMATION);
        alert.setTitle("Confirmation de suppression");
        alert.setHeaderText("Supprimer le projet : " + project.getTitle());
        alert.setContentText("Êtes-vous sûr de vouloir supprimer ce projet ?");

        if (alert.showAndWait().orElse(ButtonType.CANCEL) == ButtonType.OK) {
            try {
                projectService.deleteProject(project.getId());
                loadProjects();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }
    }

    private void showProjectForm(Project project) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/ProjectForm.fxml"));
            Parent root = loader.load();
            
            ProjectFormController controller = loader.getController();
            if (project != null) {
                controller.setProject(project);
            }
            
            Stage stage = new Stage();
            stage.setTitle(project == null ? "Ajouter un Projet" : "Modifier le Projet");
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.setScene(new Scene(root));
            
            stage.showAndWait();
            loadProjects(); // Refresh after form closes
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
