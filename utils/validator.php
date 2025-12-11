<?php
/**
 * Utility: Validator
 * Input sanitization and validation
 */

class Validator {
    
    /**
     * Sanitize integer
     */
    public static function sanitizeInt($value, $default = 0) {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        return $int !== false ? $int : $default;
    }
    
    /**
     * Sanitize string
     */
    public static function sanitizeString($value) {
        if (is_array($value)) return '';
        return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Sanitize email
     */
    public static function sanitizeEmail($value) {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validate ID (must be positive integer)
     */
    public static function validateId($id) {
        $id = self::sanitizeInt($id);
        return $id > 0 ? $id : null;
    }
    
    /**
     * Validate required field
     */
    public static function validateRequired($value) {
        return !empty(trim((string)$value));
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate string length
     */
    public static function validateLength($string, $min = 0, $max = 255) {
        $length = strlen($string);
        return $length >= $min && $length <= $max;
    }
    
    /**
     * Batch validate multiple fields
     */
    public static function batchValidate($rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            list($type, $value) = $rule;
            
            switch ($type) {
                case 'required':
                    if (!self::validateRequired($value)) {
                        $errors[] = "$field is required";
                    }
                    break;
                    
                case 'email':
                    if (!empty($value) && !self::validateEmail($value)) {
                        $errors[] = "$field must be valid email";
                    }
                    break;
                    
                case 'integer':
                    if (!is_numeric($value) || self::sanitizeInt($value) === 0) {
                        $errors[] = "$field must be valid integer";
                    }
                    break;
                    
                case 'min_length':
                    list($type, $value, $min) = $rule;
                    if (strlen($value) < $min) {
                        $errors[] = "$field must be at least $min characters";
                    }
                    break;
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}
?>
