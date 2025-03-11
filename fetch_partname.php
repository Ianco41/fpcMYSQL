<?php
header('Content-Type: application/json');
include "conn.php"; // Ensure conn.php initializes the MySQLi connection

if (isset($_GET["part_no"]) && !empty($_GET["part_no"])) {
    $part_no = trim($_GET["part_no"]);

    // Prepare the query
    $stmt = $conn->prepare("SELECT PARTNAME FROM PRODUCT_LIST WHERE PARTNUMBER = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $part_no); // Bind the parameter
        $stmt->execute(); // Execute the statement

        $stmt->bind_result($partname); // Bind the result variable

        if ($stmt->fetch()) {
            echo json_encode(["success" => true, "PARTNAME" => $partname]);
        } else {
            echo json_encode(["success" => false, "message" => "Part number not found"]);
        }

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(["success" => false, "message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request: Missing part_no parameter"]);
}

// Close the connection
$conn->close();
?>
