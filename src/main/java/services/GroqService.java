package services;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import models.Question;
import utils.Config;

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.CompletableFuture;

public class GroqService {

    private static final String API_URL = "https://api.groq.com/openai/v1/chat/completions";
    private static final String MODEL = "llama-3.3-70b-versatile";
    private final HttpClient client;
    private final Gson gson;

    public GroqService() {
        this.client = HttpClient.newHttpClient();
        this.gson = new Gson();
    }

    public CompletableFuture<List<Question>> generateQuestions(String context, int quizId) {
        if (Config.GROQ_API_KEY == null || Config.GROQ_API_KEY.equals("YOUR_API_KEY_HERE")) {
            return CompletableFuture.failedFuture(new Exception("API Key not configured in Config.java"));
        }

        String prompt = "Génère 3 à 5 questions de quiz basées sur ce texte : " + context + ". " +
                "Réponds UNIQUEMENT en JSON sous forme de tableau d'objets avec les clés: question_text, type (toujours 'Texte'), correct_answer, points (un nombre entier). " +
                "Ne mets pas de texte avant ou après le JSON.";

        JsonObject systemMessage = new JsonObject();
        systemMessage.addProperty("role", "system");
        systemMessage.addProperty("content", "Tu es un assistant pédagogique expert. Réponds UNIQUEMENT par un tableau JSON valide.");

        JsonObject userMessage = new JsonObject();
        userMessage.addProperty("role", "user");
        userMessage.addProperty("content", prompt);

        JsonArray messages = new JsonArray();
        messages.add(systemMessage);
        messages.add(userMessage);

        JsonObject body = new JsonObject();
        body.addProperty("model", MODEL);
        body.add("messages", messages);
        body.addProperty("temperature", 0.7);

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(API_URL))
                .header("Authorization", "Bearer " + Config.GROQ_API_KEY)
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(gson.toJson(body)))
                .build();

        return client.sendAsync(request, HttpResponse.BodyHandlers.ofString())
                .thenApply(response -> {
                    if (response.statusCode() != 200) {
                        throw new RuntimeException("API Error: " + response.body());
                    }

                    JsonObject jsonResponse = gson.fromJson(response.body(), JsonObject.class);
                    String content = jsonResponse.getAsJsonArray("choices")
                            .get(0).getAsJsonObject()
                            .getAsJsonObject("message")
                            .get("content").getAsString();

                    // Clean the content in case the AI added markdown blocks
                    content = content.replaceAll("```json", "").replaceAll("```", "").trim();

                    JsonArray questionsArray = gson.fromJson(content, JsonArray.class);
                    List<Question> result = new ArrayList<>();

                    for (JsonElement el : questionsArray) {
                        JsonObject obj = el.getAsJsonObject();
                        Question q = new Question();
                        q.setQuestionText(obj.get("question_text").getAsString());
                        q.setType(obj.get("type").getAsString());
                        q.setCorrectAnswer(obj.get("correct_answer").getAsString());
                        q.setPoints(obj.get("points").getAsInt());
                        q.setFormulaireId(quizId);
                        result.add(q);
                    }

                    return result;
                });
    }

    public CompletableFuture<String> getPedagogicalAnalysis(String quizTitle, int passCount, int failCount, int questionCount) {
        if (Config.GROQ_API_KEY == null || Config.GROQ_API_KEY.equals("YOUR_API_KEY_HERE")) {
            return CompletableFuture.failedFuture(new Exception("API Key not configured in Config.java"));
        }

        String prompt = String.format(
                "Analyse les résultats du quiz '%s': Pass: %d, Fail: %d, Questions: %d. Donne un conseil pédagogique court.",
                quizTitle, passCount, failCount, questionCount
        );

        JsonObject userMessage = new JsonObject();
        userMessage.addProperty("role", "user");
        userMessage.addProperty("content", prompt);

        JsonArray messages = new JsonArray();
        messages.add(userMessage);

        JsonObject body = new JsonObject();
        body.addProperty("model", MODEL);
        body.add("messages", messages);
        body.addProperty("temperature", 0.7);

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(API_URL))
                .header("Authorization", "Bearer " + Config.GROQ_API_KEY)
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(gson.toJson(body)))
                .build();

        return client.sendAsync(request, HttpResponse.BodyHandlers.ofString())
                .thenApply(response -> {
                    if (response.statusCode() != 200) {
                        throw new RuntimeException("API Error: " + response.body());
                    }

                    JsonObject jsonResponse = gson.fromJson(response.body(), JsonObject.class);
                    return jsonResponse.getAsJsonArray("choices")
                            .get(0).getAsJsonObject()
                            .getAsJsonObject("message")
                            .get("content").getAsString();
                });
    }

    public CompletableFuture<String> getChatCompletion(String prompt) {
        if (Config.GROQ_API_KEY == null || Config.GROQ_API_KEY.equals("YOUR_API_KEY_HERE")) {
            return CompletableFuture.failedFuture(new Exception("API Key not configured in Config.java"));
        }

        JsonObject userMessage = new JsonObject();
        userMessage.addProperty("role", "user");
        userMessage.addProperty("content", prompt);

        JsonArray messages = new JsonArray();
        messages.add(userMessage);

        JsonObject body = new JsonObject();
        body.addProperty("model", MODEL);
        body.add("messages", messages);
        body.addProperty("temperature", 0.7);

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(API_URL))
                .header("Authorization", "Bearer " + Config.GROQ_API_KEY)
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(gson.toJson(body)))
                .build();

        return client.sendAsync(request, HttpResponse.BodyHandlers.ofString())
                .thenApply(response -> {
                    if (response.statusCode() != 200) {
                        throw new RuntimeException("API Error: " + response.body());
                    }

                    JsonObject jsonResponse = gson.fromJson(response.body(), JsonObject.class);
                    return jsonResponse.getAsJsonArray("choices")
                            .get(0).getAsJsonObject()
                            .getAsJsonObject("message")
                            .get("content").getAsString();
                });
    }
}
