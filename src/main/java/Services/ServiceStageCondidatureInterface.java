package Services;

import Entities.StageCondidature;

import java.sql.SQLException;
import java.util.List;

public interface ServiceStageCondidatureInterface {
    public void ajouter(StageCondidature sc) throws SQLException ;
    public void supprimer(int id) throws SQLException ;
    public void modifier(StageCondidature sc) throws SQLException ;
    public List<StageCondidature> afficherAll() throws SQLException ;
    public StageCondidature getById(int id) throws SQLException ;
    public StageCondidature getProfileEtudiant(int idEtudiant) throws SQLException ;

    }
