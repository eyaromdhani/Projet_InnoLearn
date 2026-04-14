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
import javafx.scene.control.TextArea;
import javafx.scene.control.cell.PropertyValueFactory;

import java.sql.SQLException;
import java.util.List;
import java.util.stream.Collectors;
import utils.MyDatabase;

public class StudentCandidaturesController {

    @FXML private TableView<StageCondidature> tableCandidatures;
    @FXML private TableColumn<StageCondidature, String> colTitre;
    @FXML private TableColumn<StageCondidature, String> colStatut;

    @FXML private TextArea txtMotivation;

    private ServiceStageCondidature serviceMethod = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());
    private ObservableList<StageCondidature> observableList;
    private final int MOCK_STUDENT_ID = 10;

    @FXML
    public void initialize() {
        colTitre.setCellValueFactory(new PropertyValueFactory<>("titre"));
        colStatut.setCellValueFactory(new PropertyValueFactory<>("statut"));

        tableCandidatures.getSelectionModel().selectedItemProperty().addListener((obs, oldSelection, newSelection) -> {
            if (newSelection != null) {
                txtMotivation.setText(newSelection.getLettre_motivation());
            }
        });

        loadData();
    }

    private void loadData() {
        try {
            List<StageCondidature> tous = serviceMethod.afficherAll();
            List<StageCondidature> mesCandidatures = tous.stream()
                    .filter(c -> c.getId_etudiant() != null && c.getId_etudiant() == MOCK_STUDENT_ID)
                    .collect(Collectors.toList());
            observableList = FXCollections.observableArrayList(mesCandidatures);
            tableCandidatures.setItems(observableList);
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de charger les candidatures.", e.getMessage());
        }
    }

    @FXML
    void handleModifier(ActionEvent event) {
        StageCondidature selected = tableCandidatures.getSelectionModel().getSelectedItem();
        if (selected == null) {
            showAlert(Alert.AlertType.WARNING, "Attention", "Sélectionnez une candidature.", "");
            return;
        }
        try {
            selected.setLettre_motivation(txtMotivation.getText());
            serviceMethod.modifier(selected);
            showAlert(Alert.AlertType.INFORMATION, "Succès", "Candidature mise à jour !", "");
            loadData();
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Modification échouée.", e.getMessage());
        }
    }

    @FXML
    void handleAnnuler(ActionEvent event) {
        StageCondidature selected = tableCandidatures.getSelectionModel().getSelectedItem();
        if (selected != null) {
            try {
                serviceMethod.supprimer(selected.getId());
                showAlert(Alert.AlertType.INFORMATION, "Succès", "Candidature annulée.", "");
                txtMotivation.clear();
                loadData();
            } catch (SQLException e) {
                showAlert(Alert.AlertType.ERROR, "Erreur", "Annulation impossible.", e.getMessage());
            }
        } else {
            showAlert(Alert.AlertType.WARNING, "Attention", "Sélectionnez une candidature à annuler.", "");
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
