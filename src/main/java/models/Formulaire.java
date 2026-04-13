package models;

public class Formulaire {
    private int id;
    private String titre;
    private String description;
    private int tempsLimite;
    private String category;

    public Formulaire() {}

    public Formulaire(String titre, String description, int tempsLimite, String category) {
        this.titre = titre;
        this.description = description;
        this.tempsLimite = tempsLimite;
        this.category = category;
    }

    public Formulaire(int id, String titre, String description, int tempsLimite, String category) {
        this.id = id;
        this.titre = titre;
        this.description = description;
        this.tempsLimite = tempsLimite;
        this.category = category;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getTitre() {
        return titre;
    }

    public void setTitre(String titre) {
        this.titre = titre;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public int getTempsLimite() {
        return tempsLimite;
    }

    public void setTempsLimite(int tempsLimite) {
        this.tempsLimite = tempsLimite;
    }

    public String getCategory() {
        return category;
    }

    public void setCategory(String category) {
        this.category = category;
    }

    @Override
    public String toString() {
        return "Formulaire{" +
                "id=" + id +
                ", titre='" + titre + '\'' +
                ", description='" + description + '\'' +
                ", tempsLimite=" + tempsLimite +
                ", category='" + category + '\'' +
                '}';
    }
}
