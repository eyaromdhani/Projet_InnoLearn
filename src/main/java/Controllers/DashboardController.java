package Controllers;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.control.Button;
import javafx.scene.layout.StackPane;

import java.io.IOException;
import java.net.URL;

public class DashboardController {

    @FXML
    private Button btnHome;
    @FXML
    private Button btnAdminCandidatures;
    @FXML
    private Button btnAdminOffres;
    @FXML
    private Button btnRecruiterCandidatures;
    @FXML
    private Button btnRecruiterOffres;
    @FXML
    private Button btnStudentCandidatures;
    @FXML
    private Button btnStudentOffres;
    @FXML
    private StackPane contentArea;

    private void setActiveButton(Button activeBtn) {
        Button[] buttons = {
                btnHome,
                btnAdminCandidatures, btnAdminOffres,
                btnRecruiterCandidatures, btnRecruiterOffres,
                btnStudentCandidatures, btnStudentOffres
        };
        for (Button btn : buttons) {
            if (btn != null) {
                btn.getStyleClass().remove("active");
            }
        }
        if (activeBtn != null && !activeBtn.getStyleClass().contains("active")) {
            activeBtn.getStyleClass().add("active");
        }
    }

    private void loadView(String fxmlPath) {
        try {
            URL resource = getClass().getResource(fxmlPath);
            if(resource == null) {
                System.err.println("Cannot find " + fxmlPath);
                return;
            }
            Parent root = FXMLLoader.load(resource);
            contentArea.getChildren().clear();
            contentArea.getChildren().add(root);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void initialize() {
        // Load home by default
        loadHome(null);
    }

    @FXML
    void loadHome(ActionEvent event) {
        setActiveButton(btnHome);
        loadView("/fxml/Home.fxml");
    }

    @FXML
    void loadAdminCandidatures(ActionEvent event) {
        setActiveButton(btnAdminCandidatures);
        loadView("/fxml/AdminDashboard.fxml");
    }

    @FXML
    void loadAdminOffres(ActionEvent event) {
        setActiveButton(btnAdminOffres);
        loadView("/fxml/AdminDashboard.fxml");
    }

    @FXML
    void loadRecruiterCandidatures(ActionEvent event) {
        setActiveButton(btnRecruiterCandidatures);
        loadView("/fxml/RecruiterCandidatures.fxml");
    }

    @FXML
    void loadRecruiterOffres(ActionEvent event) {
        setActiveButton(btnRecruiterOffres);
        loadView("/fxml/RecruiterDashboard.fxml");
    }

    @FXML
    void loadStudentCandidatures(ActionEvent event) {
        setActiveButton(btnStudentCandidatures);
        loadView("/fxml/StudentCandidatures.fxml");
    }

    @FXML
    void loadStudentOffres(ActionEvent event) {
        setActiveButton(btnStudentOffres);
        loadView("/fxml/StudentOffres.fxml");
    }
}
