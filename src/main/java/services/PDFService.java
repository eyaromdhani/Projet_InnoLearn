package services;

import com.itextpdf.kernel.colors.ColorConstants;
import com.itextpdf.kernel.pdf.PdfDocument;
import com.itextpdf.kernel.pdf.PdfWriter;
import com.itextpdf.layout.Document;
import com.itextpdf.layout.element.Paragraph;
import com.itextpdf.layout.element.Table;
import com.itextpdf.layout.element.Cell;
import com.itextpdf.layout.properties.TextAlignment;
import com.itextpdf.layout.properties.UnitValue;
import javafx.stage.FileChooser;
import javafx.stage.Stage;
import models.Formulaire;
import models.QuizResult;

import java.io.File;
import java.io.FileOutputStream;
import java.time.format.DateTimeFormatter;

public class PDFService {

    public void generateQuizResultPDF(QuizResult result, Formulaire quiz) {
        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Enregistrer le résultat du Quiz");
        fileChooser.setInitialFileName("Resultat_" + quiz.getTitre().replaceAll("\\s+", "_") + ".pdf");
        fileChooser.getExtensionFilters().add(new FileChooser.ExtensionFilter("Fichiers PDF", "*.pdf"));
        
        File file = fileChooser.showSaveDialog(new Stage());
        
        if (file != null) {
            try {
                PdfWriter writer = new PdfWriter(new FileOutputStream(file));
                PdfDocument pdf = new PdfDocument(writer);
                Document document = new Document(pdf);

                // Title
                Paragraph title = new Paragraph("CERTIFICAT DE RÉSULTAT")
                        .setFontSize(24)
                        .setBold()
                        .setTextAlignment(TextAlignment.CENTER)
                        .setFontColor(ColorConstants.BLUE);
                document.add(title);
                
                document.add(new Paragraph("InnoLearn - Excellence Pédagogique")
                        .setTextAlignment(TextAlignment.CENTER)
                        .setItalic()
                        .setFontSize(10));

                document.add(new Paragraph("\n\n"));

                // Status Table
                Table table = new Table(UnitValue.createPercentArray(new float[]{30, 70}))
                        .useAllAvailableWidth();

                table.addCell(new Cell().add(new Paragraph("Quiz:").setBold()));
                table.addCell(new Cell().add(new Paragraph(quiz.getTitre())));

                table.addCell(new Cell().add(new Paragraph("Date:").setBold()));
                table.addCell(new Cell().add(new Paragraph(result.getCreatedAt().format(DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm")))));

                table.addCell(new Cell().add(new Paragraph("Score:").setBold()));
                table.addCell(new Cell().add(new Paragraph(result.getScore() + " / " + result.getTotalPoints())));

                double pct = (double) result.getScore() / result.getTotalPoints() * 100;
                table.addCell(new Cell().add(new Paragraph("Pourcentage:").setBold()));
                table.addCell(new Cell().add(new Paragraph(String.format("%.1f%%", pct))));

                table.addCell(new Cell().add(new Paragraph("Status:").setBold()));
                String status = (pct >= 50) ? "RÉUSSI" : "ÉCHOUÉ";
                table.addCell(new Cell().add(new Paragraph(status).setFontColor(pct >= 50 ? ColorConstants.GREEN : ColorConstants.RED)));

                document.add(table);

                document.add(new Paragraph("\n\n"));
                
                // Footer
                document.add(new Paragraph("Ce document a été généré automatiquement par la plateforme InnoLearn.")
                        .setFontSize(8)
                        .setTextAlignment(TextAlignment.CENTER)
                        .setMarginTop(50));

                document.close();
                System.out.println("PDF generated successfully: " + file.getAbsolutePath());

            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }
}
