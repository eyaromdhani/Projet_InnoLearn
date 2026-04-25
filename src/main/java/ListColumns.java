import java.sql.*;

public class ListColumns {
    public static void main(String[] args) {
        String URL = "jdbc:mysql://localhost:3306/innolearn_db";
        String USERNAME = "root";
        String PASSWORD = "";

        try (Connection connection = DriverManager.getConnection(URL, USERNAME, PASSWORD);
             Statement st = connection.createStatement();
             ResultSet rs = st.executeQuery("SELECT * FROM book LIMIT 1")) {
            
            ResultSetMetaData md = rs.getMetaData();
            System.out.println("--- COLUMNS FOR TABLE 'book' ---");
            for (int i = 1; i <= md.getColumnCount(); i++) {
                System.out.println(md.getColumnName(i));
            }
        } catch (SQLException e) {
            System.err.println("Error: " + e.getMessage());
            // Try livre
            try (Connection connection = DriverManager.getConnection(URL, USERNAME, PASSWORD);
                 Statement st = connection.createStatement();
                 ResultSet rs = st.executeQuery("SELECT * FROM livre LIMIT 1")) {
                
                ResultSetMetaData md = rs.getMetaData();
                System.out.println("--- COLUMNS FOR TABLE 'livre' ---");
                for (int i = 1; i <= md.getColumnCount(); i++) {
                    System.out.println(md.getColumnName(i));
                }
            } catch (SQLException e2) {
                System.err.println("Error fallback livre: " + e2.getMessage());
            }
        }
    }
}
