package Controllers;

import Entities.OffreStage;
import Services.ServiceOffreStage;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.cell.PropertyValueFactory;

import java.sql.SQLException;
import java.util.List;
import utils.MyDatabase;

public class AdminOffresController {

    @FXML private TableView<OffreStage> tableOffres;
    @FXML private TableColumn<OffreStage, Integer> colId;
    @FXML private TableColumn<OffreStage, String> colTitre;
    @FXML private TableColumn<OffreStage, String> colEntreprise;
    @FXML private TableColumn<OffreStage, String> colDomaine;
    @FXML private TableColumn<OffreStage, String> colStatut;

    private ServiceOffreStage serviceMethod = new ServiceOffreStage(MyDatabase.getInstance().getConnection());
    private ObservableList<OffreStage> observableList;

    @FXML
    public void initialize() {
        colId.setCellValueFactory(new PropertyValueFactory<>("id"));
        colTitre.setCellValueFactory(new PropertyValueFactory<>("titre"));
        colEntreprise.setCellValueFactory(new PropertyValueFactory<>("entreprise"));
        colDomaine.setCellValueFactory(new PropertyValueFactory<>("domaine"));
        colStatut.setCellValueFactory(new PropertyValueFactory<>("statut"));

        loadData();
    }

    private void loadData() {
        try {
            List<OffreStage> all = serviceMethod.afficherAll();
            observableList = FXCollections.observableArrayList(all);
            tableOffres.setItems(observableList);
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de charger les offres.", e.getMessage());
        }
    }

    @FXML
    void handleSupprimer(ActionEvent event) {
        OffreStage selected = tableOffres.getSelectionModel().getSelectedItem();
        if (selected != null) {
            try {
                serviceMethod.supprimer(selected.getId());
                showAlert(Alert.AlertType.INFORMATION, "Succès", "Offre supprimée du système.", "");
                loadData();
            } catch (SQLException e) {
                showAlert(Alert.AlertType.ERROR, "Erreur", "Suppression impossible.", e.getMessage());
            }
        } else {
            showAlert(Alert.AlertType.WARNING, "Attention", "Sélectionnez une offre à supprimer.", "");
        }
    }

    @FXML
    void handleActualiser(ActionEvent event) {
        loadData();
    }

    private void showAlert(Alert.AlertType type, String title, String header, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
