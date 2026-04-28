package com.innolearn.model;

import java.sql.Date;
import java.sql.Timestamp;

public class Project {
    private int id;
    private String title;
    private String description;
    private String status;
    private Date startDate;
    private Date endDate;
    private Timestamp createdAt;
    private Timestamp updatedAt;
    private String generatedImage; // From SQL: generated_image
    private String difficulty;     // From SQL: difficulty

    public Project() {}

    public Project(int id, String title, String description, String status, Date startDate, Date endDate, Timestamp createdAt, Timestamp updatedAt, String generatedImage, String difficulty) {
        this.id = id;
        this.title = title;
        this.description = description;
        this.status = status;
        this.startDate = startDate;
        this.endDate = endDate;
        this.createdAt = createdAt;
        this.updatedAt = updatedAt;
        this.generatedImage = generatedImage;
        this.difficulty = difficulty;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    public Date getStartDate() { return startDate; }
    public void setStartDate(Date startDate) { this.startDate = startDate; }
    public Date getEndDate() { return endDate; }
    public void setEndDate(Date endDate) { this.endDate = endDate; }
    public Timestamp getCreatedAt() { return createdAt; }
    public void setCreatedAt(Timestamp createdAt) { this.createdAt = createdAt; }
    public Timestamp getUpdatedAt() { return updatedAt; }
    public void setUpdatedAt(Timestamp updatedAt) { this.updatedAt = updatedAt; }
    public String getGeneratedImage() { return generatedImage; }
    public void setGeneratedImage(String generatedImage) { this.generatedImage = generatedImage; }
    public String getDifficulty() { return difficulty; }
    public void setDifficulty(String difficulty) { this.difficulty = difficulty; }

    @Override
    public String toString() {
        return "Project{" + "id=" + id + ", title='" + title + '\'' + '}';
    }
}
