<?php
include "conn.php";

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y'); // Get year from filter or use current year
$data = [];

if ($db_type == "access") {
    // Query for MS Access (using ODBC) with JOINs for meaningful data
    $query = "SELECT FPC.*, 
                     PARTS.part_name, 
                     CATEGORIES.category_name, 
                     TRIGGERS.trigger_name 
              FROM ((FPC 
              LEFT JOIN PARTS ON FPC.PART_ID = PARTS.ID)
              LEFT JOIN CATEGORIES ON FPC.CATEGORY_ID = CATEGORIES.ID)
              LEFT JOIN TRIGGERS ON FPC.TRIGGER_ID = TRIGGERS.ID
              WHERE DATEPART('yyyy', FPC.[date]) = $year 
              ORDER BY FPC.ID DESC";

    $result = odbc_exec($conn, $query);
    if (!$result) {
        die("Query failed: " . odbc_errormsg());
    }

    // Fetch data from ODBC
    while ($row = odbc_fetch_array($result)) {
        $data[] = $row;
    }
} else {
    // Query for MySQL with JOINs
    $query = "SELECT FPC.*, 
                     product_list.PARTNAME, PARTNUMBER, 
                     category_tbl.cat_name, 
                     trigger_tbl.trigger_name 
              FROM FPC 
              LEFT JOIN product_list ON FPC.PART_ID = product_list.ID 
              LEFT JOIN category_tbl ON FPC.CATEGORY_ID = category_tbl.ID 
              LEFT JOIN trigger_tbl ON FPC.TRIGGER_ID = trigger_tbl.ID 
              WHERE YEAR(FPC.date) = $year
              AND FPC.deleted_at IS NULL
              ORDER BY FPC.ID DESC";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch data from MySQL
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FPC</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <style>
        ::after,
        ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
        }

        li {
            list-style: none;
        }

        h1 {
            font-weight: 600;
            font-size: 1.5rem;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        .wrapper {
            display: flex;
        }

        .main {
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
            transition: all 0.35s ease-in-out;
            background-color: #fafbfe;
            margin-left: 70px;
        }

        #sidebar {
            width: 70px;
            min-width: 70px;
            height: 100vh;
            /* Full height of the viewport */
            z-index: 1000;
            transition: all .25s ease-in-out;
            background-color: #0e2238;
            display: flex;
            flex-direction: column;
            position: fixed;
            /* Fixes the sidebar in place */
            top: 0;
            left: 0;
        }

        #sidebar.expand {
            width: 260px;
            min-width: 260px;
        }

        .toggle-btn {
            background-color: transparent;
            cursor: pointer;
            border: 0;
            padding: 1rem 1.5rem;
        }

        .toggle-btn i {
            font-size: 1.5rem;
            color: #FFF;
        }

        .sidebar-logo {
            margin: auto 0;
        }

        .sidebar-logo a {
            color: #FFF;
            font-size: 1.15rem;
            font-weight: 600;
        }

        #sidebar:not(.expand) .sidebar-logo,
        #sidebar:not(.expand) a.sidebar-link span {
            display: none;
        }

        .sidebar-nav {
            padding: 2rem 0;
            flex: 1 1 auto;
        }

        a.sidebar-link {
            padding: .625rem 1.5rem;
            color: #FFF;
            display: block;
            font-size: 0.9rem;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .sidebar-item,
        .sidebar-footer {
            position: relative;
        }

        .sidebar-link i {
            font-size: 1.2rem;
            color: white;
            margin-right: 10px;
        }

        a.sidebar-link:hover {
            background-color: rgba(255, 255, 255, .075);
            border-left: 3px solid #3b7ddd;
        }

        .sidebar-item {
            position: relative;
        }

        #sidebar:not(.expand) .sidebar-link span {
            display: none;
            position: absolute;
            left: 80px;
            top: 50%;
            transform: translateY(-50%);
            background: #0e2238;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.85rem;
            white-space: nowrap;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        #sidebar:not(.expand) .sidebar-item:hover .sidebar-link span,
        #sidebar:not(.expand) .sidebar-footer:hover .sidebar-link span {
            display: block;
        }

        .sidebar-item,
        .sidebar-footer {
            position: relative;
        }

        .sidebar-item.active a {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #3b7ddd;
            color: #3b7ddd;
        }

        .hover-shadow:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3) !important;
            transform: translateY(-5px);
            transition: all 0.3s ease-in-out;
            background-color: #0e2238 !important;
            color: white;
        }

        .table-container {
            height: 90vh;
            /* Adjust based on requirement */
            overflow: auto;
            /* Prevent content overflow */
        }

        .search-row th {
            text-align: center;
            background: #f8f9fa;
            padding: 5px;
        }

        .column-search {
            width: 100%;
            padding: 4px;
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
    <style>
        .highlight-hidden {
            background-color: #f8d7da;
            /* Light red background for hidden columns */
            color: #721c24;
            /* Dark red text color */
            font-weight: bold;
            /* Optional: To make it stand out more */
        }

        .dropdown-menu .dropdown-item {
            cursor: default;
        }

        th {
            font-size: 12px;
        }

        td {
            font-size: 10px;
        }
    </style>
</head>

<body class="bg-white">
    <div class="wrapper bg-white">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">MENU</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item active">
                    <a href="#.php" class="sidebar-link">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="newform.php" class="sidebar-link">
                        <i class="fa-regular fa-folder-open"></i>
                        <span>FPC ADDING</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="tables.php" class="sidebar-link">
                        <i class="fa-regular fa-address-card"></i>
                        <span>Tables</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="sidebarformat.php" class="sidebar-link">
                        <i class="fa-solid fa-gear"></i>
                        <span>Setting</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="sidebar-link">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h5 class="mb-3">Flexible Plastic Circuit Table</h5>
                    </div>
                    <div class="table-container table-responsive mt-3">
                        <div class="d-flex align-items-center gap-3">
                            <form method="GET" action="" class="row g-3" id="year-form">
                                <div class="col-auto">
                                    <label for="year" class="col-form-label">Select Year:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="year" id="year" class="form-select">
                                        <?php
                                        // Assuming you already have a database connection established in $conn
                                        $query = "SELECT DISTINCT YEAR(DATE) AS year FROM fpc ORDER BY year DESC";
                                        $result = mysqli_query($conn, $query);

                                        if ($result) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $year = $row['year'];
                                                echo "<option value='$year' " . ($year == $selectedYear ? 'selected' : '') . ">$year</option>";
                                            }
                                        } else {
                                            echo "<option disabled>No years available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                            </form>
                            <?php
                            $columns = [
                                'ID',
                                'FY',
                                'MONTH',
                                'DATE',
                                'CATEGORY',
                                'TRIGGER',
                                'NT_NF',
                                'ISSUE',
                                'PARTNUMBER',
                                'PRODUCT',
                                'LOT_SUBLOT',
                                'QTY-IN',
                                'QTY-OUT',
                                'REJECT'
                            ];
                            ?>

                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Hide Columns
                                </button>
                                <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($columns as $index => $column): ?>
                                        <li><a class="dropdown-item toggle-vis" data-column="<?php echo $index; ?>"><?php echo htmlspecialchars($column); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <table id="myTable" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead class="table-primary text-center">
                                <tr>
                                    <!-- Adjust column names based on the fields from your FPC table -->
                                    <th>ID</th>
                                    <th>FY</th>
                                    <th>MONTH</th>
                                    <th>DATE</th>
                                    <th>CATEGORY</th>
                                    <th>TRIGGER</th>
                                    <th>NT/NF</th>
                                    <th>ISSUE</th>
                                    <th>PART NO.</th>
                                    <th>PART NAME</th>
                                    <th>LOT/ SUBLOT</th>
                                    <th>IN</th>
                                    <th>OUT</th>
                                    <th>REJECT</th>
                                    <!--Add more columns as necessary -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Array of columns you want to include in the data-* attributes
                                $data_columns = ['ID', 'FY', 'MONTH', 'DATE', 'cat_name', 'trigger_name', 'NT_NF', 'ISSUE', 'PARTNUMBER', 'PRODUCT', 'LOT_SUBLOT', 'IN_VALUE', 'OUT_VALUE', 'REJECT'];

                                // Loop through each row of data and create table rows
                                foreach ($data as $row) {
                                    echo "<tr class='table-row' id='triggerElement' data-bs-toggle='modal' data-bs-target='#reservationModal' 
                                    data-id='" . htmlspecialchars($row['ID'] ?? '') . "'>";

                                    // Output table cells for each row
                                    foreach ($data_columns as $column) {
                                        echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
                                    }

                                    // Close the table row
                                    echo "</tr>";
                                }
                                if ($db_type == "access") {
                                    // Close the ODBC result
                                    odbc_free_result($result);
                                    odbc_close($conn);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body">
                    <div class="card-title">
                        <h5 class="mb-3">Informative Charts</h5>
                    </div>
                    <div class="table-container table-responsive mt-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record ID: <span id="recordId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="modalForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-label">FY</div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="fy" name="FY" required readonly>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-label">Month</div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="month" name="MONTH" required readonly>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-label">Date</div>
                                <input type="date" class="form-control" id="date" name="DATE" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-label">NT/NF</div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nt_nf" name="NT_NF" required readonly>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Category</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="category" name="CATEGORY" placeholder="Type at least 2 letters..." required>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Trigger</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="trigger" name="TRIGGER" placeholder="Type Trigger..." required>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Issue</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="issue" name="ISSUE" placeholder="Type Issue..." required>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Part No.</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="partNumber" name="PART_NO" placeholder="Type Part No..." required>
                                    <button class="btn btn-secondary dropdown-toggle" type="button"></button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Part Name</label>
                                <input type="text" class="form-control" id="partName" name="PRODUCT" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Lot/Sublot</label>
                                <input type="text" class="form-control" id="lotSublot" name="LOT_SUBLOT" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Qty-In</label>
                                <input type="number" class="form-control" id="inValue" name="IN" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Qty-Out</label>
                                <input type="number" class="form-control" id="outValue" name="OUT" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Reject</label>
                                <input type="number" class="form-control" id="reject" name="REJECT" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Minutes</label>
                                <input type="number" class="form-control" id="minutes" name="MINUTES" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="editButton" class="btn btn-primary">Edit</button>
                    <button id="deleteButton" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/bootstrap/js/all.min.js"></script>
    <script src="assets/vendor/bootstrap/js/fontawesome.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            $('#ncprTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        className: 'btn btn-primary'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-warning'
                    }
                ]
            });
        });
    </script>
    <script>
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Attach event listener to all rows in the table
            document.querySelectorAll(".table-row").forEach(row => {
                row.addEventListener("click", function() {
                    let recordId = this.getAttribute("data-id"); // Get the record ID

                    fetch("fetch_modaldata.php?id=" + recordId)
                        .then(response => response.json())
                        .then(data => {
                            console.log("Fetched Data:", data); // Debugging output
                            if (data.status === "success") {
                                // Populate modal fields
                                document.getElementById("recordId").textContent = data.data.ID;
                                document.getElementById("fy").value = data.data.FY;
                                document.getElementById("month").value = data.data.MONTH;
                                document.getElementById("date").value = data.data.DATE;
                                document.getElementById("nt_nf").value = data.data.NT_NF;
                                document.getElementById("category").value = data.data.cat_name;
                                document.getElementById("trigger").value = data.data.trigger_name;
                                document.getElementById("issue").value = data.data.ISSUE;
                                document.getElementById("partNumber").value = data.data.PARTNUMBER;
                                document.getElementById("partName").value = data.data.PARTNAME;
                                document.getElementById("lotSublot").value = data.data.LOT_SUBLOT;
                                document.getElementById("inValue").value = data.data.IN_VALUE;
                                document.getElementById("outValue").value = data.data.OUT_VALUE;
                                document.getElementById("reject").value = data.data.REJECT;
                                document.getElementById("minutes").value = data.data.MINUTES;

                                // Disable all input fields and hide dropdowns when viewing
                                document.querySelectorAll("#modalForm input, #modalForm select").forEach(input => {
                                    input.setAttribute("disabled", "true");
                                });

                                document.querySelectorAll("#modalForm .dropdown-toggle").forEach(btn => {
                                    btn.classList.add("d-none");
                                });

                                // Show the modal
                                let modal = new bootstrap.Modal(document.getElementById("reservationModal"));
                                modal.show();

                                // Show Edit button, hide Save button
                                document.getElementById("editButton").classList.remove("d-none");
                            } else {
                                // Show SweetAlert if record is not found
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: data.message,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => console.error("Error fetching data:", error));
                });
            });

            document.getElementById("editButton").addEventListener("click", function() {
                // Find the active row that was clicked and opened in the modal
                let activeRow = document.querySelector(".table-row.active");

                if (activeRow) {
                    let updateID = activeRow.getAttribute("data-id"); // Get the data-id from the row

                    if (updateID) {
                        window.location.href = `updateform.php?updateID=${updateID}`;
                    } else {
                        alert("Update ID not found!");
                    }
                } else {
                    alert("No row selected!");
                }
            });
            document.querySelectorAll(".table-row").forEach(row => {
                row.addEventListener("click", function() {
                    // Remove 'active' class from all rows
                    document.querySelectorAll(".table-row").forEach(r => r.classList.remove("active"));

                    // Add 'active' class to the clicked row
                    this.classList.add("active");
                });
            });
            const modalElement = document.getElementById("reservationModal");
            const modal = new bootstrap.Modal(modalElement);

            modalElement.addEventListener("hidden.bs.modal", function() {
                document.querySelectorAll(".modal-backdrop").forEach((backdrop) => {
                    backdrop.remove();
                });
                document.body.classList.remove("modal-open"); // Remove the modal-open class
            });
            const deleteButton = document.getElementById("deleteButton");

            deleteButton.addEventListener("click", async function() {
                const recordId = document.getElementById("recordId").textContent.trim(); // Get record ID

                if (!recordId) {
                    alert("Error: No record ID found.");
                    return;
                }

                if (confirm(`Are you sure you want to delete Record ID: ${recordId}?`)) {
                    try {
                        //Perform soft delete via API call
                        let response = await fetch("delete.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                recordId
                            })
                        });

                        let result = await response.json();
                        console.log("Server response:", result); // Log server response
                        if (!result.success) {
                            alert("Error: Unable to delete the record.");
                            return;
                        }

                        //Close the modal after successful deletion
                        const modalElement = document.getElementById("reservationModal");
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();

                        //Remove backdrop if necessary
                        setTimeout(() => {
                            document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());
                            document.body.classList.remove("modal-open");
                        }, 300);

                        alert("Record deleted successfully!");
                        location.reload(); // Refresh table to reflect changes

                    } catch (error) {
                        console.error("Error deleting record:", error);
                        alert("An error occurred while deleting the record.");
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var table = new DataTable('#myTable', {
                info: true,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                layout: {
                    topStart: 'info',
                    bottom: 'paging',
                    bottomStart: null,
                    bottomEnd: null
                },
                initComplete: function() {
                    let api = this.api();
                    let $thead = $('#myTable thead');

                    // Add a new row for search inputs
                    let $searchRow = $('<tr>').addClass('search-row');

                    api.columns().every(function(index) {
                        let column = this;
                        let title = $(column.header()).text().trim();

                        // Create input element
                        let input = $('<input>')
                            .attr('type', 'text')
                            .attr('placeholder', `Search ${title}`)
                            .addClass('column-search');

                        // Append input to new search row
                        let th = $('<th>').append(input);
                        $searchRow.append(th);

                        // Event listener for column search
                        input.on('keyup', function() {
                            column.search($(this).val()).draw();
                        });
                    });

                    // Insert search row inside the table head
                    $thead.prepend($searchRow);
                }
            });

            document.querySelectorAll('a.toggle-vis').forEach((el) => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();

                    let columnIdx = parseInt(e.target.getAttribute('data-column'));
                    let column = table.column(columnIdx);
                    let isVisible = !column.visible();
                    column.visible(isVisible);

                    // Hide/show the search input in the search row
                    let searchRowTh = document.querySelector(`.search-row th:nth-child(${columnIdx + 1})`);
                    if (searchRowTh) {
                        searchRowTh.style.display = isVisible ? '' : 'none';
                    }
                });
            });
            // Update the dropdown to highlight hidden columns
            table.on('column-visibility', function(e, settings, column, state) {
                var columnIndex = column;
                var dropdownItem = $('.dropdown-item[data-column="' + columnIndex + '"]');

                // If the column is hidden, add a class to the corresponding dropdown item
                if (!state) {
                    dropdownItem.addClass('highlight-hidden');
                } else {
                    dropdownItem.removeClass('highlight-hidden');
                }
            });
        });
    </script>
    <script>
        // JavaScript to trigger automatic form submission when a new year is selected
        document.getElementById("year").addEventListener("change", function() {
            // Submit the form when the user selects a new year
            document.getElementById("year-form").submit();
        });
    </script>
</body>

</html>