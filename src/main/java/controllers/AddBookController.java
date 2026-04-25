package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.DatePicker;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.stage.FileChooser;
import javafx.stage.Stage;
import models.Book;
import services.ServiceBook;

import java.io.File;
import java.io.IOException;
import java.sql.SQLException;
import java.time.LocalDate;

public class AddBookController {

    @FXML private TextField titleField;
    @FXML private TextField authorField;
    @FXML private TextArea descArea;
    @FXML private DatePicker datePicker;
    @FXML private TextField pdfPathField;

    private ServiceBook serviceBook = new ServiceBook();

    @FXML
    private void handleChoosePDF() {
        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Choisir le fichier PDF");
        fileChooser.getExtensionFilters().addAll(
                new FileChooser.ExtensionFilter("PDF Files", "*.pdf")
        );
        File file = fileChooser.showOpenDialog(new Stage());
        if (file != null) {
            pdfPathField.setText(file.getAbsolutePath());
        }
    }

    @FXML
    private void handleSave() {
        String title = titleField.getText().trim();
        String author = authorField.getText().trim();
        String desc = descArea.getText().trim();
        LocalDate date = datePicker.getValue();
        String pdf = pdfPathField.getText();

        if (title.isEmpty() || author.isEmpty() || date == null) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Veuillez remplir tous les champs obligatoires.");
            return;
        }

        try {
            System.out.println("DEBUG: Starting handleSave for book: " + title);
            Book b = new Book(title, author, desc, date, pdf);
            serviceBook.ajouter(b);
            showAlert(Alert.AlertType.INFORMATION, "Succès", "Livre ajouté avec succès !");
            handleCancel(); // Go back
        } catch (Exception e) {
            System.err.println("DEBUG: Error in handleSave: " + e.getMessage());
            showAlert(Alert.AlertType.ERROR, "Erreur", "Une erreur est survenue: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }

    @FXML
    private void handleCancel() {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/ManageBooks.fxml"));
            Stage stage = (Stage) titleField.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
