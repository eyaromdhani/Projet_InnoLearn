/**
 * Classe de lancement (Launcher) pour contourner les restrictions de module de JavaFX.
 * Cette classe n'hérite pas de Application, ce qui permet de lancer JavaFX depuis le classpath.
 */
public class MainApp {
    public static void main(String[] args) {
        HelloFX.main(args);
    }
}
