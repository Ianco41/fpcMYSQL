<?php
header("Content-Type: application/json");
include 'conn.php'; // Ensure database connection

$data = json_decode(file_get_contents("php://input"), true);
$recordId = $data['recordId'] ?? '';

if (!$recordId) {
    echo json_encode(["success" => false, "message" => "Invalid record ID."]);
    exit;
}

// Soft delete: Set deleted_at timestamp instead of deleting
$query = "UPDATE fpc SET deleted_at = NOW() WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $recordId);
$success = $stmt->execute();

if (!$success) {
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
    exit;
}

echo json_encode(["success" => true]);
?>
