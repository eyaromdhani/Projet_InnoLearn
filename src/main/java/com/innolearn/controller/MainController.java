package com.innolearn.controller;

import com.innolearn.service.ProjectService;
import com.innolearn.service.DepotService;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.control.Button;
import javafx.scene.layout.VBox;
import javafx.scene.layout.StackPane;
import javafx.event.ActionEvent;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;

public class MainController {

    @FXML private StackPane rootPane;
    @FXML private StackPane contentPane;

    @FXML private Button btnAccueil;
    @FXML private Button btnCours;
    @FXML private Button btnProjets;
    @FXML private Button btnEvents;
    @FXML private Button btnCarriere;

    // Chatbot overlay elements
    @FXML private VBox   chatbotPanel;
    @FXML private Button btnChat;

    private boolean chatbotOpen = false;

    private ProjectService projectService = new ProjectService();
    private DepotService   depotService   = new DepotService();

    @FXML
    public void initialize() {
        handleDashboardAction(null);
        initChatbot();
    }

    // ── Navigation ──────────────────────────────────────────────────────────

    private void setActiveButton(Button activeBtn) {
        List<Button> buttons = Arrays.asList(btnAccueil, btnCours, btnProjets, btnEvents, btnCarriere);
        for (Button btn : buttons) {
            if (btn != null) btn.getStyleClass().remove("active");
        }
        if (activeBtn != null) activeBtn.getStyleClass().add("active");
    }

    @FXML
    private void handleDashboardAction(ActionEvent event) {
        setActiveButton(btnAccueil);
        loadView("/com/innolearn/Dashboard.fxml");
    }

    @FXML
    private void handleCoursAction(ActionEvent event) {
        setActiveButton(btnCours);
        loadView("/com/innolearn/Cours.fxml");
    }

    @FXML
    private void handleProjectsAction(ActionEvent event) {
        setActiveButton(btnProjets);
        loadView("/com/innolearn/ProjectList.fxml");
    }

    @FXML
    private void handleDepotsAction(ActionEvent event) {
        setActiveButton(btnProjets);
        loadView("/com/innolearn/DepotList.fxml");
    }

    @FXML
    private void handleEventsAction(ActionEvent event) {
        setActiveButton(btnEvents);
        loadView("/com/innolearn/Events.fxml");
    }

    // ── Chatbot Toggle ───────────────────────────────────────────────────────

    /** Called when the floating 💬 FAB button is clicked. */
    @FXML
    private void handleToggleChat(ActionEvent event) {
        chatbotOpen = !chatbotOpen;
        chatbotPanel.setVisible(chatbotOpen);
        chatbotPanel.setManaged(chatbotOpen);
        btnChat.setText(chatbotOpen ? "\u274C" : "\uD83D\uDCAC");
    }

    /** Load the Chatbot.fxml into the chatbot panel VBox (once). */
    private void initChatbot() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/Chatbot.fxml"));
            Node chatNode = loader.load();
            chatbotPanel.getChildren().setAll(chatNode);
        } catch (IOException e) {
            e.printStackTrace();
            System.err.println("Could not load Chatbot.fxml");
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private void loadView(String fxmlPath) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
            Node view = loader.load();
            contentPane.getChildren().setAll(view);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
