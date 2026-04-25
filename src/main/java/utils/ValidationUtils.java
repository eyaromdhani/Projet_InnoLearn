package utils;

import javafx.scene.control.Control;
import javafx.scene.control.TextInputControl;

public class ValidationUtils {

    /**
     * Checks if a string is null, empty or only contains whitespace.
     */
    public static boolean isEmpty(String value) {
        return value == null || value.trim().isEmpty();
    }

    /**
     * Checks if a string is a valid integer.
     */
    public static boolean isNumeric(String value) {
        if (isEmpty(value)) return false;
        try {
            Integer.parseInt(value);
            return true;
        } catch (NumberFormatException e) {
            return false;
        }
    }

    /**
     * Checks if a string is a positive integer.
     */
    public static boolean isPositive(String value) {
        if (!isNumeric(value)) return false;
        return Integer.parseInt(value) > 0;
    }

    /**
     * Checks if a string length is within specified bounds.
     */
    public static boolean isValidLength(String value, int min, int max) {
        if (value == null) return false;
        int length = value.trim().length();
        return length >= min && length <= max;
    }

    /**
     * Applies an error style (red border) to a control.
     */
    public static void setErrorStyle(Control control) {
        control.setStyle("-fx-border-color: #EF4444; -fx-border-width: 1.5px; -fx-border-radius: 6px;");
    }

    /**
     * Clears any special styling from a control.
     */
    public static void clearErrorStyle(Control control) {
        control.setStyle("");
    }
}
