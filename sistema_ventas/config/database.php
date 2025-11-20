<?php
/**
 * Archivo de conexión a la base de datos
 * Sistema de Gestión de Ventas de Celulares
 */

class Database {
    private $host = "localhost";
    private $user = "root";      // Usuario por defecto de XAMPP
    private $password = "";      // Contraseña por defecto de XAMPP (vacía)
    private $database = "sistema_ventas_celulares";
    private $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        
        // Verificar conexión
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
        
        // Establecer charset
        $this->conn->set_charset("utf8");
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Ejecutar consulta SELECT
     */
    public function executeQuery($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            die("Error en consulta: " . $this->conn->error);
        }
        return $result;
    }
    
    /**
     * Ejecutar procedimiento almacenado
     */
    public function executeProcedure($procedureName, $params = []) {
        $placeholders = str_repeat('?,', count($params));
        $placeholders = rtrim($placeholders, ',');
        
        $stmt = $this->conn->prepare("CALL $procedureName($placeholders)");
        
        if (!$stmt) {
            die("Error al preparar procedimiento: " . $this->conn->error);
        }
        
        // Bind parameters si existen
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // todos como string por simplicidad
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
}

// Función helper para obtener conexión rápida
function getDBConnection() {
    $db = new Database();
    return $db->getConnection();
}

// Función para mostrar errores de forma amigable
function showError($message) {
    echo "<div class='error'>Error: $message</div>";
}

// Función para mostrar mensajes de éxito
function showSuccess($message) {
    echo "<div class='success'>$message</div>";
}
?>