<?php
// This is a direct database fix script to ensure pending LPOs appear on the approval page

// Set up database connection directly
$host = 'localhost';
$database = 'efficentrix'; // You may need to adjust this to your actual database name
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password is empty

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "Connected to database successfully\n\n";

    // Find procurement officers
    $query = "SELECT id, name, purchase_order_authorization_level FROM users WHERE id IN 
              (SELECT user_id FROM role_user WHERE role_id IN 
               (SELECT id FROM roles WHERE slug = 'procurement-officer'))";
    $stmt = $pdo->query($query);
    $procurementOfficers = $stmt->fetchAll();

    if (count($procurementOfficers) > 0) {
        // Get the first procurement officer
        $user = $procurementOfficers[0];
        $userId = $user['id'];
        $userName = $user['name'];
        $authLevel = $user['purchase_order_authorization_level'] ?? 2; // Default to level 2 if not set
        
        echo "Using procurement officer: {$userName} (ID: {$userId}, Level: {$authLevel})\n\n";
    } else {
        // If no procurement officers found, get the first admin user
        $query = "SELECT id, name FROM users LIMIT 1";
        $stmt = $pdo->query($query);
        $user = $stmt->fetch();
        $userId = $user['id'];
        $userName = $user['name'];
        $authLevel = 2; // Default to level 2
        
        echo "No procurement officers found. Using user: {$userName} (ID: {$userId})\n\n";
    }

    // Find all pending LPOs
    $query = "SELECT id, purchase_no, status FROM wa_purchase_orders 
              WHERE status IN ('PENDING', 'pending', 'UNAPPROVED', 'unapproved')";
    $stmt = $pdo->query($query);
    $pendingLPOs = $stmt->fetchAll();

    echo "Found " . count($pendingLPOs) . " pending LPOs:\n";

    // Process each pending LPO
    foreach ($pendingLPOs as $lpo) {
        $lpoId = $lpo['id'];
        $lpoNo = $lpo['purchase_no'];
        $lpoStatus = $lpo['status'];
        
        echo "- LPO #{$lpoNo} (ID: {$lpoId})\n";
        
        // Check if a permission record already exists
        $checkQuery = "SELECT id, status FROM wa_purchase_order_permissions 
                       WHERE wa_purchase_order_id = :lpo_id AND user_id = :user_id";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([
            ':lpo_id' => $lpoId,
            ':user_id' => $userId
        ]);
        $existingPermission = $checkStmt->fetch();
        
        if ($existingPermission) {
            // Permission exists, check if it needs to be updated
            $permId = $existingPermission['id'];
            $permStatus = $existingPermission['status'];
            
            echo "  Permission record already exists (ID: {$permId})\n";
            
            // Update status to NEW if needed
            if ($permStatus != 'NEW') {
                $updateQuery = "UPDATE wa_purchase_order_permissions 
                               SET status = 'NEW', note = 'Updated by fix script' 
                               WHERE id = :perm_id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([':perm_id' => $permId]);
                echo "  Updated permission status to NEW\n";
            }
        } else {
            // Create a new permission record
            $insertQuery = "INSERT INTO wa_purchase_order_permissions 
                           (wa_purchase_order_id, user_id, approve_level, status, note, created_at, updated_at) 
                           VALUES (:lpo_id, :user_id, :auth_level, 'NEW', 'Auto-created by fix script', NOW(), NOW())";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([
                ':lpo_id' => $lpoId,
                ':user_id' => $userId,
                ':auth_level' => $authLevel
            ]);
            echo "  Created new permission record\n";
        }
        
        // Make sure LPO status is consistently uppercase if needed
        if (strtoupper($lpoStatus) != $lpoStatus) {
            $uppercaseStatus = strtoupper($lpoStatus);
            $updateQuery = "UPDATE wa_purchase_orders SET status = :status WHERE id = :lpo_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([
                ':status' => $uppercaseStatus,
                ':lpo_id' => $lpoId
            ]);
            echo "  Updated LPO status to {$uppercaseStatus}\n";
        }
    }

    echo "\nFix complete. Please go to the approve-lpo page now to see your pending LPOs.\n";
    echo "URL: http://localhost/efficentrix/public/admin/approve-lpo\n";
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
