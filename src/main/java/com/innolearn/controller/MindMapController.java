package com.innolearn.controller;
 
import com.innolearn.model.Depot;
import com.innolearn.model.Project;
import com.innolearn.service.DepotService;
import javafx.animation.ScaleTransition;
import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.layout.Pane;
import javafx.scene.layout.StackPane;
import javafx.scene.paint.Color;
import javafx.scene.paint.CycleMethod;
import javafx.scene.paint.LinearGradient;
import javafx.scene.paint.Stop;
import javafx.scene.shape.Circle;
import javafx.scene.shape.Line;
import javafx.stage.Stage;
import javafx.util.Duration;
 
import java.sql.SQLException;
import java.util.List;
 
public class MindMapController {
 
    @FXML private Pane drawingPane;
    @FXML private Label lblStatus;
 
    private DepotService depotService = new DepotService();
 
    public void setProject(Project project) {
        try {
            List<Depot> depots = depotService.getDepotsByProject(project.getId());
            drawMindMap(project, depots);
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
 
    private void drawMindMap(Project project, List<Depot> depots) {
        drawingPane.getChildren().clear();
 
        double centerX = 400;
        double centerY = 300;
        double radius = 180;
 
        // 1. Create Central Node (Project)
        StackPane projectNode = createNode(project.getTitle(), 60, true);
        projectNode.setLayoutX(centerX - 60);
        projectNode.setLayoutY(centerY - 60);
        
        drawingPane.getChildren().add(projectNode);
 
        // 2. Create Satellite Nodes (Depots)
        int totalDepots = depots.size();
        for (int i = 0; i < totalDepots; i++) {
            double angle = 2 * Math.PI * i / totalDepots;
            double targetX = centerX + radius * Math.cos(angle);
            double targetY = centerY + radius * Math.sin(angle);
 
            // Draw Line (Branch)
            Line line = new Line(centerX, centerY, targetX, targetY);
            line.setStroke(Color.web("#e2e8f0"));
            line.setStrokeWidth(2);
            drawingPane.getChildren().add(line);
 
            // Draw Depot Node
            Depot depot = depots.get(i);
            StackPane depotNode = createNode(depot.getTitle(), 45, false);
            depotNode.setLayoutX(targetX - 45);
            depotNode.setLayoutY(targetY - 45);
            
            // Interaction
            depotNode.setOnMouseEntered(e -> animateScale(depotNode, 1.1));
            depotNode.setOnMouseExited(e -> animateScale(depotNode, 1.0));
 
            drawingPane.getChildren().add(depotNode);
        }
    }
 
    private StackPane createNode(String text, double radius, boolean isMain) {
        StackPane stack = new StackPane();
        
        Circle circle = new Circle(radius);
        if (isMain) {
            circle.setFill(new LinearGradient(0, 0, 1, 1, true, CycleMethod.NO_CYCLE,
                    new Stop(0, Color.web("#6366f1")),
                    new Stop(1, Color.web("#a855f7"))));
        } else {
            circle.setFill(Color.WHITE);
            circle.setStroke(Color.web("#6366f1"));
            circle.setStrokeWidth(2);
        }
        
        circle.setEffect(new javafx.scene.effect.DropShadow(10, Color.rgb(0,0,0,0.1)));
 
        Label label = new Label(text);
        label.setStyle("-fx-text-fill: " + (isMain ? "white" : "#475569") + "; -fx-font-weight: bold; -fx-font-size: 12px;");
        label.setWrapText(true);
        label.setMaxWidth(radius * 1.5);
        label.setAlignment(javafx.geometry.Pos.CENTER);
        label.setTextAlignment(javafx.scene.text.TextAlignment.CENTER);
 
        stack.getChildren().addAll(circle, label);
        return stack;
    }
 
    private void animateScale(StackPane node, double scale) {
        ScaleTransition st = new ScaleTransition(Duration.millis(200), node);
        st.setToX(scale);
        st.setToY(scale);
        st.play();
    }
 
    @FXML
    private void handleClose() {
        ((Stage) drawingPane.getScene().getWindow()).close();
    }
}
