<?php
class Response {
    public static function json($data, $status = 200) {
        // Set the HTTP response code
        http_response_code($status);
        
        // Set headers
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        // Return JSON
        echo json_encode($data);
    }
    
    public static function success($message, $data = null) {
        $response = [
            "status" => "success",
            "message" => $message
        ];
        
        if ($data !== null) {
            $response["data"] = $data;
        }
        
        self::json($response);
    }
    
    public static function error($message, $status = 400) {
        $response = [
            "status" => "error",
            "message" => $message
        ];
        
        self::json($response, $status);
    }
}
?>