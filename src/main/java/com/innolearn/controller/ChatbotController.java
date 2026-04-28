package com.innolearn.controller;

import com.innolearn.service.CohereService;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.ScrollPane;
import javafx.scene.control.TextField;
import javafx.scene.input.KeyCode;
import javafx.scene.input.KeyEvent;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Region;
import javafx.scene.layout.VBox;

import java.net.URL;
import java.util.ResourceBundle;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

public class ChatbotController implements Initializable {

    @FXML private VBox       chatMessages;
    @FXML private ScrollPane chatScrollPane;
    @FXML private TextField  messageInput;
    @FXML private Button     sendButton;
    @FXML private Button     clearButton;

    private final CohereService cohereService = new CohereService();
    private final ExecutorService executor    = Executors.newSingleThreadExecutor(r -> {
        Thread t = new Thread(r, "cohere-api-thread");
        t.setDaemon(true);
        return t;
    });

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        // Auto-scroll when new messages appear
        chatMessages.heightProperty().addListener((obs, oldVal, newVal) ->
                chatScrollPane.setVvalue(1.0));

        // Send on Enter key
        messageInput.addEventHandler(KeyEvent.KEY_PRESSED, event -> {
            if (event.getCode() == KeyCode.ENTER) {
                handleSend();
            }
        });

        // Welcome message
        addBotMessage("👋 Bonjour ! Je suis **InnoBot**, ton assistant IA sur InnoLearn.\n\nPose-moi n'importe quelle question sur tes projets, cours ou programmation !");
    }

    @FXML
    private void handleSend() {
        String text = messageInput.getText().trim();
        if (text.isEmpty()) return;

        // Show user message immediately
        addUserMessage(text);
        messageInput.clear();
        setInputEnabled(false);

        // Show typing indicator
        HBox typingBubble = createTypingIndicator();
        chatMessages.getChildren().add(typingBubble);

        // Call API on background thread
        executor.submit(() -> {
            String reply = cohereService.sendMessage(text);
            Platform.runLater(() -> {
                chatMessages.getChildren().remove(typingBubble);
                addBotMessage(reply);
                setInputEnabled(true);
                messageInput.requestFocus();
            });
        });
    }

    @FXML
    private void handleClear() {
        chatMessages.getChildren().clear();
        cohereService.clearHistory();
        addBotMessage("🔄 Conversation réinitialisée. Comment puis-je t'aider ?");
    }

    // ── Message bubble builders ─────────────────────────────────────────────

    private void addUserMessage(String text) {
        HBox row = new HBox();
        row.setAlignment(Pos.CENTER_RIGHT);
        row.setPadding(new Insets(4, 0, 4, 60));

        Label bubble = new Label(text);
        bubble.setWrapText(true);
        bubble.setMaxWidth(320);
        bubble.getStyleClass().add("chat-bubble-user");

        row.getChildren().add(bubble);
        chatMessages.getChildren().add(row);
    }

    private void addBotMessage(String text) {
        HBox row = new HBox(10);
        row.setAlignment(Pos.CENTER_LEFT);
        row.setPadding(new Insets(4, 60, 4, 0));

        // Bot avatar
        Label avatar = new Label("🤖");
        avatar.getStyleClass().add("chat-avatar");

        Label bubble = new Label(text);
        bubble.setWrapText(true);
        bubble.setMaxWidth(320);
        bubble.getStyleClass().add("chat-bubble-bot");

        row.getChildren().addAll(avatar, bubble);
        chatMessages.getChildren().add(row);
    }

    private HBox createTypingIndicator() {
        HBox row = new HBox(10);
        row.setAlignment(Pos.CENTER_LEFT);
        row.setPadding(new Insets(4, 60, 4, 0));

        Label avatar = new Label("🤖");
        avatar.getStyleClass().add("chat-avatar");

        Label dots = new Label("⋯  InnoBot est en train d'écrire...");
        dots.getStyleClass().add("chat-typing");

        row.getChildren().addAll(avatar, dots);
        return row;
    }

    private void setInputEnabled(boolean enabled) {
        messageInput.setDisable(!enabled);
        sendButton.setDisable(!enabled);
    }
}
