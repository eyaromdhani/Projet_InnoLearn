package com.innolearn.service;

import com.innolearn.model.Depot;
import com.innolearn.util.DatabaseConnection;
import org.junit.jupiter.api.*;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.MockedStatic;
import org.mockito.Mockito;
import org.mockito.junit.jupiter.MockitoExtension;

import java.sql.*;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("Tests du service DepotService")
class DepotServiceTest {

    private DepotService depotService;
    private Connection mockConnection;
    private PreparedStatement mockPreparedStatement;
    private Statement mockStatement;
    private ResultSet mockResultSet;

    @BeforeEach
    void setUp() throws SQLException {
        depotService          = new DepotService();
        mockConnection        = mock(Connection.class);
        mockPreparedStatement = mock(PreparedStatement.class);
        mockStatement         = mock(Statement.class);
        mockResultSet         = mock(ResultSet.class);
    }

    // ─── Helper ────────────────────────────────────────────────────────────────

    private Depot buildSampleDepot() {
        Depot d = new Depot();
        d.setTitle("Rapport de Projet");
        d.setStudentName("Omar Karim");
        d.setType("PDF");
        d.setFilePath("/depot/rapport.pdf");
        d.setProjectId(1);
        d.setUploadedAt(new Timestamp(System.currentTimeMillis()));
        return d;
    }

    private void mockResultSetRow(int id, String title, int projectId) throws SQLException {
        when(mockResultSet.getInt("id")).thenReturn(id);
        when(mockResultSet.getString("title")).thenReturn(title);
        when(mockResultSet.getString("description")).thenReturn("description");
        when(mockResultSet.getString("type")).thenReturn("PDF");
        when(mockResultSet.getString("file_path")).thenReturn("/path/file.pdf");
        when(mockResultSet.getString("file_size")).thenReturn("1MB");
        when(mockResultSet.getString("file_type")).thenReturn("application/pdf");
        when(mockResultSet.getTimestamp("uploaded_at")).thenReturn(new Timestamp(System.currentTimeMillis()));
        when(mockResultSet.getInt("project_id")).thenReturn(projectId);
        when(mockResultSet.getString("student_name")).thenReturn("Étudiant");
        when(mockResultSet.getInt("download_count")).thenReturn(0);
        when(mockResultSet.getObject("user_id")).thenReturn(null);
    }

    // ─── getAllDepots ───────────────────────────────────────────────────────────

    @Test
    @DisplayName("getAllDepots retourne une liste de dépôts")
    void testGetAllDepots_returnsList() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.createStatement()).thenReturn(mockStatement);
            when(mockStatement.executeQuery(anyString())).thenReturn(mockResultSet);

            when(mockResultSet.next()).thenReturn(true, false);
            mockResultSetRow(1, "Soumission 1", 5);

            List<Depot> depots = depotService.getAllDepots();
            assertEquals(1, depots.size());
            assertEquals("Soumission 1", depots.get(0).getTitle());
        }
    }

    @Test
    @DisplayName("getAllDepots retourne une liste vide si aucun dépôt")
    void testGetAllDepots_emptyTable() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.createStatement()).thenReturn(mockStatement);
            when(mockStatement.executeQuery(anyString())).thenReturn(mockResultSet);
            when(mockResultSet.next()).thenReturn(false);

            List<Depot> depots = depotService.getAllDepots();
            assertNotNull(depots);
            assertTrue(depots.isEmpty());
        }
    }

    // ─── getDepotsByProject ─────────────────────────────────────────────────────

    @Test
    @DisplayName("getDepotsByProject retourne les dépôts filtrés par projet")
    void testGetDepotsByProject_success() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);
            when(mockPreparedStatement.executeQuery()).thenReturn(mockResultSet);

            when(mockResultSet.next()).thenReturn(true, true, false);
            mockResultSetRow(1, "Fichier A", 10);

            List<Depot> depots = depotService.getDepotsByProject(10);

            verify(mockPreparedStatement).setInt(1, 10);
            assertEquals(2, depots.size());
        }
    }

    @Test
    @DisplayName("getDepotsByProject retourne liste vide pour projet sans dépôt")
    void testGetDepotsByProject_empty() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);
            when(mockPreparedStatement.executeQuery()).thenReturn(mockResultSet);
            when(mockResultSet.next()).thenReturn(false);

            List<Depot> depots = depotService.getDepotsByProject(999);
            assertTrue(depots.isEmpty());
        }
    }

    // ─── addDepot ──────────────────────────────────────────────────────────────

    @Test
    @DisplayName("addDepot exécute un INSERT sans exception")
    void testAddDepot_success() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);

            Depot d = buildSampleDepot();
            assertDoesNotThrow(() -> depotService.addDepot(d));
            verify(mockPreparedStatement, times(1)).executeUpdate();
        }
    }

    @Test
    @DisplayName("addDepot lève une SQLException si la connexion échoue")
    void testAddDepot_sqlException() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenThrow(new SQLException("DB indisponible"));
            Depot d = buildSampleDepot();
            assertThrows(SQLException.class, () -> depotService.addDepot(d));
        }
    }

    // ─── deleteDepot ───────────────────────────────────────────────────────────

    @Test
    @DisplayName("deleteDepot exécute un DELETE avec le bon ID")
    void testDeleteDepot_success() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);

            assertDoesNotThrow(() -> depotService.deleteDepot(7));
            verify(mockPreparedStatement, times(1)).setInt(1, 7);
            verify(mockPreparedStatement, times(1)).executeUpdate();
        }
    }

    @Test
    @DisplayName("deleteDepot lève une SQLException si la DB est inaccessible")
    void testDeleteDepot_sqlException() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenThrow(new SQLException("Timeout connexion"));
            assertThrows(SQLException.class, () -> depotService.deleteDepot(1));
        }
    }
}
