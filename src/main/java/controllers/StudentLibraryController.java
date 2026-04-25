package controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.TextField;
import javafx.scene.layout.FlowPane;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import models.Book;
import services.ServiceBook;

import java.io.IOException;
import java.net.URL;
import java.util.List;
import java.util.ResourceBundle;

public class StudentLibraryController implements Initializable {

    @FXML
    private FlowPane booksGrid;

    @FXML
    private TextField searchField;

    private ServiceBook serviceBook = new ServiceBook();

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        loadBooks();
        setupSearch();
    }

    public void loadBooks() {
        booksGrid.getChildren().clear();
        try {
            List<Book> books = serviceBook.afficher();
            for (Book b : books) {
                addBookCard(b);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void addBookCard(Book b) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/StudentBookCard.fxml"));
            VBox card = loader.load();
            
            StudentBookCardController controller = loader.getController();
            controller.setBookData(b, this);
            
            booksGrid.getChildren().add(card);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void openBookDetails(Book b) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/BookDetails.fxml"));
            Parent root = loader.load();
            
            BookDetailsController controller = loader.getController();
            controller.initData(b);

            Stage stage = (Stage) booksGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void setupSearch() {
        searchField.textProperty().addListener((obs, old, val) -> {
            filterBooks(val);
        });
    }

    private void filterBooks(String query) {
        booksGrid.getChildren().clear();
        try {
            List<Book> filtered = serviceBook.rechercherParTitre(query);
            for (Book b : filtered) {
                addBookCard(b);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGoBack() {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/StudentDashboard.fxml"));
            Stage stage = (Stage) booksGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGoToQuiz() {
        handleGoBack();
    }
}
