<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "evelio_db";
    public $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Database Connection Failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

class Login extends Database {

    /** --------------------------------------------------
     * UNIVERSAL LOGIN SYSTEM (simple password check)
     * -------------------------------------------------- */
    public function loginUser($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows !== 1) {
            return false; // No user found
        }

        $user = $result->fetch_assoc();

        // If you add hashing later → replace with password_verify()
        if ($user['password'] === $password) {
            return $user; // Successful login
        }

        return false; // Wrong password
    }

    /** --------------------------------------------------
     * CREATE USER (for admin panel or registration)
     * -------------------------------------------------- */
    public function createUser($username, $password, $email, $fullname, $user_type) {
        $sql = "INSERT INTO users (username, password, email, fullname, user_type)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $password, $email, $fullname, $user_type);

        return $stmt->execute();
    }
}

/** ---------------------------------------------------------
 * AUTO-CREATE DEFAULT SUPERADMIN IF NONE EXISTS
 * --------------------------------------------------------- */
function ensureSuperAdminExists() {
    $db = new mysqli("localhost", "root", "", "evelio_db");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $check = $db->query("SELECT * FROM users WHERE user_type = 'Admin' LIMIT 1");

    if ($check->num_rows == 0) {

        $username = "superadmin";
        $email = "admin@evelio.edu";
        $fullname = "System Administrator";
        $password = "admin"; // default password
        $type = "Admin";

        $insert = $db->prepare("
            INSERT INTO users (username, password, email, fullname, user_type)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insert->bind_param("sssss", $username, $password, $email, $fullname, $type);
        $insert->execute();

        echo "<p style='color:green; text-align:center; font-weight:bold;'>
            SuperAdmin created!<br>
            Email: admin@evelio.edu | Password: admin
        </p>";
    }

    $db->close();
}

ensureSuperAdminExists();
?>
