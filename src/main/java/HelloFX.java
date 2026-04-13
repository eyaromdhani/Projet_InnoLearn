import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;

/**
 * Classe principale de l'application JavaFX.
 * Charge l'interface de gestion des formulaires.
 */
public class HelloFX extends Application {

    @Override
    public void start(Stage stage) {
        try {
            // Chargement de la vue FXML
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/SelectionView.fxml"));
            Parent root = loader.load();
            
            // Création de la scène avec le fichier CSS inclus via FXML
            Scene scene = new Scene(root);
            
            stage.setScene(scene);
            stage.setTitle("Gestion des Formulaires - InnoLearn");
            stage.show();
            
        } catch (IOException e) {
            System.err.println("Erreur lors du chargement de l'interface FXML : " + e.getMessage());
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch();
    }
}
