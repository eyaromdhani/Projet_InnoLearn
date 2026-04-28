package com.innolearn.model;

import java.time.LocalDateTime;

public class Course {
    private int id;
    private String title;
    private String description;
    private String level;
    private LocalDateTime publicationDate;
    private int categoryId;

    public Course() {}

    public Course(int id, String title, String description, String level, LocalDateTime publicationDate, int categoryId) {
        this.id = id;
        this.title = title;
        this.description = description;
        this.level = level;
        this.publicationDate = publicationDate;
        this.categoryId = categoryId;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    public String getLevel() { return level; }
    public void setLevel(String level) { this.level = level; }
    public LocalDateTime getPublicationDate() { return publicationDate; }
    public void setPublicationDate(LocalDateTime publicationDate) { this.publicationDate = publicationDate; }
    public int getCategoryId() { return categoryId; }
    public void setCategoryId(int categoryId) { this.categoryId = categoryId; }

    @Override
    public String toString() {
        return "Course{" + "id=" + id + ", title='" + title + '\'' + ", level='" + level + '\'' + '}';
    }
}
