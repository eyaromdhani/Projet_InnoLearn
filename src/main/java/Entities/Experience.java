package Entities;

public class Experience {
    private int id;
    private int user_id;
    private String type;
    private String annee;
    private String etablissement;
    private String domaine;
    private String niveau;
    private String description;

    public Experience() {
    }

    public Experience(int user_id, String type, String annee, String etablissement, String domaine, String niveau,
            String description) {
        this.user_id = user_id;
        this.type = type;
        this.annee = annee;
        this.etablissement = etablissement;
        this.domaine = domaine;
        this.niveau = niveau;
        this.description = description;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getUser_id() {
        return user_id;
    }

    public void setUser_id(int user_id) {
        this.user_id = user_id;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getAnnee() {
        return annee;
    }

    public void setAnnee(String annee) {
        this.annee = annee;
    }

    public String getEtablissement() {
        return etablissement;
    }

    public void setEtablissement(String etablissement) {
        this.etablissement = etablissement;
    }

    public String getDomaine() {
        return domaine;
    }

    public void setDomaine(String domaine) {
        this.domaine = domaine;
    }

    public String getNiveau() {
        return niveau;
    }

    public void setNiveau(String niveau) {
        this.niveau = niveau;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }
}
