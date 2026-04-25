import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class CheckDB {
    public static void main(String[] args) {
        String URL = "jdbc:mysql://localhost:3306/innolearn_db";
        String USERNAME = "root";
        String PASSWORD = "";

        try (Connection connection = DriverManager.getConnection(URL, USERNAME, PASSWORD)) {
            System.out.println("--- TABLES IN innolearn_db ---");
            DatabaseMetaData metaData = connection.getMetaData();
            ResultSet tables = metaData.getTables("innolearn_db", null, "%", new String[]{"TABLE"});
            while (tables.next()) {
                String tableName = tables.getString("TABLE_NAME");
                System.out.println("Table: " + tableName);
                
                // If it looks like book or livre, show columns
                if (tableName.equalsIgnoreCase("book") || tableName.equalsIgnoreCase("livre") || tableName.contains("book")) {
                    System.out.println("  Columns for " + tableName + ":");
                    ResultSet columns = metaData.getColumns(null, null, tableName, null);
                    while (columns.next()) {
                        System.out.println("    - " + columns.getString("COLUMN_NAME") + " (" + columns.getString("TYPE_NAME") + ")");
                    }
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
