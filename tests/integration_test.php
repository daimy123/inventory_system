<?php
// Set error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test class
class IntegrationTest {
    private $base_url;
    private $test_product_id;
    
    public function __construct() {
        // Get the base URL for the API
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $this->base_url = $protocol . '://' . $host . '/inventory_system/api/products/';
    }
    
    public function runTests() {
        echo "<h1>Running Integration Tests</h1>";
        
        try {
            $this->testCreateProduct();
            $this->testReadProducts();
            $this->testReadOneProduct();
            $this->testUpdateProduct();
            $this->testDeleteProduct();
            
            echo "<p style='color: green; font-weight: bold;'>All integration tests completed successfully!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red; font-weight: bold;'>Test failed: " . $e->getMessage() . "</p>";
        }
    }
    
    private function makeApiRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->base_url . $endpoint;
        
        $curl = curl_init();
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ];
        
        if ($data !== null) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        }
        
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            throw new Exception("cURL Error: " . $err);
        }
        
        return json_decode($response, true);
    }
    
    private function testCreateProduct() {
        echo "<h2>Testing Create Product API</h2>";
        
        // Test data
        $product_data = [
            'name' => 'Integration Test Product ' . uniqid(),
            'description' => 'This is a test product created by integration test',
            'category' => 'Test Category',
            'quantity' => 5,
            'price' => 29.99
        ];
        
        // Make API request
        $response = $this->makeApiRequest('create.php', 'POST', $product_data);
        
        // Check response
        if (isset($response['status']) && $response['status'] === 'success') {
            $this->test_product_id = $response['data']['id'];
            echo "<p>✓ Create API test passed! Product ID: {$this->test_product_id}</p>";
        } else {
            throw new Exception("API Error: " . ($response['message'] ?? 'Unknown error'));
        }
    }
    
    private function testReadProducts() {
        echo "<h2>Testing Read Products API</h2>";
        
        // Make API request
        $response = $this->makeApiRequest('read.php');
        
        // Check response
        if (isset($response['status']) && $response['status'] === 'success') {
            $count = count($response['data'] ?? []);
            echo "<p>✓ Read API test passed! Found {$count} products.</p>";
        } else {
            throw new Exception("API Error: " . ($response['message'] ?? 'Unknown error'));
        }
    }
    
    private function testReadOneProduct() {
        echo "<h2>Testing Read One Product API</h2>";
        
        if (!$this->test_product_id) {
            throw new Exception("No test product ID available");
        }
        
        // Make API request
        $response = $this->makeApiRequest("read_one.php?id={$this->test_product_id}");
        
        // Check response
        if (isset($response['status']) && $response['status'] === 'success') {
            $product = $response['data'];
            echo "<p>✓ Read One API test passed! Found product: {$product['name']}</p>";
        } else {
            throw new Exception("API Error: " . ($response['message'] ?? 'Unknown error'));
        }
    }
    
    private function testUpdateProduct() {
        echo "<h2>Testing Update Product API</h2>";
        
        if (!$this->test_product_id) {
            throw new Exception("No test product ID available");
        }
        
        // Updated data
        $updated_data = [
            'id' => $this->test_product_id,
            'name' => 'Updated Integration Test Product ' . uniqid(),
            'description' => 'This product was updated by the integration test',
            'price' => 39.99
        ];
        
        // Make API request
        $response = $this->makeApiRequest('update.php', 'POST', $updated_data);
        
        // Check response
        if (isset($response['status']) && $response['status'] === 'success') {
            echo "<p>✓ Update API test passed! Product updated successfully.</p>";
            
            // Verify the update
            $verify_response = $this->makeApiRequest("read_one.php?id={$this->test_product_id}");
            if (isset($verify_response['status']) && $verify_response['status'] === 'success') {
                $product = $verify_response['data'];
                if ($product['name'] === $updated_data['name']) {
                    echo "<p>✓ Update verification passed! Product name updated correctly.</p>";
                } else {
                    throw new Exception("Update verification failed: Product name was not updated correctly");
                }
            }
        } else {
            throw new Exception("API Error: " . ($response['message'] ?? 'Unknown error'));
        }
    }
    
    private function testDeleteProduct() {
        echo "<h2>Testing Delete Product API</h2>";
        
        if (!$this->test_product_id) {
            throw new Exception("No test product ID available");
        }
        
        // Make API request
        $response = $this->makeApiRequest('delete.php', 'POST', ['id' => $this->test_product_id]);
        
        // Check response
        if (isset($response['status']) && $response['status'] === 'success') {
            echo "<p>✓ Delete API test passed! Product deleted successfully.</p>";
            
            // Verify the deletion
            $verify_response = $this->makeApiRequest("read_one.php?id={$this->test_product_id}");
            if (isset($verify_response['status']) && $verify_response['status'] === 'error') {
                echo "<p>✓ Delete verification passed! Product no longer exists.</p>";
            } else {
                throw new Exception("Delete verification failed: Product still exists");
            }
        } else {
            throw new Exception("API Error: " . ($response['message'] ?? 'Unknown error'));
        }
    }
}

// Run the tests
$test = new IntegrationTest();
$test->runTests();
?>