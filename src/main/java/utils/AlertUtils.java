package utils;

import javafx.scene.control.Alert;

public class AlertUtils {

    public static void showError(String title, String message) {
        showAlert(Alert.AlertType.ERROR, title, "Erreur", message);
    }

    public static void showInfo(String title, String message) {
        showAlert(Alert.AlertType.INFORMATION, title, "Information", message);
    }

    public static void showWarning(String title, String message) {
        showAlert(Alert.AlertType.WARNING, title, "Avertissement", message);
    }

    private static void showAlert(Alert.AlertType type, String title, String header, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
