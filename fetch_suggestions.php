<?php
include 'conn.php'; // Include your connection script

header('Content-Type: application/json');

$suggestions = [];

if ($db_type == "access") {
    $query = "SELECT DISTINCT CATEGORY FROM FPC";
    $result = odbc_exec($conn, $query);
    if ($result) {
        while ($row = odbc_fetch_array($result)) {
            $suggestions[] = $row['CATEGORY'];
        }
    }
} else {
    $query = "SELECT DISTINCT CATEGORY FROM FPC";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $suggestions[] = $row['CATEGORY'];
        }
    }
}

echo json_encode($suggestions);
?>
