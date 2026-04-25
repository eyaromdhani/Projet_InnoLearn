import java.sql.*;

public class TestDB {
    public static void main(String[] args) {
        String URL = "jdbc:mysql://localhost:3306/innolearn_db";
        String USERNAME = "root";
        String PASSWORD = "";

        try (Connection connection = DriverManager.getConnection(URL, USERNAME, PASSWORD)) {
            System.out.println("✅ Connection Success!");
            DatabaseMetaData metaData = connection.getMetaData();
            
            System.out.println("\nTables found:");
            ResultSet tables = metaData.getTables("innolearn_db", null, "%", new String[]{"TABLE"});
            while (tables.next()) {
                System.out.println(" - " + tables.getString("TABLE_NAME"));
            }

            System.out.println("\nChecking 'book' table:");
            try (Statement st = connection.createStatement();
                 ResultSet rs = st.executeQuery("SELECT * FROM book")) {
                int count = 0;
                while (rs.next()) {
                    count++;
                    System.out.println("Book Found: " + rs.getString("titre"));
                }
                System.out.println("Total books in DB: " + count);
            } catch (SQLException e) {
                System.out.println("❌ Error checking 'book' table: " + e.getMessage());
                
                System.out.println("\nChecking 'livre' table (fallback):");
                try (Statement st2 = connection.createStatement();
                     ResultSet rs2 = st2.executeQuery("SELECT * FROM livre")) {
                    int count = 0;
                    while (rs2.next()) {
                        count++;
                        System.out.println("Livre Found: " + rs2.getString("titre"));
                    }
                    System.out.println("Total livre in DB: " + count);
                } catch (SQLException e2) {
                    System.out.println("❌ Error checking 'livre' table: " + e2.getMessage());
                }
            }
        } catch (SQLException e) {
            System.out.println("❌ Connection failed: " + e.getMessage());
        }
    }
}
