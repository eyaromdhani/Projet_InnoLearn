package models;

public class Question {
    private int id;
    private String questionText;
    private String correctAnswer;
    private int points;
    private String type;
    private int formulaireId;

    public Question() {}

    public Question(String questionText, String correctAnswer, int points, String type, int formulaireId) {
        this.questionText = questionText;
        this.correctAnswer = correctAnswer;
        this.points = points;
        this.type = type;
        this.formulaireId = formulaireId;
    }

    public Question(int id, String questionText, String correctAnswer, int points, String type, int formulaireId) {
        this.id = id;
        this.questionText = questionText;
        this.correctAnswer = correctAnswer;
        this.points = points;
        this.type = type;
        this.formulaireId = formulaireId;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getQuestionText() {
        return questionText;
    }

    public void setQuestionText(String questionText) {
        this.questionText = questionText;
    }

    public String getCorrectAnswer() {
        return correctAnswer;
    }

    public void setCorrectAnswer(String correctAnswer) {
        this.correctAnswer = correctAnswer;
    }

    public int getPoints() {
        return points;
    }

    public void setPoints(int points) {
        this.points = points;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public int getFormulaireId() {
        return formulaireId;
    }

    public void setFormulaireId(int formulaireId) {
        this.formulaireId = formulaireId;
    }

    @Override
    public String toString() {
        return "Question{" +
                "id=" + id +
                ", questionText='" + questionText + '\'' +
                ", points=" + points +
                ", type='" + type + '\'' +
                ", formulaireId=" + formulaireId +
                '}';
    }
}
