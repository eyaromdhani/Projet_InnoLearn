package com.innolearn.controller;
 
import com.innolearn.model.Project;
import com.innolearn.service.ProjectService;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.layout.FlowPane;
import javafx.scene.layout.VBox;
import javafx.scene.layout.HBox;
import javafx.scene.Parent;
import javafx.scene.control.Button;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.StackPane;
import javafx.scene.image.ImageView;
import javafx.scene.image.Image;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.scene.control.ComboBox;
import javafx.scene.control.TextField;
import javafx.stage.FileChooser;
import javafx.scene.control.Alert;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.animation.TranslateTransition;
import javafx.animation.Interpolator;
import javafx.util.Duration;
import javafx.scene.layout.Region;
import javafx.scene.control.Hyperlink;
import org.jfree.chart.ChartFactory;
import org.jfree.chart.JFreeChart;
import org.jfree.chart.fx.ChartViewer;
import org.jfree.data.general.DefaultPieDataset;
import org.jfree.chart.plot.PiePlot;
import com.itextpdf.text.Document;
import com.itextpdf.text.Paragraph;
import com.itextpdf.text.pdf.PdfWriter;
import com.itextpdf.text.pdf.PdfPTable;
import com.itextpdf.text.pdf.PdfPCell;
import com.itextpdf.text.Phrase;
import com.itextpdf.text.Element;
import com.itextpdf.text.Font;
import com.itextpdf.text.BaseColor;
import com.itextpdf.text.Rectangle;
import com.itextpdf.text.pdf.draw.LineSeparator;
import com.itextpdf.text.pdf.draw.VerticalPositionMark;
import org.apache.poi.ss.usermodel.*;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;
import org.apache.poi.ss.util.CellRangeAddress;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;
import javafx.application.Platform;

public class ProjectListController {

    private Process eyeTrackerProcess;
    @FXML private Button btnEyeTracking;
    @FXML private ImageView heroImage;

    @FXML private FlowPane projectContainer;
    @FXML private ComboBox<String> levelCombo;
    @FXML private TextField searchField;
    @FXML private ComboBox<String> sortCombo;

    private ProjectService projectService = new ProjectService();
    private List<Project> allProjects = new ArrayList<>();

    @FXML
    public void initialize() {
        setupFilters();
        loadProjects();
    }

    private void setupFilters() {
        levelCombo.getItems().addAll("Tous les niveaux", "Débutant", "Intermédiaire", "Avancé", "Expert");
        levelCombo.setValue("Tous les niveaux");
        
        sortCombo.getItems().addAll("Plus récents", "Plus anciens", "Nom (A-Z)", "Difficulté");
        sortCombo.setValue("Plus récents");

        searchField.textProperty().addListener((obs, oldVal, newVal) -> filterAndSortProjects());
        levelCombo.valueProperty().addListener((obs, oldVal, newVal) -> filterAndSortProjects());
        sortCombo.valueProperty().addListener((obs, oldVal, newVal) -> filterAndSortProjects());

        // 🚀 Animation de flottement pour l image hero
        applyHeroAnimation();
    }

    private void applyHeroAnimation() {
        if (heroImage != null) {
            TranslateTransition tt = new TranslateTransition(Duration.seconds(2), heroImage);
            tt.setByY(-20); // Monte de 20 pixels
            tt.setCycleCount(TranslateTransition.INDEFINITE);
            tt.setAutoReverse(true); // Redescend ensuite
            tt.setInterpolator(Interpolator.EASE_BOTH); // Mouvement fluide
            tt.play();
        }
    }

    private void filterAndSortProjects() {
        String searchText = searchField.getText().toLowerCase();
        String level = levelCombo.getValue();
        String sortBy = sortCombo.getValue();

        List<Project> filtered = allProjects.stream()
            .filter(p -> {
                boolean matchesSearch = p.getTitle().toLowerCase().contains(searchText) || 
                                       p.getDescription().toLowerCase().contains(searchText);
                boolean matchesLevel = level == null || level.equals("Tous les niveaux") || 
                                       normalizeLevel(p.getDifficulty()).equalsIgnoreCase(normalizeLevel(level));
                return matchesSearch && matchesLevel;
            })
            .collect(Collectors.toList());

        if (sortBy != null) {
            switch (sortBy) {
                case "Plus récents":
                    filtered.sort((p1, p2) -> p2.getCreatedAt().compareTo(p1.getCreatedAt()));
                    break;
                case "Plus anciens":
                    filtered.sort(Comparator.comparing(Project::getCreatedAt));
                    break;
                case "Nom (A-Z)":
                    filtered.sort(Comparator.comparing(p -> p.getTitle().toLowerCase()));
                    break;
                case "Difficulté":
                    filtered.sort(Comparator.comparing(Project::getDifficulty));
                    break;
            }
        }

        displayProjects(filtered);
    }

    public void loadProjects() {
        try {
            allProjects = projectService.getAllProjects();
            filterAndSortProjects();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private void displayProjects(List<Project> projects) {
        projectContainer.getChildren().clear();
        for (Project project : projects) {
            projectContainer.getChildren().add(createProjectCard(project));
        }
    }

    private VBox createProjectCard(Project project) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/ProjectCard.fxml"));
            VBox card = loader.load();
            ProjectCardController controller = loader.getController();
            controller.setData(project, this);
            return card;
        } catch (IOException e) {
            e.printStackTrace();
            return new VBox();
        }
    }

    public void handleEditProject(Project project) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/ProjectForm.fxml"));
            VBox form = loader.load();
            
            ProjectFormController controller = loader.getController();
            controller.setProject(project);
            controller.setOnSaveCallback(this::loadProjects);
            
            Stage stage = new Stage();
            stage.setTitle("Edit Project");
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.setScene(new Scene(form));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void handleViewDepots(Project project) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/DepotList.fxml"));
            Parent view = loader.load();
            
            DepotListController controller = loader.getController();
            controller.setProject(project);
            
            StackPane contentPane = (StackPane) projectContainer.getScene().lookup("#contentPane");
            contentPane.getChildren().setAll(view);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void handleMindMap(Project project) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/MindMapView.fxml"));
            Parent view = loader.load();
            
            MindMapController controller = loader.getController();
            controller.setProject(project);
            
            Stage stage = new Stage();
            stage.setTitle("Visualisation Arborescente - " + project.getTitle());
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.setScene(new Scene(view));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleAddProject() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/innolearn/ProjectForm.fxml"));
            VBox form = loader.load();
            
            ProjectFormController controller = loader.getController();
            controller.setOnSaveCallback(this::loadProjects);
            
            Stage stage = new Stage();
            stage.setTitle("Add New Project");
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.setScene(new Scene(form));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private String normalizeLevel(String level) {
        if (level == null) return "";
        String normalized = level.toLowerCase()
            .replace("é", "e")
            .replace("è", "e")
            .replace("à", "a")
            .trim();
            
        // Map common synonyms
        if (normalized.equals("intermediate")) return "intermediaire";
        if (normalized.equals("advanced")) return "avance";
        if (normalized.equals("beginner")) return "debutant";
        
        return normalized;
    }
    @FXML
    private void handleExportPDF() {
        List<Project> currentProjects = allProjects.stream()
            .filter(p -> {
                String searchText = searchField.getText().toLowerCase();
                String level = levelCombo.getValue();
                boolean matchesSearch = p.getTitle().toLowerCase().contains(searchText) || 
                                       p.getDescription().toLowerCase().contains(searchText);
                boolean matchesLevel = level == null || level.equals("Tous les niveaux") || 
                                       normalizeLevel(p.getDifficulty()).equalsIgnoreCase(normalizeLevel(level));
                return matchesSearch && matchesLevel;
            })
            .collect(Collectors.toList());

        if (currentProjects.isEmpty()) {
            showAlert(Alert.AlertType.WARNING, "Export PDF", "Aucun projet à exporter.");
            return;
        }

        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Sauvegarder le PDF Premium");
        fileChooser.getExtensionFilters().add(new FileChooser.ExtensionFilter("Fichiers PDF", "*.pdf"));
        fileChooser.setInitialFileName("InnoLearn_Report_" + java.time.LocalDate.now() + ".pdf");
        
        File file = fileChooser.showSaveDialog(projectContainer.getScene().getWindow());
        if (file != null) {
            try {
                Document document = new Document(com.itextpdf.text.PageSize.A4);
                PdfWriter.getInstance(document, new FileOutputStream(file));
                document.open();
                
                // 🎨 Premium Colors
                BaseColor primary = new BaseColor(79, 70, 229); // Indigo 600
                BaseColor accent = new BaseColor(147, 51, 234); // Purple 600
                BaseColor dark = new BaseColor(30, 41, 59);    // Slate 800
                BaseColor light = new BaseColor(248, 250, 252); // Slate 50
                
                // ✒️ Fonts
                Font titleFont = new Font(Font.FontFamily.HELVETICA, 26, Font.BOLD, primary);
                Font sectionFont = new Font(Font.FontFamily.HELVETICA, 16, Font.BOLD, dark);
                Font normalFont = new Font(Font.FontFamily.HELVETICA, 11, Font.NORMAL, dark);
                Font whiteBold = new Font(Font.FontFamily.HELVETICA, 12, Font.BOLD, BaseColor.WHITE);
                
                // 🏷️ Header Design
                PdfPTable headerTable = new PdfPTable(2);
                headerTable.setWidthPercentage(100);
                headerTable.setWidths(new float[]{2, 1});
                
                PdfPCell logoCell = new PdfPCell(new Phrase("InnoLearn", titleFont));
                logoCell.setBorder(Rectangle.NO_BORDER);
                logoCell.setVerticalAlignment(Element.ALIGN_MIDDLE);
                headerTable.addCell(logoCell);
                
                PdfPCell infoCell = new PdfPCell(new Paragraph("RAPPORT ANALYTIQUE\n" + java.time.format.DateTimeFormatter.ofPattern("dd/MM/yyyy").format(java.time.LocalDateTime.now()), 
                    new Font(Font.FontFamily.HELVETICA, 10, Font.NORMAL, BaseColor.GRAY)));
                infoCell.setBorder(Rectangle.NO_BORDER);
                infoCell.setHorizontalAlignment(Element.ALIGN_RIGHT);
                headerTable.addCell(infoCell);
                
                document.add(headerTable);
                
                LineSeparator line = new LineSeparator(2f, 100f, primary, Element.ALIGN_CENTER, -2f);
                document.add(new Paragraph(" "));
                document.add(line);
                document.add(new Paragraph(" "));

                // 📊 Executive Summary
                document.add(new Paragraph("Résumé de la Session", sectionFont));
                document.add(new Paragraph(" "));
                
                PdfPTable statsTable = new PdfPTable(4);
                statsTable.setWidthPercentage(100);
                statsTable.setSpacingBefore(10f);
                
                addStatCell(statsTable, "Total Projets", String.valueOf(currentProjects.size()), primary);
                long active = currentProjects.stream().filter(p -> !"Terminé".equals(p.getStatus())).count();
                addStatCell(statsTable, "En Cours", String.valueOf(active), accent);
                long experts = currentProjects.stream().filter(p -> "Expert".equalsIgnoreCase(p.getDifficulty())).count();
                addStatCell(statsTable, "Niveau Expert", String.valueOf(experts), new BaseColor(220, 38, 38));
                addStatCell(statsTable, "Statut", "Généré", new BaseColor(22, 163, 74));
                
                document.add(statsTable);
                document.add(new Paragraph(" "));
                document.add(new Paragraph(" "));

                // 📋 Detailed Projects List
                document.add(new Paragraph("Détail des Projets Actifs", sectionFont));
                document.add(new Paragraph(" "));

                PdfPTable table = new PdfPTable(4);
                table.setWidthPercentage(100);
                table.setWidths(new float[]{3, 2, 2, 4});
                
                String[] headers = {"Titre du Projet", "Difficulté", "Statut", "Description & Objectifs"};
                for (String h : headers) {
                    PdfPCell cell = new PdfPCell(new Phrase(h, whiteBold));
                    cell.setBackgroundColor(primary);
                    cell.setPadding(10);
                    cell.setHorizontalAlignment(Element.ALIGN_CENTER);
                    cell.setBorderColor(BaseColor.WHITE);
                    table.addCell(cell);
                }

                boolean alt = false;
                for (Project p : currentProjects) {
                    BaseColor bg = alt ? light : BaseColor.WHITE;
                    
                    addTableCell(table, p.getTitle(), normalFont, bg, Element.ALIGN_LEFT);
                    
                    Font diffFont = new Font(Font.FontFamily.HELVETICA, 10, Font.BOLD);
                    String d = p.getDifficulty() != null ? p.getDifficulty() : "Standard";
                    if (d.contains("Expert")) diffFont.setColor(new BaseColor(220, 38, 38));
                    else if (d.contains("Débutant")) diffFont.setColor(new BaseColor(22, 163, 74));
                    else diffFont.setColor(new BaseColor(202, 138, 4));
                    addTableCell(table, d, diffFont, bg, Element.ALIGN_CENTER);
                    
                    addTableCell(table, p.getStatus() != null ? p.getStatus() : "Actif", normalFont, bg, Element.ALIGN_CENTER);
                    addTableCell(table, p.getDescription() != null ? p.getDescription() : "-", new Font(Font.FontFamily.HELVETICA, 9, Font.NORMAL, BaseColor.GRAY), bg, Element.ALIGN_LEFT);
                    
                    alt = !alt;
                }
                
                document.add(table);
                
                // 🏁 Footer
                document.add(new Paragraph(" "));
                document.add(new LineSeparator(1f, 100f, BaseColor.LIGHT_GRAY, Element.ALIGN_CENTER, -2f));
                Paragraph footer = new Paragraph("\nCe document est généré automatiquement par le portail InnoLearn.\n© 2026 InnoLearn Inc. - Tous droits réservés.", 
                    new Font(Font.FontFamily.HELVETICA, 9, Font.ITALIC, BaseColor.GRAY));
                footer.setAlignment(Element.ALIGN_CENTER);
                document.add(footer);
                
                document.close();
                showAlert(Alert.AlertType.INFORMATION, "Succès", "Rapport PDF Premium généré !");
                
            } catch (Exception e) {
                e.printStackTrace();
                showAlert(Alert.AlertType.ERROR, "Erreur", "Échec de la génération du PDF.");
            }
        }
    }

    private void addStatCell(PdfPTable table, String label, String value, BaseColor color) {
        PdfPCell cell = new PdfPCell();
        cell.setBackgroundColor(new BaseColor(241, 245, 249));
        cell.setPadding(15);
        cell.setBorderColor(color);
        cell.setBorderWidthTop(4f);
        
        Paragraph pLabel = new Paragraph(label.toUpperCase(), new Font(Font.FontFamily.HELVETICA, 8, Font.BOLD, BaseColor.GRAY));
        pLabel.setAlignment(Element.ALIGN_CENTER);
        cell.addElement(pLabel);
        
        Paragraph pValue = new Paragraph(value, new Font(Font.FontFamily.HELVETICA, 18, Font.BOLD, color));
        pValue.setAlignment(Element.ALIGN_CENTER);
        cell.addElement(pValue);
        
        table.addCell(cell);
    }

    private void addTableCell(PdfPTable table, String text, Font font, BaseColor bg, int align) {
        PdfPCell cell = new PdfPCell(new Phrase(text, font));
        cell.setBackgroundColor(bg);
        cell.setPadding(10);
        cell.setHorizontalAlignment(align);
        cell.setVerticalAlignment(Element.ALIGN_MIDDLE);
        cell.setBorderColor(new BaseColor(226, 232, 240));
        table.addCell(cell);
    }

    @FXML
    private void handleExportExcel() {
        List<Project> currentProjects = allProjects.stream()
            .filter(p -> {
                String searchText = searchField.getText().toLowerCase();
                String level = levelCombo.getValue();
                boolean matchesSearch = p.getTitle().toLowerCase().contains(searchText) || 
                                       p.getDescription().toLowerCase().contains(searchText);
                boolean matchesLevel = level == null || level.equals("Tous les niveaux") || 
                                       normalizeLevel(p.getDifficulty()).equalsIgnoreCase(normalizeLevel(level));
                return matchesSearch && matchesLevel;
            })
            .collect(Collectors.toList());

        if (currentProjects.isEmpty()) {
            showAlert(Alert.AlertType.WARNING, "Export Excel", "Aucun projet à exporter.");
            return;
        }

        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Sauvegarder l'Excel");
        fileChooser.getExtensionFilters().add(new FileChooser.ExtensionFilter("Fichiers Excel", "*.xlsx"));
        fileChooser.setInitialFileName("InnoLearn_Projects_" + java.time.LocalDate.now() + ".xlsx");
        
        File file = fileChooser.showSaveDialog(projectContainer.getScene().getWindow());
        if (file != null) {
            try (Workbook workbook = new XSSFWorkbook()) {
                Sheet sheet = workbook.createSheet("Liste des Projets");
                
                // 🎨 Excel Styles
                org.apache.poi.ss.usermodel.Font headerFont = workbook.createFont();
                headerFont.setBold(true);
                headerFont.setColor(IndexedColors.WHITE.getIndex());
                headerFont.setFontHeightInPoints((short) 12);

                CellStyle headerStyle = workbook.createCellStyle();
                headerStyle.setFillForegroundColor(IndexedColors.INDIGO.getIndex());
                headerStyle.setFillPattern(FillPatternType.SOLID_FOREGROUND);
                headerStyle.setFont(headerFont);
                headerStyle.setAlignment(HorizontalAlignment.CENTER);
                headerStyle.setBorderBottom(BorderStyle.MEDIUM);

                CellStyle dateStyle = workbook.createCellStyle();
                dateStyle.setDataFormat(workbook.getCreationHelper().createDataFormat().getFormat("dd/MM/yyyy"));

                // 🏷️ Header Row
                Row headerRow = sheet.createRow(0);
                String[] columns = {"ID", "Titre", "Description", "Statut", "Difficulté", "Date Création"};
                for (int i = 0; i < columns.length; i++) {
                    Cell cell = headerRow.createCell(i);
                    cell.setCellValue(columns[i]);
                    cell.setCellStyle(headerStyle);
                }

                // 📝 Data Rows
                int rowIdx = 1;
                for (Project p : currentProjects) {
                    Row row = sheet.createRow(rowIdx++);
                    row.createCell(0).setCellValue(p.getId());
                    row.createCell(1).setCellValue(p.getTitle());
                    row.createCell(2).setCellValue(p.getDescription());
                    row.createCell(3).setCellValue(p.getStatus());
                    row.createCell(4).setCellValue(p.getDifficulty());
                    
                    Cell dateCell = row.createCell(5);
                    if (p.getCreatedAt() != null) {
                        dateCell.setCellValue(p.getCreatedAt());
                        dateCell.setCellStyle(dateStyle);
                    }
                }

                // 📏 Auto-size columns
                for (int i = 0; i < columns.length; i++) {
                    sheet.autoSizeColumn(i);
                }

                try (FileOutputStream fileOut = new FileOutputStream(file)) {
                    workbook.write(fileOut);
                }
                
                showAlert(Alert.AlertType.INFORMATION, "Succès", "Le fichier Excel a été généré avec succès !");
                
            } catch (Exception e) {
                e.printStackTrace();
                showAlert(Alert.AlertType.ERROR, "Erreur", "Une erreur est survenue lors de la création du fichier Excel.");
            }
        }
    }

    @FXML
    private void handleShowStats() {
        List<Project> currentProjects = allProjects.stream()
            .filter(p -> {
                String searchText = searchField.getText().toLowerCase();
                String level = levelCombo.getValue();
                boolean matchesSearch = p.getTitle().toLowerCase().contains(searchText) || 
                                       p.getDescription().toLowerCase().contains(searchText);
                boolean matchesLevel = level == null || level.equals("Tous les niveaux") || 
                                       normalizeLevel(p.getDifficulty()).equalsIgnoreCase(normalizeLevel(level));
                return matchesSearch && matchesLevel;
            })
            .collect(Collectors.toList());

        if (currentProjects.isEmpty()) {
            showAlert(Alert.AlertType.WARNING, "Statistiques", "Aucun projet pour générer des statistiques.");
            return;
        }

        Map<String, Long> difficultyCounts = currentProjects.stream()
            .collect(Collectors.groupingBy(
                p -> p.getDifficulty() != null && !p.getDifficulty().trim().isEmpty() ? p.getDifficulty() : "Inconnu",
                Collectors.counting()
            ));

        DefaultPieDataset dataset = new DefaultPieDataset();
        for (Map.Entry<String, Long> entry : difficultyCounts.entrySet()) {
            dataset.setValue(entry.getKey() + " (" + entry.getValue() + ")", entry.getValue());
        }

        JFreeChart chart = ChartFactory.createPieChart(
            "Répartition par Niveau de Difficulté",
            dataset,
            true, // legend
            true, // tooltips
            false // URLs
        );

        // Customize JFreeChart to look modern
        chart.setBackgroundPaint(java.awt.Color.WHITE);
        chart.getTitle().setPaint(new java.awt.Color(30, 41, 59)); // Slate 800
        chart.getTitle().setFont(new java.awt.Font("SansSerif", java.awt.Font.BOLD, 18));
        
        PiePlot plot = (PiePlot) chart.getPlot();
        plot.setBackgroundPaint(java.awt.Color.WHITE);
        plot.setOutlineVisible(false);
        plot.setLabelBackgroundPaint(new java.awt.Color(255, 255, 255, 200));
        plot.setLabelOutlinePaint(null);
        plot.setLabelShadowPaint(null);
        plot.setShadowPaint(null);

        // Map colors (AWT Colors)
        plot.setSectionPaint("Débutant", new java.awt.Color(34, 197, 94));
        plot.setSectionPaint("Debutant", new java.awt.Color(34, 197, 94));
        plot.setSectionPaint("Intermédiaire", new java.awt.Color(234, 179, 8));
        plot.setSectionPaint("Intermediaire", new java.awt.Color(234, 179, 8));
        plot.setSectionPaint("Avancé", new java.awt.Color(249, 115, 22));
        plot.setSectionPaint("Avance", new java.awt.Color(249, 115, 22));
        plot.setSectionPaint("Expert", new java.awt.Color(239, 68, 68));

        ChartViewer viewer = new ChartViewer(chart);
        viewer.setPrefSize(600, 400);

        VBox layout = new VBox(viewer);
        layout.setStyle("-fx-padding: 20; -fx-background-color: white;");
        
        Stage stage = new Stage();
        stage.setTitle("Statistiques des Projets (JFreeChart)");
        stage.setScene(new Scene(layout, 650, 450));
        stage.initModality(Modality.APPLICATION_MODAL);
        stage.show();
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }

    @FXML
    private void handleVoiceSearch() {
        try {
            // S'assurer que le champ de recherche a le focus pour que pyautogui tape au bon endroit
            searchField.requestFocus();
            
            String pythonPath = System.getenv("LOCALAPPDATA") + "\\Programs\\Python\\Python311\\python.exe";
            File pyFile = new File(pythonPath);
            if (!pyFile.exists()) {
                pythonPath = "python";
            }

            ProcessBuilder pb = new ProcessBuilder(pythonPath, "voice_search.py");
            pb.directory(new File("."));
            pb.start();
        } catch (IOException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur Recherche Vocale", "Impossible de démarrer le script de reconnaissance vocale.");
        }
    }

    @FXML
    private void handleToggleEyeTracking() {
        if (eyeTrackerProcess != null && eyeTrackerProcess.isAlive()) {
            eyeTrackerProcess.destroy();
            eyeTrackerProcess = null;
            btnEyeTracking.setText("👁️ Suivi Oculaire");
            btnEyeTracking.setStyle("-fx-background-color: #f59e0b; -fx-text-fill: white; -fx-font-weight: bold; -fx-font-size: 14px; -fx-padding: 12 25; -fx-background-radius: 30; -fx-effect: dropshadow(gaussian, rgba(245,158,11,0.4), 15, 0, 0, 5); -fx-cursor: hand;");
        } else {
            try {
                // Use absolute path for safety if PATH is not updated
                String pythonPath = System.getenv("LOCALAPPDATA") + "\\Programs\\Python\\Python311\\python.exe";
                File pyFile = new File(pythonPath);
                if (!pyFile.exists()) {
                    pythonPath = "python"; // Fallback to system path
                }
                
                ProcessBuilder pb = new ProcessBuilder(pythonPath, "eye_tracker.py");
                pb.directory(new File(".")); // root of the project
                eyeTrackerProcess = pb.start();
                
                btnEyeTracking.setText("🛑 Arrêter le Suivi");

                btnEyeTracking.setStyle("-fx-background-color: #ef4444; -fx-text-fill: white; -fx-font-weight: bold; -fx-font-size: 14px; -fx-padding: 12 25; -fx-background-radius: 30; -fx-effect: dropshadow(gaussian, rgba(239,68,68,0.4), 15, 0, 0, 5); -fx-cursor: hand;");
            } catch (IOException e) {
                e.printStackTrace();
                showAlert(Alert.AlertType.ERROR, "Erreur Eye Tracking", "Impossible de démarrer le script Python. Assurez-vous que Python est installé et que opencv-python, mediapipe et pyautogui sont installés.");
            }
        }
    }
}

