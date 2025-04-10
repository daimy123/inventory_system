<?php
// Set error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
include_once '../api/config/database.php';
include_once '../api/utils/response.php';

// Test class
class ProductTest {
    private $db;
    private $test_product_id;
    private $all_test_ids = [];
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function runTests() {
        echo "<h1>Running Unit Tests</h1>";
        
        try {
            $this->testCreate();
            $this->testRead();
            $this->testReadOne();
            $this->testUpdate();
            $this->testDelete();
            
            // Clean up any remaining test products
            $this->cleanUp();
            
            echo "<p style='color: green; font-weight: bold;'>All tests completed successfully!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red; font-weight: bold;'>Test failed: " . $e->getMessage() . "</p>";
        }
    }
    
    private function testCreate() {
        echo "<h2>Testing Create Product</h2>";
        
        // Test data
        $test_product = [
            'name' => 'Test Product ' . uniqid(),
            'description' => 'This is a test product created by unit test',
            'category' => 'Test Category',
            'quantity' => 10,
            'price' => 19.99
        ];
        
        try {
            // Create query
            $query = "INSERT INTO products 
                     SET name=:name, description=:description, category=:category, 
                         quantity=:quantity, price=:price";
            
            // Prepare query
            $stmt = $this->db->prepare($query);
            
            // Bind values
            $stmt->bindParam(":name", $test_product['name']);
            $stmt->bindParam(":description", $test_product['description']);
            $stmt->bindParam(":category", $test_product['category']);
            $stmt->bindParam(":quantity", $test_product['quantity']);
            $stmt->bindParam(":price", $test_product['price']);
            
            // Execute query
            if($stmt->execute()) {
                $this->test_product_id = $this->db->lastInsertId();
                $this->all_test_ids[] = $this->test_product_id;
                
                echo "<p>✓ Create test passed! Product ID: {$this->test_product_id}</p>";
            } else {
                throw new Exception("Create product failed");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    private function testRead() {
        echo "<h2>Testing Read Products</h2>";
        
        try {
            // Create query
            $query = "SELECT id FROM products LIMIT 1";
            
            // Prepare statement
            $stmt = $this->db->prepare($query);
            
            // Execute query
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                echo "<p>✓ Read test passed! Found products in database.</p>";
            } else {
                throw new Exception("No products found in database");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    private function testReadOne() {
        echo "<h2>Testing Read One Product</h2>";
        
        if (!$this->test_product_id) {
            throw new Exception("No test product ID available");
        }
        
        try {
            // Query to read single product
            $query = "SELECT * FROM products WHERE id = ? LIMIT 0,1";
            
            // Prepare statement
            $stmt = $this->db->prepare($query);
            
            // Bind ID
            $stmt->bindParam(1, $this->test_product_id);
            
            // Execute query
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>✓ Read One test passed! Found product: {$row['name']}</p>";
            } else {
                throw new Exception("Product not found");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    private function testUpdate() {
        echo "<h2>Testing Update Product</h2>";
        
        if (!$this->test_product_id) {
            throw new Exception("No test product ID available");
        }
        
        // Updated data
        $updated_name = "Updated Test Product " . uniqid();
        
        try {
            // Create query
            $query = "UPDATE products SET name = :name WHERE id = :id";
            
            // Prepare statement
            $stmt = $this->db->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':name', $updated_name);
            $stmt->bindParam(':id', $this->test_product_id);
            
            // Execute query
            if($stmt->execute()) {
                // Check if update was successful by retrieving the updated record
                $verify_query = "SELECT name FROM products WHERE id = ?";
                $verify_stmt = $this->db->prepare($verify_query);
                $verify_stmt->bindParam(1, $this->test_product_id);
                $verify_stmt->execute();
                
                if($verify_stmt->rowCount() > 0) {
                    $row = $verify_stmt->fetch(PDO::FETCH_ASSOC);
                    if($row['name'] === $updated_name) {
                        echo "<p>✓ Update test passed! Product name updated to: {$updated_name}</p>";
                    } else {
                        throw new Exception("Product name was not updated correctly");
                    }
                } else {
                    throw new Exception("Updated product not found");
                }
            } else {
                throw new Exception("Update product failed");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    private function testDelete() {
        echo "<h2>Testing Delete Product</h2>";
        
        if (!$this->test_product_id) {
            throw new Exception("No test product ID available");
        }
        
        try {
            // Create query
            $query = "DELETE FROM products WHERE id = ?";
            
            // Prepare statement
            $stmt = $this->db->prepare($query);
            
            // Bind id
            $stmt->bindParam(1, $this->test_product_id);
            
            // Execute query
            if($stmt->execute() && $stmt->rowCount() > 0) {
                echo "<p>✓ Delete test passed! Product deleted successfully.</p>";
                
                // Remove from list of test IDs to clean up
                $key = array_search($this->test_product_id, $this->all_test_ids);
                if($key !== false) {
                    unset($this->all_test_ids[$key]);
                }
            } else {
                throw new Exception("Delete product failed");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    private function cleanUp() {
        echo "<h2>Cleaning up test data</h2>";
        
        if(empty($this->all_test_ids)) {
            echo "<p>No test products to clean up.</p>";
            return;
        }
        
        try {
            foreach($this->all_test_ids as $id) {
                $query = "DELETE FROM products WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $id);
                $stmt->execute();
            }
            echo "<p>✓ Clean up complete.</p>";
        } catch (PDOException $e) {
            echo "<p>Warning: Clean up error: " . $e->getMessage() . "</p>";
        }
    }
}

// Run the tests
$test = new ProductTest();
$test->runTests();
?>