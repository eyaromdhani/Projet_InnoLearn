package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Label;
import models.Book;

import java.awt.*;
import java.io.File;
import java.io.IOException;

public class StudentBookCardController {

    @FXML
    private Label titleLabel;

    @FXML
    private Label authorLabel;

    @FXML
    private Label authorBadge;

    @FXML
    private Label yearLabel;

    private Book currentBook;
    private StudentLibraryController parentController;

    public void setBookData(Book b, StudentLibraryController parent) {
        this.currentBook = b;
        this.parentController = parent;
        titleLabel.setText(b.getTitre());
        authorLabel.setText(b.getAuthor());
        authorBadge.setText(b.getAuthor().toUpperCase());
        if (b.getReleaseDate() != null) {
            yearLabel.setText(String.valueOf(b.getReleaseDate().getYear()));
        }
    }

    @FXML
    private void handleOpenDetails() {
        if (parentController != null) {
            parentController.openBookDetails(currentBook);
        }
    }

    @FXML
    private void handleOpenPDF() {
        if (currentBook.getPdfPath() == null || currentBook.getPdfPath().isEmpty()) {
            showAlert(Alert.AlertType.WARNING, "Attention", "Aucun fichier PDF n'est associé à ce livre.");
            return;
        }

        File file = new File(currentBook.getPdfPath());
        if (!file.exists()) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Le fichier PDF est introuvable sur le disque.");
            return;
        }

        try {
            Desktop.getDesktop().open(file);
        } catch (IOException e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Impossible d'ouvrir le fichier : " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.show();
    }
}
