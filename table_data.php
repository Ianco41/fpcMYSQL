<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "conn.php";

header('Content-Type: application/json');

// Get table name
$table = $_GET['table'] ?? '';

// Validate table
$valid_tables = [
    "product_list" => ["ID", "PARTNUMBER", "PARTNAME"],
    "category_tbl" => ["ID", "NAME"],
    "trigger_tbl" => ["ID", "NAME"],
    "issue_tbl" => ["ID", "NAME"]
];

if (!isset($valid_tables[$table])) {
    echo json_encode(["error" => "Invalid table name"]);
    exit;
}

// Pagination variables
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;
$searchValue = $_GET['search']['value'] ?? '';
$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDir = $_GET['order'][0]['dir'] ?? 'asc';

// Get column names
$columns = $valid_tables[$table];
$orderColumn = $columns[$orderColumnIndex] ?? $columns[0];

// SQL Query
$query = "SELECT * FROM $table";

// Apply search filter
if (!empty($searchValue)) {
    $searchConditions = [];
    foreach ($columns as $col) {
        $searchConditions[] = "$col LIKE '%" . mysqli_real_escape_string($conn, $searchValue) . "%'";
    }
    $query .= " WHERE " . implode(" OR ", $searchConditions);
}

// Ordering
$query .= " ORDER BY $orderColumn $orderDir";

// Get total records before filtering
$totalRecordsQuery = "SELECT COUNT(*) as total FROM $table";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

// Get total records after filtering
$totalFilteredQuery = "SELECT COUNT(*) as total FROM ($query) as temp";
$totalFilteredResult = mysqli_query($conn, $totalFilteredQuery);
$totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['total'];

// Apply pagination
$query .= " LIMIT $start, $length";
$result = mysqli_query($conn, $query);

// If error occurs
if (!$result) {
    echo json_encode(["error" => "SQL Error: " . mysqli_error($conn)]);
    exit;
}

// Fetch data
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// JSON Output
$response = [
    "draw" => intval($_GET['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
