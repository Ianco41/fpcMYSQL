<?php
include 'conn.php'; // Include your database connection

header('Content-Type: application/json'); // Set JSON response header

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query for MySQL with JOINs
    $query = "SELECT FPC.*, 
                     product_list.PARTNAME, product_list.PARTNUMBER, 
                     category_tbl.cat_name, 
                     trigger_tbl.trigger_name 
              FROM FPC 
              LEFT JOIN product_list ON FPC.PART_ID = product_list.ID 
              LEFT JOIN category_tbl ON FPC.CATEGORY_ID = category_tbl.ID 
              LEFT JOIN trigger_tbl ON FPC.TRIGGER_ID = trigger_tbl.ID 
              WHERE FPC.ID = ?"; // Ensure you're filtering by the correct column

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data and return as JSON
    if ($data = $result->fetch_assoc()) {
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Record not found"]);
    }

    // Close statement and connection
    $stmt->close();
}

$conn->close();
?>
