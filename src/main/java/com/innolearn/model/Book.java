package com.innolearn.model;

import java.sql.Date;

public class Book {
    private int id;
    private String title;
    private String author;
    private String description;
    private Date publishedAt;

    public Book() {}

    public Book(int id, String title, String author, String description, Date publishedAt) {
        this.id = id;
        this.title = title;
        this.author = author;
        this.description = description;
        this.publishedAt = publishedAt;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }
    public String getAuthor() { return author; }
    public void setAuthor(String author) { this.author = author; }
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    public Date getPublishedAt() { return publishedAt; }
    public void setPublishedAt(Date publishedAt) { this.publishedAt = publishedAt; }

    @Override
    public String toString() {
        return "Book{" + "id=" + id + ", title='" + title + '\'' + ", author='" + author + '\'' + '}';
    }
}
