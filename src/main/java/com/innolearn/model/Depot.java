package com.innolearn.model;

import java.sql.Timestamp;

public class Depot {
    private int id;
    private String title;
    private String description;
    private String type;
    private String filePath;   // From SQL: file_path
    private String fileSize;   // From SQL: file_size
    private String fileType;   // From SQL: file_type
    private Timestamp uploadedAt; // From SQL: uploaded_at
    private int projectId;     // From SQL: project_id
    private String studentName; // From SQL: student_name
    private int downloadCount;  // From SQL: download_count
    private Integer userId;     // From SQL: user_id (nullable)
    private String todoStatus;   // From SQL: todo_status (Done | Doing | Didn't Do)
    private String aiResult;     // From SQL: ai_result (full AI feedback text)
    private Integer aiScore;     // From SQL: ai_score (0-100)

    public Depot() {}

    public Depot(int id, String title, String description, String type, String filePath, String fileSize, String fileType, Timestamp uploadedAt, int projectId, String studentName, int downloadCount, Integer userId) {
        this.id = id;
        this.title = title;
        this.description = description;
        this.type = type;
        this.filePath = filePath;
        this.fileSize = fileSize;
        this.fileType = fileType;
        this.uploadedAt = uploadedAt;
        this.projectId = projectId;
        this.studentName = studentName;
        this.downloadCount = downloadCount;
        this.userId = userId;
        this.todoStatus = "Doing";
    }

    public Depot(int id, String title, String description, String type, String filePath, String fileSize, String fileType, Timestamp uploadedAt, int projectId, String studentName, int downloadCount, Integer userId, String todoStatus) {
        this(id, title, description, type, filePath, fileSize, fileType, uploadedAt, projectId, studentName, downloadCount, userId);
        this.todoStatus = todoStatus;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    public String getType() { return type; }
    public void setType(String type) { this.type = type; }
    public String getFilePath() { return filePath; }
    public void setFilePath(String filePath) { this.filePath = filePath; }
    public String getFileSize() { return fileSize; }
    public void setFileSize(String fileSize) { this.fileSize = fileSize; }
    public String getFileType() { return fileType; }
    public void setFileType(String fileType) { this.fileType = fileType; }
    public Timestamp getUploadedAt() { return uploadedAt; }
    public void setUploadedAt(Timestamp uploadedAt) { this.uploadedAt = uploadedAt; }
    public int getProjectId() { return projectId; }
    public void setProjectId(int projectId) { this.projectId = projectId; }
    public String getStudentName() { return studentName; }
    public void setStudentName(String studentName) { this.studentName = studentName; }
    public int getDownloadCount() { return downloadCount; }
    public void setDownloadCount(int downloadCount) { this.downloadCount = downloadCount; }
    public Integer getUserId() { return userId; }
    public void setUserId(Integer userId) { this.userId = userId; }
    public String getTodoStatus() { return todoStatus; }
    public void setTodoStatus(String todoStatus) { this.todoStatus = todoStatus; }
    public String getAiResult() { return aiResult; }
    public void setAiResult(String aiResult) { this.aiResult = aiResult; }
    public Integer getAiScore() { return aiScore; }
    public void setAiScore(Integer aiScore) { this.aiScore = aiScore; }

    @Override
    public String toString() {
        return "Depot{" + "id=" + id + ", title='" + title + '\'' + ", projectId=" + projectId + '}';
    }
}
