<?php
// Dropdown options (You can update these based on your database values)
$selectmonths = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
];
$NT_NF = ["NTPI", "NFLD"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data safely (handling multiple entries)
    $FY = $_POST['FY'] ?? "FY25";
    $MONTH = $_POST['MONTH'] ?? [];

    // Ensure arrays are set to prevent errors
    $DATES = $_POST['DATE'] ?? [];
    $CATEGORIES = $_POST['CATEGORY'] ?? [];
    $TRIGGERS = $_POST['TRIGGER'] ?? [];
    $NT_NFS = $_POST['NT_NF'] ?? [];
    $ISSUES = $_POST['ISSUE'] ?? [];
    $PART_NOS = $_POST['PART_NO'] ?? [];
    $PRODUCTS = $_POST['PRODUCT'] ?? [];
    $LOT_SUBLOTS = $_POST['LOT_SUBLOT'] ?? [];
    $INS = $_POST['IN'] ?? [];
    $OUTS = $_POST['OUT'] ?? [];
    $REJECTS = $_POST['REJECT'] ?? [];

    // Validate if required fields have data
    if (empty($CATEGORIES) || empty($DATES)) {
        die("Error: Missing required data.");
    }

    // Start transaction to ensure atomic operations
    mysqli_begin_transaction($conn);

    try {
        foreach ($CATEGORIES as $index => $CATEGORY) {
            $MONTH_VAL = $MONTH[$index] ?? '';
            $DATE = $DATES[$index] ?? date('Y-m-d');
            $TRIGGER = $TRIGGERS[$index] ?? '';
            $NT_NF = $NT_NFS[$index] ?? '';
            $ISSUE = $ISSUES[$index] ?? '';
            $PART_NO = $PART_NOS[$index] ?? '';
            $PRODUCT = $PRODUCTS[$index] ?? '';
            $LOT_SUBLOT = $LOT_SUBLOTS[$index] ?? '';
            $IN = $INS[$index] ?? 0;
            $OUT = $OUTS[$index] ?? 0;
            $REJECT = $REJECTS[$index] ?? 0;

            // Ensure part number and product exist
            if (!empty($PART_NO) && !empty($PRODUCT)) {
                $query = "SELECT ID FROM PRODUCT_LIST WHERE PARTNUMBER = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $PART_NO);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 0) {
                    $insertQuery = "INSERT INTO PRODUCT_LIST (PARTNUMBER, PARTNAME) VALUES (?, ?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("ss", $PART_NO, $PRODUCT);
                    $insertStmt->execute();
                    $PART_ID = $conn->insert_id;
                    $insertStmt->close();
                } else {
                    $stmt->bind_result($PART_ID);
                    $stmt->fetch();
                }
                $stmt->close();
            }

            // Ensure category exists
            $query = "SELECT ID FROM category_tbl WHERE cat_name = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $CATEGORY);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $insertQuery = "INSERT INTO category_tbl (cat_name) VALUES (?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("s", $CATEGORY);
                $insertStmt->execute();
                $CATEGORY_ID = $conn->insert_id;
                $insertStmt->close();
            } else {
                $stmt->bind_result($CATEGORY_ID);
                $stmt->fetch();
            }
            $stmt->close();

            // Ensure trigger exists
            $query = "SELECT ID FROM trigger_tbl WHERE trigger_name = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $TRIGGER);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $insertQuery = "INSERT INTO trigger_tbl (trigger_name) VALUES (?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("s", $TRIGGER);
                $insertStmt->execute();
                $TRIGGER_ID = $conn->insert_id;
                $insertStmt->close();
            } else {
                $stmt->bind_result($TRIGGER_ID);
                $stmt->fetch();
            }
            $stmt->close();

            // Insert into FPC table
            $sql = "INSERT INTO FPC (FY, MONTH, DATE, CATEGORY_ID, TRIGGER_ID, NT_NF, ISSUE, PART_ID, PRODUCT, LOT_SUBLOT, IN_VALUE, OUT_VALUE, REJECT) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("MySQL prepare failed: " . $conn->error);
            }

            $stmt->bind_param(
                "ssssssssssiii",
                $FY,
                $MONTH_VAL,
                $DATE,
                $CATEGORY_ID,
                $TRIGGER_ID,
                $NT_NF,
                $ISSUE,
                $PART_ID,
                $PRODUCT,
                $LOT_SUBLOT,
                $IN,
                $OUT,
                $REJECT
            );

            if (!$stmt->execute()) {
                throw new Exception("Error inserting record: " . $stmt->error);
            }

            $stmt->close();
        }

        // Commit transaction if everything is successful
        mysqli_commit($conn);
    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback in case of error
        die("Transaction failed: " . $e->getMessage());
    }
}
?>