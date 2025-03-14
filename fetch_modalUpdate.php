<?php
// Include database connection
include 'conn.php'; // Change this to your actual database connection file

header('Content-Type: application/json'); // Return JSON response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get record ID
    $recordId = isset($_POST['recordId']) ? intval($_POST['recordId']) : 0;

    if ($recordId == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid Record ID."]);
        exit();
    }

    // Get form data
    $fy = $_POST['fy'] ?? '';
    $month = $_POST['month'] ?? '';
    $date = $_POST['date'] ?? '';
    $category = $_POST['category'] ?? '';
    $trigger = $_POST['trigger'] ?? '';
    $nt_nf = $_POST['nt_nf'] ?? '';
    $issue = $_POST['issue'] ?? '';
    $partNumber = $_POST['partNumber'] ?? '';
    $product = $_POST['product'] ?? '';
    $lotSublot = $_POST['lotSublot'] ?? '';
    $inValue = $_POST['inValue'] ?? '';
    $outValue = $_POST['outValue'] ?? '';
    $reject = $_POST['reject'] ?? '';

    try {
        // Ensure part number and product exist
        if (!empty($partNumber) && !empty($product)) {
            $query = "SELECT ID FROM PRODUCT_LIST WHERE PARTNUMBER = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $partNumber);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $insertQuery = "INSERT INTO PRODUCT_LIST (PARTNUMBER, PARTNAME) VALUES (?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ss", $partNumber, $product);
                $insertStmt->execute();
                $partId = $conn->insert_id;
                $insertStmt->close();
            } else {
                $stmt->bind_result($partId);
                $stmt->fetch();
            }
            $stmt->close();
        }

        // Ensure category exists
        $query = "SELECT ID FROM category_tbl WHERE cat_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $insertQuery = "INSERT INTO category_tbl (cat_name) VALUES (?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("s", $category);
            $insertStmt->execute();
            $categoryId = $conn->insert_id;
            $insertStmt->close();
        } else {
            $stmt->bind_result($categoryId);
            $stmt->fetch();
        }
        $stmt->close();

        // Ensure trigger exists
        $query = "SELECT ID FROM trigger_tbl WHERE trigger_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $trigger);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $insertQuery = "INSERT INTO trigger_tbl (trigger_name) VALUES (?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("s", $trigger);
            $insertStmt->execute();
            $triggerId = $conn->insert_id;
            $insertStmt->close();
        } else {
            $stmt->bind_result($triggerId);
            $stmt->fetch();
        }
        $stmt->close();

        // Update FPC table
        $stmt = $conn->prepare("UPDATE FPC SET FY = ?, MONTH = ?, DATE = ?, CATEGORY_ID = ?, TRIGGER_ID = ?, NT_NF = ?, ISSUE = ?, PART_ID = ?, PRODUCT = ?, LOT_SUBLOT = ?, IN_VALUE = ?, OUT_VALUE = ?, REJECT = ? WHERE ID = ?");
        
        $stmt->bind_param("sssssssssssssi", 
            $fy, $month, $date, $categoryId, $triggerId, $nt_nf, $issue, 
            $partId, $product, $lotSublot, $inValue, $outValue, $reject, $recordId
        );

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update record."]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }

    // Close database connection
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
