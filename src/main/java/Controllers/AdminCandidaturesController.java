package Controllers;

import Entities.StageCondidature;
import Services.ServiceStageCondidature;
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

public class AdminCandidaturesController {

    @FXML private TableView<StageCondidature> tableCandidatures;
    @FXML private TableColumn<StageCondidature, Integer> colId;
    @FXML private TableColumn<StageCondidature, String> colTitre;
    @FXML private TableColumn<StageCondidature, Integer> colEtudiant;
    @FXML private TableColumn<StageCondidature, Integer> colOffre;
    @FXML private TableColumn<StageCondidature, String> colStatut;

    private ServiceStageCondidature serviceMethod = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());
    private ObservableList<StageCondidature> observableList;

    @FXML
    public void initialize() {
        colId.setCellValueFactory(new PropertyValueFactory<>("id"));
        colTitre.setCellValueFactory(new PropertyValueFactory<>("titre"));
        colEtudiant.setCellValueFactory(new PropertyValueFactory<>("id_etudiant"));
        colOffre.setCellValueFactory(new PropertyValueFactory<>("id_offre"));
        colStatut.setCellValueFactory(new PropertyValueFactory<>("statut"));

        loadData();
    }

    private void loadData() {
        try {
            List<StageCondidature> all = serviceMethod.afficherAll();
            observableList = FXCollections.observableArrayList(all);
            tableCandidatures.setItems(observableList);
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de charger les candidatures.", e.getMessage());
        }
    }

    @FXML
    void handleSupprimer(ActionEvent event) {
        StageCondidature selected = tableCandidatures.getSelectionModel().getSelectedItem();
        if (selected != null) {
            try {
                serviceMethod.supprimer(selected.getId());
                showAlert(Alert.AlertType.INFORMATION, "Succès", "Candidature supprimée du système.", "");
                loadData();
            } catch (SQLException e) {
                showAlert(Alert.AlertType.ERROR, "Erreur", "Suppression impossible.", e.getMessage());
            }
        } else {
            showAlert(Alert.AlertType.WARNING, "Attention", "Sélectionnez une candidature à supprimer.", "");
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
