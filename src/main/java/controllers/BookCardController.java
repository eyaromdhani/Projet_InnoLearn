package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import models.Book;
import services.ServiceBook;

import java.sql.SQLException;

public class BookCardController {

    @FXML
    private Label titleLabel;

    @FXML
    private Label authorLabel;

    private Book currentBook;
    private ManageBooksController parentController;
    private ServiceBook serviceBook = new ServiceBook();

    public void setBookData(Book b, ManageBooksController parent) {
        this.currentBook = b;
        this.parentController = parent;

        titleLabel.setText(b.getTitre());
        authorLabel.setText("Par " + b.getAuthor());
    }

    @FXML
    private void handleEdit() {
        parentController.openEditDialog(currentBook);
    }

    @FXML
    private void handleDelete() {
        try {
            serviceBook.supprimer(currentBook.getId());
            parentController.loadBooks();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
