package com.innolearn.util;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DatabaseConnection {
    private static DatabaseConnection instance;
    private Connection connection;
    private final String url = "jdbc:mysql://localhost:3306/innolearn?useUnicode=true&characterEncoding=UTF-8";
    private final String user = "root";
    private final String password = "";

    private DatabaseConnection() throws SQLException {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            java.util.Properties props = new java.util.Properties();
            props.setProperty("user", user);
            props.setProperty("password", password);
            props.setProperty("useUnicode", "true");
            props.setProperty("characterEncoding", "UTF-8");
            props.setProperty("characterSetResults", "UTF-8");
            props.setProperty("serverTimezone", "UTC");
            this.connection = DriverManager.getConnection(url, props);
        } catch (ClassNotFoundException e) {
            throw new SQLException("MySQL JDBC Driver not found.", e);
        }
    }

    public static Connection getConnection() throws SQLException {
        if (instance == null) {
            instance = new DatabaseConnection();
        } else if (instance.connection.isClosed()) {
            instance = new DatabaseConnection();
        }
        return instance.connection;
    }
}
