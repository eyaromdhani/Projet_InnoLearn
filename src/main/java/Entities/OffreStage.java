package Entities;

import java.sql.Timestamp; // Utilisation de Timestamp pour le type DATETIME de MySQL

public class OffreStage {
    private int id;
    private String titre;
    private String description;
    private String entreprise;
    private String lieu;
    private String domaine;
    private String competences;
    private int duree;
    private Timestamp date_publication;
    private String statut;
    private Integer id_recruteur;

    // Constructeur par défaut
    public OffreStage() {}

    // Constructeur parametre
    public OffreStage(String titre, String description, String entreprise, String lieu,
                      String domaine, String competences, int duree,
                      Timestamp date_publication, String statut, Integer id_recruteur) {
        this.titre = titre;
        this.description = description;
        this.entreprise = entreprise;
        this.lieu = lieu;
        this.domaine = domaine;
        this.competences = competences;
        this.duree = duree;
        this.date_publication = date_publication;
        this.statut = statut;
        this.id_recruteur = id_recruteur;
    }

    // Getters et Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getTitre() { return titre; }
    public void setTitre(String titre) { this.titre = titre; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getEntreprise() { return entreprise; }
    public void setEntreprise(String entreprise) { this.entreprise = entreprise; }

    public String getLieu() { return lieu; }
    public void setLieu(String lieu) { this.lieu = lieu; }

    public String getDomaine() { return domaine; }
    public void setDomaine(String domaine) { this.domaine = domaine; }

    public String getCompetences() { return competences; }
    public void setCompetences(String competences) { this.competences = competences; }

    public int getDuree() { return duree; }
    public void setDuree(int duree) { this.duree = duree; }

    public Timestamp getDate_publication() { return date_publication; }
    public void setDate_publication(Timestamp date_publication) { this.date_publication = date_publication; }

    public String getStatut() { return statut; }
    public void setStatut(String statut) { this.statut = statut; }

    public Integer getId_recruteur() { return id_recruteur; }
    public void setId_recruteur(Integer id_recruteur) { this.id_recruteur = id_recruteur; }
}