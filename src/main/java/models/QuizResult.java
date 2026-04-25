package models;

import java.time.LocalDateTime;

public class QuizResult {
    private int id;
    private String studentName;
    private int score;
    private int totalPoints;
    private LocalDateTime createdAt;
    private int formulaireId;
    private String suspiciousActivity;

    public QuizResult() {}

    public QuizResult(String studentName, int score, int totalPoints, int formulaireId) {
        this.studentName = studentName;
        this.score = score;
        this.totalPoints = totalPoints;
        this.formulaireId = formulaireId;
        this.createdAt = LocalDateTime.now();
    }

    public QuizResult(int id, String studentName, int score, int totalPoints, LocalDateTime createdAt, int formulaireId, String suspiciousActivity) {
        this.id = id;
        this.studentName = studentName;
        this.score = score;
        this.totalPoints = totalPoints;
        this.createdAt = createdAt;
        this.formulaireId = formulaireId;
        this.suspiciousActivity = suspiciousActivity;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getStudentName() { return studentName; }
    public void setStudentName(String studentName) { this.studentName = studentName; }

    public int getScore() { return score; }
    public void setScore(int score) { this.score = score; }

    public int getTotalPoints() { return totalPoints; }
    public void setTotalPoints(int totalPoints) { this.totalPoints = totalPoints; }

    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }

    public int getFormulaireId() { return formulaireId; }
    public void setFormulaireId(int formulaireId) { this.formulaireId = formulaireId; }

    public String getSuspiciousActivity() { return suspiciousActivity; }
    public void setSuspiciousActivity(String suspiciousActivity) { this.suspiciousActivity = suspiciousActivity; }

    @Override
    public String toString() {
        return "QuizResult{" +
                "studentName='" + studentName + '\'' +
                ", score=" + score +
                "/" + totalPoints +
                ", formulaireId=" + formulaireId +
                '}';
    }
}
