package Entities;

import java.sql.Date;

public class StageCondidature {
    private int id;
    private String type_request;
    private String titre;
    private String description;
    private String domaine;
    private String competences;
    private String cv;
    private String lettre_motivation;
    private Date date_publication;
    private String statut;
    private Integer id_etudiant;
    private Integer id_offre;

    public StageCondidature() {}

    public StageCondidature(String type_request, String titre, String description, String domaine,
                            String competences, String cv, String lettre_motivation,
                            Date date_publication, String statut, Integer id_etudiant, Integer id_offre) {
        this.type_request = type_request;
        this.titre = titre;
        this.description = description;
        this.domaine = domaine;
        this.competences = competences;
        this.cv = cv;
        this.lettre_motivation = lettre_motivation;
        this.date_publication = date_publication;
        this.statut = statut;
        this.id_etudiant = id_etudiant;
        this.id_offre = id_offre;
    }
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getType_request() { return type_request; }
    public void setType_request(String type_request) { this.type_request = type_request; }

    public String getTitre() { return titre; }
    public void setTitre(String titre) { this.titre = titre; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getDomaine() { return domaine; }
    public void setDomaine(String domaine) { this.domaine = domaine; }

    public String getCompetences() { return competences; }
    public void setCompetences(String competences) { this.competences = competences; }

    public String getCv() { return cv; }
    public void setCv(String cv) { this.cv = cv; }

    public String getLettre_motivation() { return lettre_motivation; }
    public void setLettre_motivation(String lettre_motivation) { this.lettre_motivation = lettre_motivation; }

    public Date getDate_publication() { return date_publication; }
    public void setDate_publication(Date date_publication) { this.date_publication = date_publication; }

    public String getStatut() { return statut; }
    public void setStatut(String statut) { this.statut = statut; }

    public Integer getId_etudiant() { return id_etudiant; }
    public void setId_etudiant(Integer id_etudiant) { this.id_etudiant = id_etudiant; }

    public Integer getId_offre() { return id_offre; }
    public void setId_offre(Integer id_offre) { this.id_offre = id_offre; }
}