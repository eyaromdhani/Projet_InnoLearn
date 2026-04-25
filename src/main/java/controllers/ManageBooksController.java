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
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;

public class ManageBooksController implements Initializable {

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
        System.out.println("DEBUG: loadBooks() called");
        booksGrid.getChildren().clear();
        try {
            List<Book> books = serviceBook.afficher();
            System.out.println("DEBUG: Books retrieved: " + books.size());
            for (Book b : books) {
                addBookCard(b);
            }
        } catch (Exception e) {
            System.err.println("DEBUG: Error in loadBooks: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void addBookCard(Book b) {
        try {
            System.out.println("DEBUG: Adding card for: " + b.getTitre());
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/BookCard.fxml"));
            VBox card = loader.load();
            
            BookCardController controller = loader.getController();
            controller.setBookData(b, this);
            
            booksGrid.getChildren().add(card);
        } catch (Exception e) {
            System.err.println("DEBUG: Error in addBookCard: " + e.getMessage());
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
            System.err.println("DEBUG: Error in filterBooks: " + e.getMessage());
            e.printStackTrace();
        }
    }

    @FXML
    private void handleAddBook() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/AddBook.fxml"));
            Parent root = loader.load();
            Stage stage = (Stage) booksGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void openEditDialog(Book b) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/EditBook.fxml"));
            Parent root = loader.load();
            
            EditBookController controller = loader.getController();
            controller.initData(b);

            Stage stage = (Stage) booksGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGoBack() {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/AdminDashboard.fxml"));
            Stage stage = (Stage) booksGrid.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleGoToQuiz() {
        handleGoBack(); // Both go back to dashboard which contains quizzes
    }
}
