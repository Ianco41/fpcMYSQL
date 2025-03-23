<?php
include 'conn.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['FPC'])) {
    $fpc_id = $_GET['FPC']; // Use the correct GET parameter

    // Query with JOINs to fetch FPC details
    $query = "
        SELECT 
            fpc.*, 
            product_list.PARTNAME, product_list.PARTNUMBER, 
            category_tbl.cat_name, 
            trigger_tbl.trigger_name 
        FROM fpc
        LEFT JOIN product_list ON fpc.PART_ID = product_list.ID 
        LEFT JOIN category_tbl ON fpc.CATEGORY_ID = category_tbl.ID 
        LEFT JOIN trigger_tbl ON fpc.TRIGGER_ID = trigger_tbl.ID 
        WHERE fpc.ID = ?
    ";

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $fpc_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data and display form
    if ($data = $result->fetch_assoc()) {
?>
        <?php
function renderDetailRow($label, $value, $colSize = 4) {
    echo "<div class='col-md-$colSize mb-3'>";
    echo "<strong style='color: #007bff;'>$label:</strong> <span style='color: #333; font-weight: 500;'>" . htmlspecialchars($value) . "</span>";
    echo "</div>";
}
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <?php 
                renderDetailRow("FY", $data['FY']);
                renderDetailRow("Month", $data['MONTH']);
                renderDetailRow("Date", $data['DATE']);
                ?>
            </div>

            <div class="row">
                <?php 
                renderDetailRow("Category", $data['cat_name']);
                renderDetailRow("Trigger", $data['trigger_name']);
                renderDetailRow("NT/NF", $data['NT_NF']);
                ?>
            </div>

            <div class="row">
                <?php 
                renderDetailRow("Issue", $data['ISSUE']);
                renderDetailRow("Part Number", $data['PARTNUMBER']);
                renderDetailRow("Part Name", $data['PARTNAME']);
                ?>
            </div>

            <div class="row">
                <?php renderDetailRow("Lot/Sublot", $data['LOT_SUBLOT'], 12); ?>
            </div>

            <div class="row">
                <?php 
                renderDetailRow("Qty-In", $data['IN_VALUE'], 3);
                renderDetailRow("Qty-Out", $data['OUT_VALUE'], 3);
                renderDetailRow("Reject", $data['REJECT'], 3);
                renderDetailRow("Minutes", $data['MINUTES'], 3);
                ?>
            </div>
        </div>
    </div>
</div>


<?php
    } else {
        echo "<div class='container mt-4'><h4>Record not found</h4></div>";
    }

    // Close statement and connection
    $stmt->close();
}

$conn->close();
?>