package com.innolearn.service;

import org.json.JSONArray;
import org.json.JSONObject;

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;
import java.util.ArrayList;
import java.util.List;

/**
 * Service for interacting with the Cohere Chat API v1 (command-r-plus model).
 * Uses Java's built-in HttpClient + org.json — no extra dependencies needed.
 */
public class CohereService {

    private static final String API_KEY = "XTUIY4MTrR1iACEI2YTFOnVzBQkziy9t9ge5EanG";

    // v1 endpoint — compatible with all trial keys
    private static final String API_URL = "https://api.cohere.ai/v1/chat";
    private static final String MODEL   = "command-a-03-2025";

    // Gemini Config
    private static final String GEMINI_API_KEY = "AIzaSyChy5CjkpeTSdGTvHjX77RS0qDB1h3paxI";
    private static final String GEMINI_MODEL   = "gemini-2.0-flash";

    private static final String PREAMBLE =
            "You are InnoBot, a helpful AI assistant embedded in InnoLearn, " +
            "an innovative e-learning platform for students. " +
            "You help students with questions about their projects, courses, " +
            "coding, learning paths, and general knowledge. " +
            "Be concise, friendly, and encouraging. " +
            "Answer in the same language the user writes in (French or English).";

    private final HttpClient httpClient;

    // v1 chat history: list of {role: "USER"|"CHATBOT", message: "..."}
    private final List<JSONObject> chatHistory = new ArrayList<>();

    public CohereService() {
        this.httpClient = HttpClient.newBuilder()
                .connectTimeout(Duration.ofSeconds(15))
                .build();
    }

    /**
     * Sends a user message to Cohere v1 and returns the assistant's reply.
     * Maintains full conversation history for multi-turn chat.
     *
     * @param userMessage the text typed by the user
     * @return the assistant's reply text, or an error message
     */
    public String sendMessage(String userMessage) {
        try {
            // Build v1 request body
            JSONObject body = new JSONObject();
            body.put("model", MODEL);
            body.put("message", userMessage);          // current user message
            body.put("preamble", PREAMBLE);            // system/context prompt
            body.put("chat_history", buildHistoryArray()); // previous turns

            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(API_URL))
                    .header("Content-Type", "application/json")
                    .header("Authorization", "Bearer " + API_KEY)
                    .header("Accept", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString(body.toString()))
                    .timeout(Duration.ofSeconds(30))
                    .build();

            HttpResponse<String> response = httpClient.send(request,
                    HttpResponse.BodyHandlers.ofString());

            System.out.println("Cohere status: " + response.statusCode());

            if (response.statusCode() == 200) {
                JSONObject json = new JSONObject(response.body());
                // v1 response: { "text": "reply..." }
                String reply = json.getString("text").trim();

                // Save this turn to history for next call
                JSONObject userTurn = new JSONObject();
                userTurn.put("role", "USER");
                userTurn.put("message", userMessage);
                chatHistory.add(userTurn);

                JSONObject botTurn = new JSONObject();
                botTurn.put("role", "CHATBOT");
                botTurn.put("message", reply);
                chatHistory.add(botTurn);

                return reply;
            } else {
                System.err.println("Cohere error " + response.statusCode() + ": " + response.body());
                return "⚠️ Erreur (" + response.statusCode() + "). Détails: " +
                        tryExtractError(response.body());
            }

        } catch (java.net.http.HttpTimeoutException e) {
            return "⏱️ La requête a expiré. Veuillez réessayer.";
        } catch (Exception e) {
            e.printStackTrace();
            return "❌ Erreur: " + e.getMessage();
        }
    }

    /** Clears the conversation history (start a new session). */
    public void clearHistory() {
        chatHistory.clear();
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private JSONArray buildHistoryArray() {
        JSONArray arr = new JSONArray();
        for (JSONObject turn : chatHistory) {
            arr.put(turn);
        }
        return arr;
    }

    private String tryExtractError(String body) {
        try {
            JSONObject json = new JSONObject(body);
            return json.optString("message", body);
        } catch (Exception e) {
            return body.length() > 120 ? body.substring(0, 120) + "..." : body;
        }
    }

    /**
     * Classifies a project's difficulty based on its title and description.
     * Returns one of: Débutant, Intermédiaire, Avancé, Expert
     *
     * @param title       The project title
     * @param description The project description
     * @return The classified difficulty level
     */
    public String classifyProjectDifficulty(String title, String description) {
        try {
            String geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/" + GEMINI_MODEL + ":generateContent?key=" + GEMINI_API_KEY;

            String prompt = "Tu es un expert en éducation. Analyse le titre et la description du projet suivant, puis détermine son niveau de difficulté.\n\n" +
                            "Titre : " + title + "\n" +
                            "Description : " + description + "\n\n" +
                            "Tu DOIS répondre UNIQUEMENT par l'un de ces 3 mots exacts : Débutant, Intermédiaire, Expert.";

            JSONObject body = new JSONObject();
            JSONArray contents = new JSONArray();
            JSONObject content = new JSONObject();
            JSONArray parts = new JSONArray();
            JSONObject part = new JSONObject();
            part.put("text", prompt);
            parts.put(part);
            content.put("parts", parts);
            contents.put(content);
            body.put("contents", contents);

            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(geminiUrl))
                    .header("Content-Type", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString(body.toString()))
                    .timeout(Duration.ofSeconds(15))
                    .build();

            HttpResponse<String> response = httpClient.send(request, HttpResponse.BodyHandlers.ofString());

            if (response.statusCode() == 200) {
                JSONObject json = new JSONObject(response.body());
                String reply = json.getJSONArray("candidates")
                        .getJSONObject(0)
                        .getJSONObject("content")
                        .getJSONArray("parts")
                        .getJSONObject(0)
                        .getString("text")
                        .trim();
                
                // Final cleaning to match the 3 categories
                if (reply.toLowerCase().contains("expert")) return "Expert";
                if (reply.toLowerCase().contains("interm")) return "Intermédiaire";
                return "Débutant";
            }
            return "Intermédiaire";
        } catch (Exception e) {
            e.printStackTrace();
            return "Intermédiaire";
        }
    }

    /**
     * Uses the Gemini API to analyze the completion level of a depot in the context of its project.
     * The AI reads the project info + depot todo status and returns a score + feedback.
     *
     * @param projectTitle       The project title
     * @param projectDescription The project description
     * @param projectDifficulty  The project difficulty level
     * @param projectStatus      The project status
     * @param depotTitle         The depot title
     * @param depotType          The depot type (PDF, Code, etc.)
     * @param depotStudent       The student name
     * @param checklist          The list of todo items and their status
     * @return A formatted result string with score and AI feedback
     */
    public String analyzeDepotCompletion(
            String projectTitle, String projectDescription,
            String projectDifficulty, String projectStatus,
            String depotTitle, String depotType,
            String depotStudent, List<String> checklist) {
        try {
            String geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/" + GEMINI_MODEL + ":generateContent?key=" + GEMINI_API_KEY;

            StringBuilder checklistStr = new StringBuilder();
            if (checklist != null && !checklist.isEmpty()) {
                for (String task : checklist) {
                    checklistStr.append("- ").append(task).append("\n");
                }
            } else {
                checklistStr.append("Aucune tâche spécifiée.");
            }

            String prompt =
                "Tu es un évaluateur expert en projets étudiants. Analyse le niveau de complétion d'un dépôt de projet basé sur les informations suivantes.\n\n" +
                "=== PROJET ===\n" +
                "Titre : " + projectTitle + "\n" +
                "Description : " + projectDescription + "\n" +
                "Difficulté : " + (projectDifficulty != null ? projectDifficulty : "Non spécifiée") + "\n" +
                "Statut du projet : " + (projectStatus != null ? projectStatus : "Non spécifié") + "\n\n" +
                "=== DÉPÔT ===\n" +
                "Titre du dépôt : " + depotTitle + "\n" +
                "Type de fichier : " + depotType + "\n" +
                "Étudiant : " + depotStudent + "\n\n" +
                "=== LISTE DES TÂCHES (TODO) ===\n" +
                checklistStr.toString() + "\n\n" +
                "=== INSTRUCTIONS CRITIQUES ===\n" +
                "1. Si la liste des tâches est vide ou contient 'Aucune tâche spécifiée', le SCORE DOIT ÊTRE 0.\n" +
                "2. Calcule le score basé sur le ratio de tâches [DONE] par rapport au total.\n" +
                "3. Réponds UNIQUEMENT dans ce format exact :\n" +
                "SCORE: [nombre]\n" +
                "NIVEAU: [Excellent | Bien | Moyen | Insuffisant]\n" +
                "FEEDBACK: [Analyse professionnelle]";


            JSONObject body = new JSONObject();
            JSONArray contents = new JSONArray();
            JSONObject content = new JSONObject();
            JSONArray parts = new JSONArray();
            JSONObject part = new JSONObject();
            part.put("text", prompt);
            parts.put(part);
            content.put("parts", parts);
            contents.put(content);
            body.put("contents", contents);

            HttpClient client = HttpClient.newBuilder()
                    .connectTimeout(Duration.ofSeconds(15))
                    .build();
            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(geminiUrl))
                    .header("Content-Type", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString(body.toString()))
                    .timeout(Duration.ofSeconds(30))
                    .build();

            HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());

            if (response.statusCode() == 200) {
                JSONObject json = new JSONObject(response.body());
                String reply = json.getJSONArray("candidates")
                        .getJSONObject(0)
                        .getJSONObject("content")
                        .getJSONArray("parts")
                        .getJSONObject(0)
                        .getString("text")
                        .trim();
                return reply;
            } else {
                System.err.println("Gemini analyze error: " + response.statusCode() + " - " + response.body());
                return buildFallbackAnalysis("Doing");
            }
        } catch (Exception e) {
            e.printStackTrace();
            return buildFallbackAnalysis("Doing");
        }
    }

    private String buildFallbackAnalysis(String todoStatus) {
        if ("Done".equals(todoStatus)) {
            return "SCORE: 100\nNIVEAU: Excellent\nFEEDBACK: Le dépôt est marqué comme terminé.";
        } else {
            return "SCORE: 50\nNIVEAU: MOYEN\nFEEDBACK:  progression détectée ou liste de tâches .";
        }
    }

}
