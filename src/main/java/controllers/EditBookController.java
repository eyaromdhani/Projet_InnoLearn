package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Label;
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

public class EditBookController {

    @FXML private Label headerTitle;
    @FXML private TextField titleField;
    @FXML private TextField authorField;
    @FXML private TextArea descArea;
    @FXML private DatePicker datePicker;
    @FXML private TextField pdfPathField;

    private ServiceBook serviceBook = new ServiceBook();
    private Book currentBook;

    public void initData(Book b) {
        this.currentBook = b;
        headerTitle.setText("Modification : " + b.getTitre());
        titleField.setText(b.getTitre());
        authorField.setText(b.getAuthor());
        descArea.setText(b.getDescription());
        datePicker.setValue(b.getReleaseDate());
        pdfPathField.setText(b.getPdfPath());
    }

    @FXML
    private void handleChoosePDF() {
        FileChooser fileChooser = new FileChooser();
        File file = fileChooser.showOpenDialog(new Stage());
        if (file != null) pdfPathField.setText(file.getAbsolutePath());
    }

    @FXML
    private void handleUpdate() {
        currentBook.setTitre(titleField.getText());
        currentBook.setAuthor(authorField.getText());
        currentBook.setDescription(descArea.getText());
        currentBook.setReleaseDate(datePicker.getValue());
        currentBook.setPdfPath(pdfPathField.getText());

        try {
            serviceBook.modifier(currentBook);
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setContentText("Livre mis à jour avec succès !");
            alert.showAndWait();
            handleCancel();
        } catch (Exception e) {
            System.err.println("DEBUG: Error in handleUpdate: " + e.getMessage());
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setContentText("Erreur: " + e.getMessage());
            alert.show();
            e.printStackTrace();
        }
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
