package services;

import models.Book;
import utils.MyDataBase;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ServiceBook {

    private Connection connection;

    public ServiceBook() {
        this.connection = MyDataBase.getInstance().getConnection();
        if (this.connection == null) {
            System.err.println("❌ Critical: Database connection could not be established in ServiceBook.");
        }
    }

    public void ajouter(Book book) throws SQLException {
        if (connection == null) throw new SQLException("Database connection is null");
        String req = "INSERT INTO book (titre, author, description, publier, pdf_path) VALUES (?, ?, ?, ?, ?)";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, book.getTitre());
        ps.setString(2, book.getAuthor());
        ps.setString(3, book.getDescription());
        ps.setDate(4, Date.valueOf(book.getReleaseDate()));
        ps.setString(5, book.getPdfPath());
        ps.executeUpdate();
        System.out.println("Livre ajouté avec succès dans la table 'book' !");
    }

    public void modifier(Book book) throws SQLException {
        if (connection == null) throw new SQLException("Database connection is null");
        String req = "UPDATE book SET titre = ?, author = ?, description = ?, publier = ?, pdf_path = ? WHERE id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, book.getTitre());
        ps.setString(2, book.getAuthor());
        ps.setString(3, book.getDescription());
        ps.setDate(4, Date.valueOf(book.getReleaseDate()));
        ps.setString(5, book.getPdfPath());
        ps.setInt(6, book.getId());
        ps.executeUpdate();
    }

    public void supprimer(int id) throws SQLException {
        if (connection == null) throw new SQLException("Database connection is null");
        String req = "DELETE FROM book WHERE id = ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setInt(1, id);
        ps.executeUpdate();
    }

    public List<Book> afficher() throws SQLException {
        List<Book> list = new ArrayList<>();
        if (connection == null) return list;
        String req = "SELECT * FROM book";
        Statement st = connection.createStatement();
        ResultSet rs = st.executeQuery(req);
        while (rs.next()) {
            list.add(new Book(
                    rs.getInt("id"),
                    rs.getString("titre"),
                    rs.getString("author"),
                    rs.getString("description"),
                    rs.getDate("publier").toLocalDate(),
                    rs.getString("pdf_path")
            ));
        }
        return list;
    }

    public List<Book> rechercherParTitre(String titre) throws SQLException {
        List<Book> list = new ArrayList<>();
        if (connection == null) return list;
        String req = "SELECT * FROM book WHERE titre LIKE ?";
        PreparedStatement ps = connection.prepareStatement(req);
        ps.setString(1, "%" + titre + "%");
        ResultSet rs = ps.executeQuery();
        while (rs.next()) {
            list.add(new Book(
                    rs.getInt("id"),
                    rs.getString("titre"),
                    rs.getString("author"),
                    rs.getString("description"),
                    rs.getDate("publier").toLocalDate(),
                    rs.getString("pdf_path")
            ));
        }
        return list;
    }
}
