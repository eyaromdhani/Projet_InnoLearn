package Controllers;

import javafx.fxml.Initializable;
import javafx.fxml.FXML;
import javafx.scene.image.Image;
import javafx.scene.paint.ImagePattern;
import javafx.scene.shape.Circle;
import java.net.URL;
import java.util.ResourceBundle;

public class HomeController implements Initializable {
    
    @FXML
    private Circle heroCircle;

    @FXML
    private NavbarController navbarController;
    
    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        // Highlight active link
        if (navbarController != null) {
            navbarController.setActiveLink("Accueil");
        }
        // Load the hero image and set it as the fill for the circle
        try {
            URL imageResource = getClass().getResource("/assets/hero_learning.png");
            if (imageResource != null) {
                Image image = new Image(imageResource.toExternalForm());
                heroCircle.setFill(new ImagePattern(image));
            } else {
                System.err.println("Could not find hero image at /assets/hero_learning.png");
            }
        } catch (Exception e) {
            System.err.println("Error loading hero image: " + e.getMessage());
        }
    }
}
