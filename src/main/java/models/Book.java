package models;

import java.time.LocalDate;

public class Book {
    private int id;
    private String titre;
    private String author;
    private String description;
    private LocalDate releaseDate;
    private String pdfPath;

    public Book() {}

    public Book(String titre, String author, String description, LocalDate releaseDate, String pdfPath) {
        this.titre = titre;
        this.author = author;
        this.description = description;
        this.releaseDate = releaseDate;
        this.pdfPath = pdfPath;
    }

    public Book(int id, String titre, String author, String description, LocalDate releaseDate, String pdfPath) {
        this.id = id;
        this.titre = titre;
        this.author = author;
        this.description = description;
        this.releaseDate = releaseDate;
        this.pdfPath = pdfPath;
    }

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getTitre() { return titre; }
    public void setTitre(String titre) { this.titre = titre; }

    public String getAuthor() { return author; }
    public void setAuthor(String author) { this.author = author; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public LocalDate getReleaseDate() { return releaseDate; }
    public void setReleaseDate(LocalDate releaseDate) { this.releaseDate = releaseDate; }

    public String getPdfPath() { return pdfPath; }
    public void setPdfPath(String pdfPath) { this.pdfPath = pdfPath; }

    @Override
    public String toString() {
        return "Book{" +
                "id=" + id +
                ", titre='" + titre + '\'' +
                ", author='" + author + '\'' +
                '}';
    }
}
