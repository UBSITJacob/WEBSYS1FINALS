<?php
/**
 * Utility: ApiResponse Handler
 * Standardized API response formatting
 */

class ApiResponse {
    
    /**
     * Success response
     */
    public static function success($data = null, $message = 'Operation successful') {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Error response
     */
    public static function error($message = 'An error occurred', $code = 400) {
        return [
            'success' => false,
            'error' => $message,
            'code' => $code,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Send JSON response with HTTP code
     */
    public static function send($response, $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * Validate input data
     */
    public static function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            list($type, $value) = $rule;
            
            switch ($type) {
                case 'required':
                    if (empty($value)) {
                        $errors[] = "$field is required";
                    }
                    break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "$field must be valid email";
                    }
                    break;
                case 'integer':
                    if (!is_int($value) && !ctype_digit((string)$value)) {
                        $errors[] = "$field must be integer";
                    }
                    break;
            }
        }
        return empty($errors) ? true : $errors;
    }
}
?>
