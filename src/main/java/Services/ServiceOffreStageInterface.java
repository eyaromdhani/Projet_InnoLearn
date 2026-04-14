package Services;

import Entities.OffreStage;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.List;

public interface ServiceOffreStageInterface {


    public abstract void ajouter(OffreStage os) throws SQLException;
    public abstract List<OffreStage> afficherAll() throws SQLException ;
    public abstract OffreStage getById(int id) throws SQLException ;
    public abstract void modifier(OffreStage os) throws SQLException ;
    public abstract void supprimer(int id) throws SQLException ;



    }
