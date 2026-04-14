package Controllers;

import Entities.OffreStage;
import Entities.StageCondidature;
import Services.ServiceOffreStage;
import Services.ServiceStageCondidature;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Label;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextArea;
import javafx.scene.control.cell.PropertyValueFactory;

import java.sql.Date;
import java.sql.SQLException;
import java.util.List;
import utils.MyDatabase;

public class StudentOffresController {

    @FXML private TableView<OffreStage> tableOffres;
    @FXML private TableColumn<OffreStage, String> colTitre;
    @FXML private TableColumn<OffreStage, String> colEntreprise;
    @FXML private TableColumn<OffreStage, String> colDomaine;

    @FXML private Label lblSelectedOffre;
    @FXML private Label lblDescription;
    @FXML private TextArea txtMotivation;

    private ServiceOffreStage serviceOffre = new ServiceOffreStage(MyDatabase.getInstance().getConnection());
    private ServiceStageCondidature serviceCondidature = new ServiceStageCondidature(MyDatabase.getInstance().getConnection());
    private ObservableList<OffreStage> observableList;
    private final int MOCK_STUDENT_ID = 10; 

    @FXML
    public void initialize() {
        colTitre.setCellValueFactory(new PropertyValueFactory<>("titre"));
        colEntreprise.setCellValueFactory(new PropertyValueFactory<>("entreprise"));
        colDomaine.setCellValueFactory(new PropertyValueFactory<>("domaine"));

        tableOffres.getSelectionModel().selectedItemProperty().addListener((obs, oldSelection, newSelection) -> {
            if (newSelection != null) {
                lblSelectedOffre.setText("Offre: " + newSelection.getTitre() + " chez " + newSelection.getEntreprise());
                lblDescription.setText(newSelection.getDescription());
            }
        });

        loadData();
    }

    private void loadData() {
        try {
            List<OffreStage> all = serviceOffre.afficherAll();
            observableList = FXCollections.observableArrayList(all);
            tableOffres.setItems(observableList);
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible de charger les offres.", e.getMessage());
        }
    }

    @FXML
    void handlePostuler(ActionEvent event) {
        OffreStage selected = tableOffres.getSelectionModel().getSelectedItem();
        if (selected == null) {
            showAlert(Alert.AlertType.WARNING, "Attention", "Veuillez sélectionner une offre.", "");
            return;
        }
        if (txtMotivation.getText().isEmpty()) {
            showAlert(Alert.AlertType.WARNING, "Attention", "Veuillez écrire une lettre de motivation.", "");
            return;
        }

        try {
            StageCondidature c = new StageCondidature();
            c.setId_offre(selected.getId());
            c.setId_etudiant(MOCK_STUDENT_ID);
            c.setTitre("Candidature pour " + selected.getTitre());
            c.setDomaine(selected.getDomaine());
            c.setLettre_motivation(txtMotivation.getText());
            c.setDate_publication(new Date(System.currentTimeMillis()));
            c.setStatut("En attente");
            c.setType_request("Demande");

            serviceCondidature.ajouter(c);
            showAlert(Alert.AlertType.INFORMATION, "Succès", "Votre candidature a été envoyée !", "");
            txtMotivation.clear();
        } catch (SQLException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "L'envoi a échoué.", e.getMessage());
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
