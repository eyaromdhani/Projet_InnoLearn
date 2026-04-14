package Controllers;

import Entities.OffreStage;
import Services.ServiceOffreStage;
import Services.ServiceStageCondidature;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.geometry.Insets;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.scene.shape.Circle;
import javafx.scene.shape.SVGPath;
import utils.MyDatabase;

import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;
import java.util.stream.Collectors;

public class RecruiterDashboardController implements Initializable {

    @FXML private TextField txtSearch;
    @FXML private ComboBox<String> comboEnterprise;
    @FXML private ComboBox<String> comboDuration;
    @FXML private Label lblCount;
    @FXML private FlowPane cardsContainer;
    @FXML private Circle heroCircle;

    private ServiceOffreStage serviceOffre;
    private final int MOCK_RECRUITER_ID = 8;
    private List<OffreStage> allMyOffres;

    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        serviceOffre = new ServiceOffreStage(MyDatabase.getInstance().getConnection());
        
        loadData();

        // Add search listener
        txtSearch.textProperty().addListener((obs, oldVal, newVal) -> {
            filterData(newVal);
        });
    }

    private void loadData() {
        try {
            List<OffreStage> all = serviceOffre.afficherAll();
            // Display ALL offers
            allMyOffres = all;
            
            updateDisplay(allMyOffres);
            
            // Populate combos
            List<String> enterprises = allMyOffres.stream()
                    .map(OffreStage::getEntreprise)
                    .distinct()
                    .collect(Collectors.toList());
            comboEnterprise.getItems().clear();
            comboEnterprise.getItems().add("Toutes");
            comboEnterprise.getItems().addAll(enterprises);
            
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void filterData(String query) {
        if (allMyOffres == null) return;
        
        List<OffreStage> filtered = allMyOffres.stream()
                .filter(o -> o.getTitre().toLowerCase().contains(query.toLowerCase()) || 
                            o.getEntreprise().toLowerCase().contains(query.toLowerCase()))
                .collect(Collectors.toList());
        
        updateDisplay(filtered);
    }

    private void updateDisplay(List<OffreStage> offres) {
        cardsContainer.getChildren().clear();
        lblCount.setText(offres.size() + " Offres");

        int colorIndex = 0;
        String[] colors = {"recruiter-card-blue", "recruiter-card-purple", "recruiter-card-pink", "recruiter-card-cyan"};

        for (OffreStage o : offres) {
            VBox card = createOfferCard(o, colors[colorIndex % colors.length]);
            cardsContainer.getChildren().add(card);
            colorIndex++;
        }
    }

    private VBox createOfferCard(OffreStage o, String colorClass) {
        VBox card = new VBox(15);
        card.getStyleClass().addAll("recruiter-offer-card", colorClass);
        
        HBox tagBox = new HBox();
        Label tag = new Label(o.getDomaine());
        tag.getStyleClass().add("card-tag");
        tagBox.getChildren().add(tag);
        
        VBox content = new VBox(5);
        Label title = new Label(o.getTitre());
        title.getStyleClass().add("card-title-white");
        title.setWrapText(true);
        
        Label company = new Label(o.getEntreprise());
        company.getStyleClass().add("card-company-white");
        
        content.getChildren().addAll(title, company);
        
        Region spacer = new Region();
        VBox.setVgrow(spacer, Priority.ALWAYS);
        
        HBox footer = new HBox();
        footer.setAlignment(javafx.geometry.Pos.CENTER_RIGHT);
        Button btnView = new Button("Voir Détails");
        btnView.setStyle("-fx-background-color: white; -fx-text-fill: #6358ff; -fx-background-radius: 15px; -fx-font-weight: bold;");
        btnView.setCursor(javafx.scene.Cursor.HAND);
        
        // Navigation to details
        btnView.setOnAction(e -> {
            try {
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/OffreDetail.fxml"));
                javafx.scene.Parent root = loader.load();
                
                OffreDetailController controller = loader.getController();
                // Pass null for StagesController as we are in Recruiter Dashboard context
                controller.setOffre(o, null);
                
                javafx.stage.Stage stage = (javafx.stage.Stage) cardsContainer.getScene().getWindow();
                stage.getScene().setRoot(root);
            } catch (java.io.IOException ex) {
                ex.printStackTrace();
            }
        });
        
        footer.getChildren().add(btnView);
        
        card.getChildren().addAll(tagBox, content, spacer, footer);
        
        return card;
    }

    @FXML
    void handlePublish(ActionEvent event) {
        navigateTo("/fxml/RecruiterOffres.fxml");
    }

    @FXML
    void handleMyOffers(ActionEvent event) {
        navigateTo("/fxml/RecruiterOffres.fxml");
    }

    @FXML
    void handleCandidatures(ActionEvent event) {
        navigateTo("/fxml/RecruiterCandidatures.fxml");
    }

    private void navigateTo(String fxmlPath) {
        try {
            javafx.scene.Parent root = javafx.fxml.FXMLLoader.load(getClass().getResource(fxmlPath));
            javafx.stage.Stage stage = (javafx.stage.Stage) cardsContainer.getScene().getWindow();
            stage.getScene().setRoot(root);
        } catch (java.io.IOException e) {
            e.printStackTrace();
        }
    }
}
