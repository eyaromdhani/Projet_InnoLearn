package com.innolearn.service;

import com.innolearn.model.Project;
import com.innolearn.util.DatabaseConnection;
import org.junit.jupiter.api.*;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.MockedStatic;
import org.mockito.Mockito;
import org.mockito.junit.jupiter.MockitoExtension;

import java.sql.*;
import java.time.LocalDate;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("Tests du service ProjectService")
class ProjectServiceTest {

    private ProjectService projectService;
    private Connection mockConnection;
    private PreparedStatement mockPreparedStatement;
    private Statement mockStatement;
    private ResultSet mockResultSet;

    @BeforeEach
    void setUp() throws SQLException {
        projectService       = new ProjectService();
        mockConnection       = mock(Connection.class);
        mockPreparedStatement = mock(PreparedStatement.class);
        mockStatement        = mock(Statement.class);
        mockResultSet        = mock(ResultSet.class);
    }

    // ─── Helper ────────────────────────────────────────────────────────────────

    private Project buildSampleProject() {
        Project p = new Project();
        p.setTitle("Test Project");
        p.setDescription("Une description de test");
        p.setStatus("active");
        p.setDifficulty("Débutant");
        p.setStartDate(Date.valueOf(LocalDate.now()));
        p.setEndDate(Date.valueOf(LocalDate.now().plusDays(30)));
        return p;
    }

    // ─── getAllProjects ─────────────────────────────────────────────────────────

    @Test
    @DisplayName("getAllProjects retourne une liste de projets depuis la DB")
    void testGetAllProjects_returnsList() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.createStatement()).thenReturn(mockStatement);
            when(mockStatement.executeQuery(anyString())).thenReturn(mockResultSet);

            // Simulate 2 rows
            when(mockResultSet.next()).thenReturn(true, true, false);
            when(mockResultSet.getInt("id")).thenReturn(1, 2);
            when(mockResultSet.getString("title")).thenReturn("Projet A", "Projet B");
            when(mockResultSet.getString("description")).thenReturn("Desc A", "Desc B");
            when(mockResultSet.getString("status")).thenReturn("active", "completed");
            when(mockResultSet.getDate("start_date")).thenReturn(Date.valueOf(LocalDate.now()));
            when(mockResultSet.getDate("end_date")).thenReturn(Date.valueOf(LocalDate.now().plusMonths(1)));
            when(mockResultSet.getTimestamp("created_at")).thenReturn(new Timestamp(System.currentTimeMillis()));
            when(mockResultSet.getTimestamp("updated_at")).thenReturn(null);
            when(mockResultSet.getString("generated_image")).thenReturn(null);
            when(mockResultSet.getString("difficulty")).thenReturn("Débutant", "Expert");

            List<Project> projects = projectService.getAllProjects();

            assertEquals(2,         projects.size());
            assertEquals("Projet A", projects.get(0).getTitle());
            assertEquals("Projet B", projects.get(1).getTitle());
        }
    }

    @Test
    @DisplayName("getAllProjects retourne une liste vide si la table est vide")
    void testGetAllProjects_emptyTable() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.createStatement()).thenReturn(mockStatement);
            when(mockStatement.executeQuery(anyString())).thenReturn(mockResultSet);
            when(mockResultSet.next()).thenReturn(false);

            List<Project> projects = projectService.getAllProjects();
            assertNotNull(projects);
            assertTrue(projects.isEmpty());
        }
    }

    // ─── addProject ────────────────────────────────────────────────────────────

    @Test
    @DisplayName("addProject exécute un INSERT sans exception")
    void testAddProject_success() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);

            Project p = buildSampleProject();
            assertDoesNotThrow(() -> projectService.addProject(p));
            verify(mockPreparedStatement, times(1)).executeUpdate();
        }
    }

    @Test
    @DisplayName("addProject lève une SQLException si la connexion échoue")
    void testAddProject_sqlException() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenThrow(new SQLException("Connexion refusée"));

            Project p = buildSampleProject();
            assertThrows(SQLException.class, () -> projectService.addProject(p));
        }
    }

    // ─── updateProject ─────────────────────────────────────────────────────────

    @Test
    @DisplayName("updateProject exécute un UPDATE sans exception")
    void testUpdateProject_success() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);

            Project p = buildSampleProject();
            p.setId(5);
            assertDoesNotThrow(() -> projectService.updateProject(p));
            verify(mockPreparedStatement, times(1)).executeUpdate();
        }
    }

    // ─── deleteProject ─────────────────────────────────────────────────────────

    @Test
    @DisplayName("deleteProject exécute un DELETE avec l'ID correct")
    void testDeleteProject_success() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenReturn(mockConnection);
            when(mockConnection.prepareStatement(anyString())).thenReturn(mockPreparedStatement);

            assertDoesNotThrow(() -> projectService.deleteProject(99));
            verify(mockPreparedStatement, times(1)).setInt(1, 99);
            verify(mockPreparedStatement, times(1)).executeUpdate();
        }
    }

    @Test
    @DisplayName("deleteProject lève une SQLException si la connexion échoue")
    void testDeleteProject_sqlException() throws SQLException {
        try (MockedStatic<DatabaseConnection> dbMock = Mockito.mockStatic(DatabaseConnection.class)) {
            dbMock.when(DatabaseConnection::getConnection).thenThrow(new SQLException("Timeout"));
            assertThrows(SQLException.class, () -> projectService.deleteProject(1));
        }
    }
}
