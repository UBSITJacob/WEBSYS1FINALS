<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = ""; // Change if needed
    private $dbname = "evelio_db";
    public $conn; // made public so it can be accessed directly

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Database Connection Failed: " . $this->conn->connect_error);
        }
    }

    // ✅ Allow other scripts to use the same DB connection
    public function getConnection() {
        return $this->conn;
    }
}

class Login extends Database {

    /* ----------- ADMIN LOGIN ----------- */
    public function loginAdmin($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND user_type = 'Admin' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (empty($admin['profile_pic'])) {
                $admin['profile_pic'] = 'img/default.jpg';
            }
            return $admin;
        }
        return false;
    }

    /* ----------- TEACHER LOGIN ----------- */
    public function loginTeacher($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND user_type = 'Teacher' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $teacher = $result->fetch_assoc();
            if (empty($teacher['profile_pic'])) {
                $teacher['profile_pic'] = 'img/default.jpg';
            }
            return $teacher;
        }
        return false;
    }

    /* ----------- STUDENT LOGIN ----------- */
    public function loginStudent($schoolId, $birthdate, $password) {
        $birthdateFormatted = date('Y-m-d', strtotime($birthdate));

        $sql = "
            SELECT u.*, sd.school_id, sd.birthdate, sd.gender, sd.status
            FROM users u
            JOIN student_details sd ON sd.user_id = u.id
            WHERE sd.school_id = ? AND sd.birthdate = ? AND u.password = ? AND u.user_type = 'Student'
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $schoolId, $birthdateFormatted, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();
            if (empty($student['profile_pic'])) {
                $student['profile_pic'] = 'img/default.jpg';
            }
            return $student;
        }
        return false;
    }
}
/* ----------- AUTO-CREATE SUPERADMIN ----------- */
function ensureSuperAdminExists() {
    $db = new Database();
    $conn = $db->getConnection();

    $conn->query("\n        CREATE TABLE IF NOT EXISTS users (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            username VARCHAR(100) NOT NULL,\n            password VARCHAR(255) NOT NULL,\n            email VARCHAR(150) NOT NULL,\n            fullname VARCHAR(150) NOT NULL,\n            user_type VARCHAR(20) NOT NULL,\n            profile_pic VARCHAR(255) DEFAULT NULL\n        )\n    ");

    $superUsername = "superadmin";
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? AND user_type = 'Admin' LIMIT 1");
    $check->bind_param("s", $superUsername);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $fullname = "System Administrator";
        $email = "admin@a.evelio.edu";
        $password = "admin"; // default

        $insert = $conn->prepare("
            INSERT INTO users (username, password, email, fullname, user_type)
            VALUES (?, ?, ?, ?, 'Admin')
        ");
        $insert->bind_param("ssss", $superUsername, $password, $email, $fullname);
        $insert->execute();

        echo "<p style='text-align:center; color:green; font-weight:bold;'>
            ✅ SuperAdmin account created successfully!<br>
            Username: superadmin | Password: admin
        </p>";
    }
    $check->close();
    $conn->close();
}

// ✅ Automatically ensure SuperAdmin exists
ensureSuperAdminExists();
?>
