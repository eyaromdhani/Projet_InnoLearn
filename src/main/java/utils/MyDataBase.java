package utils;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class MyDataBase {
    private final String URL = "jdbc:mysql://localhost:3306/innolearn_db";
    private final String USERNAME = "root";
    private final String PASSWORD = "";

    private Connection connection;
    private static MyDataBase instance;

    private MyDataBase() {
        try {
            connection = DriverManager.getConnection(URL, USERNAME, PASSWORD);
            System.out.println("Connection established to innolearn_db");
        } catch (SQLException e) {
            System.err.println("❌ Erreur JDBC : " + e.getMessage());
        }
    }

    public static MyDataBase getInstance() {
        if (instance == null) {
            instance = new MyDataBase();
        }
        return instance;
    }

    public Connection getConnection() {
        return connection;
    }
}
