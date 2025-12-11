<?php
/**
 * Utility: AuthHelper
 * Authentication and authorization helpers
 */

class AuthHelper {
    
    /**
     * Require user to be logged in
     */
    public static function requireLogin() {
        if (!isset($_SESSION['account_id']) || !isset($_SESSION['role'])) {
            header('Location: index.php');
            exit;
        }
        return true;
    }
    
    /**
     * Require specific role
     */
    public static function requireRole($role) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
            http_response_code(403);
            return false;
        }
        return true;
    }
    
    /**
     * Require admin role
     */
    public static function requireAdmin() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return false;
        }
        return true;
    }
    
    /**
     * Require teacher role
     */
    public static function requireTeacher() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
            return false;
        }
        return true;
    }
    
    /**
     * Require student role
     */
    public static function requireStudent() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
            return false;
        }
        return true;
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId() {
        return $_SESSION['account_id'] ?? null;
    }
    
    /**
     * Get current user role
     */
    public static function getRole() {
        return $_SESSION['role'] ?? null;
    }
}
?>
