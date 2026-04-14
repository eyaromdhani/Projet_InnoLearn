import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.net.URL;

public class MainFX extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {
        URL resource = getClass().getResource("/fxml/Stages.fxml");
        if (resource == null) {
            System.err.println("Cannot find /fxml/Home.fxml");
            System.exit(1);
        }
        Parent root = FXMLLoader.load(resource);
        Scene scene = new Scene(root, 1100, 700);
        
        URL css = getClass().getResource("/css/style.css");
        if(css != null) {
            scene.getStylesheets().add(css.toExternalForm());
        }

        primaryStage.setTitle("InnoLearn - Gestion des Stages");
        primaryStage.setScene(scene);
        // Center on screen
        primaryStage.centerOnScreen();
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}
