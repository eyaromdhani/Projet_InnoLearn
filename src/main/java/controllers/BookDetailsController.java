package controllers;

import javafx.embed.swing.SwingFXUtils;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.ScrollPane;
import javafx.scene.image.ImageView;
import javafx.scene.image.WritableImage;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import models.Book;
import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.rendering.PDFRenderer;
import org.apache.pdfbox.text.PDFTextStripper;
import services.GroqService;

import java.awt.Desktop;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;

public class BookDetailsController {

    @FXML private Label titleLabel;
    @FXML private Label authorLabel;
    @FXML private Label yearLabel;
    @FXML private Label descLabel;
    @FXML private ScrollPane pdfScrollPane;
    @FXML private VBox pdfPagesContainer;
    @FXML private VBox fallbackView;
    @FXML private Button stopButton;

    private Book currentBook;
    private GroqService groqService = new GroqService();
    private Process currentSpeechProcess;

    public void initData(Book b) {
        this.currentBook = b;
        titleLabel.setText(b.getTitre());
        authorLabel.setText(b.getAuthor());
        if (b.getReleaseDate() != null) {
            yearLabel.setText(String.valueOf(b.getReleaseDate().getYear()));
        }
        descLabel.setText(b.getDescription());

        renderPDFInternal();
    }

    private void renderPDFInternal() {
        if (currentBook.getPdfPath() == null || currentBook.getPdfPath().isEmpty()) return;

        File file = new File(currentBook.getPdfPath());
        if (!file.exists()) return;

        new Thread(() -> {
            try (PDDocument document = PDDocument.load(file)) {
                PDFRenderer renderer = new PDFRenderer(document);
                int pageCount = Math.min(document.getNumberOfPages(), 5);
                
                for (int i = 0; i < pageCount; i++) {
                    BufferedImage bim = renderer.renderImageWithDPI(i, 100);
                    WritableImage img = SwingFXUtils.toFXImage(bim, null);
                    
                    javafx.application.Platform.runLater(() -> {
                        ImageView iv = new ImageView(img);
                        iv.setPreserveRatio(true);
                        iv.setFitWidth(550);
                        pdfPagesContainer.getChildren().add(iv);
                    });
                }

                javafx.application.Platform.runLater(() -> {
                    pdfScrollPane.setVisible(true);
                    fallbackView.setVisible(false);
                });

            } catch (IOException e) {
                e.printStackTrace();
            }
        }).start();
    }

    @FXML
    private void handleBack() {
        try {
            Parent root = FXMLLoader.load(getClass().getResource("/StudentLibrary.fxml"));
            Stage stage = (Stage) titleLabel.getScene().getWindow();
            stage.setScene(new Scene(root));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleAIAnalysis() {
        String prompt = "Analyse ce livre intitulé '" + currentBook.getTitre() + "' de " + currentBook.getAuthor() + 
                        ". Description: " + currentBook.getDescription() + ". Donne des points clés pédagogiques.";
        
        callGroqContent(prompt, "Analyse IA Pédagogique");
    }

    @FXML
    private void handleSummary() {
        // Extract text from first 2 pages
        String text = extractTextFromPDF(2);
        if (text.isEmpty()) {
            text = currentBook.getDescription();
        }

        String prompt = "Fais un résumé structuré et court de ce texte extrait d'un livre : " + text;
        callGroqContent(prompt, "Résumé IA du Livre");
    }

    private void callGroqContent(String prompt, String title) {
        javafx.application.Platform.runLater(() -> showAlert(Alert.AlertType.INFORMATION, title, "L'IA réfléchit... un instant."));
        
        groqService.getChatCompletion(prompt).thenAccept(response -> {
            javafx.application.Platform.runLater(() -> {
                Alert alert = new Alert(Alert.AlertType.INFORMATION);
                alert.setTitle(title);
                alert.setHeaderText(currentBook.getTitre());
                alert.setContentText(response);
                alert.getDialogPane().setPrefSize(600, 400);
                alert.show();
            });
        }).exceptionally(ex -> {
            javafx.application.Platform.runLater(() -> showAlert(Alert.AlertType.ERROR, "Erreur AI", ex.getMessage()));
            return null;
        });
    }

    private String extractTextFromPDF(int maxPages) {
        if (currentBook.getPdfPath() == null) return "";
        File file = new File(currentBook.getPdfPath());
        if (!file.exists()) return "";

        try (PDDocument document = PDDocument.load(file)) {
            PDFTextStripper stripper = new PDFTextStripper();
            stripper.setEndPage(Math.min(document.getNumberOfPages(), maxPages));
            return stripper.getText(document);
        } catch (IOException e) {
            return "";
        }
    }

    @FXML
    private void handleVoice() {
        handleStopVoice(); // Stop any existing reading first

        String text = extractTextFromPDF(1);
        if (text.isEmpty()) {
            text = "Le contenu du livre est indisponible pour la lecture vocale.";
        }

        // ULTR-STRICT CLEANING for SAPI
        String cleanedText = text.replace("©", " ").replace("Copyright", " ").replace("gts", "G T S").replace("Vlan", "V Lan");
        
        // Remove everything except standard characters and French accents
        cleanedText = cleanedText.replaceAll("[^a-zA-Z0-9àâäéèêëîïôöûüùçÀÂÄÉÈÊËÎÏÔÖÛÜÙÇ,.!?;:() \\-']", " ");
        
        cleanedText = cleanedText.replace("\"", "'").replace("\n", " ").replace("\r", " ").trim();
        cleanedText = cleanedText.replaceAll("\\s+", " "); // Collapse multiple spaces
        if (cleanedText.length() > 500) cleanedText = cleanedText.substring(0, 500);

        String finalContent = cleanedText;
        
        System.out.println("DEBUG: AI Vocal Attempt starting (cleaned text)...");
        stopButton.setVisible(true);

        new Thread(() -> {
            try {
                File tempVBS = File.createTempFile("speech", ".vbs");
                tempVBS.deleteOnExit();
                
                String vbsCode = "On Error Resume Next\n" +
                                 "Set voice = CreateObject(\"SAPI.SpVoice\")\n" +
                                 "voice.Speak \"" + finalContent.replace("\"", "\"\"") + "\"";
                
                // Write with UTF-16LE + BOM for perfect character support on Windows
                java.io.FileOutputStream fos = new java.io.FileOutputStream(tempVBS);
                fos.write(255); // BOM LE
                fos.write(254); // BOM LE
                fos.write(vbsCode.getBytes("UTF-16LE"));
                fos.close();
                
                currentSpeechProcess = new ProcessBuilder("wscript.exe", tempVBS.getAbsolutePath()).start();
                
                // Wait for process to end to hide stop button
                currentSpeechProcess.waitFor();
                javafx.application.Platform.runLater(() -> stopButton.setVisible(false));
                
            } catch (Exception e) {
                e.printStackTrace();
            }
        }).start();
    }

    @FXML
    private void handleStopVoice() {
        if (currentSpeechProcess != null && currentSpeechProcess.isAlive()) {
            currentSpeechProcess.destroy();
            stopButton.setVisible(false);
            // Kill all wscript processes to be sure (optional but safer on some Windows)
            try { new ProcessBuilder("taskkill", "/F", "/IM", "wscript.exe").start(); } catch (Exception e) {}
        }
    }

    @FXML
    private void handleOpenExternal() {
        if (currentBook.getPdfPath() != null) {
            try {
                Desktop.getDesktop().open(new File(currentBook.getPdfPath()));
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.show();
    }
}
