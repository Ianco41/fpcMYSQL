<?php
include "conn.php";

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y'); // Get year from filter or use current year

if ($db_type == "access") {
    // Query for MS Access (using ODBC)
    $query = "SELECT * FROM FPC WHERE YEAR(date) = $year ORDER BY ID DESC";
    $result = odbc_exec($conn, $query);

    if (!$result) {
        die("Query failed: " . odbc_errormsg());
    }

    // Fetch data from ODBC
    $data = [];
    while ($row = odbc_fetch_array($result)) {
        $data[] = $row;
    }
} else {
    // Query for MySQL
    $query = "SELECT * FROM FPC WHERE YEAR(date) = $year ORDER BY ID DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch data from MySQL
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        }

        #sidebar {
            width: 70px;
            min-width: 70px;
            z-index: 1000;
            transition: all .25s ease-in-out;
            background-color: #0e2238;
            display: flex;
            flex-direction: column;
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
            height: 60vh;
            /* Adjust based on requirement */
            overflow: auto;
            /* Prevent content overflow */
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
                    <a href="#" class="sidebar-link">
                        <i class="fa-regular fa-address-card"></i>
                        <span>NCPR List</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-helmet-safety"></i>
                        <span>Product Key</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-paperclip"></i>
                        <span>Engineer List</span>
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
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <a href="ncprlist.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>FPC </h5>
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/folder.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="ncprlist.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>Total Ins</h5>
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/open.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="productkey.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>Total Outs</h5>
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/close.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="status.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>Total Rejects</h5>
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/eng.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

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
                                        // Display the last 10 years as options (from 2025 to 2015)
                                        for ($i = 2025; $i >= 2015; $i--) {
                                            echo "<option value='$i' " . ($i == $year ? 'selected' : '') . ">$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="toggleDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Show/Hide Columns
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="toggleDropdown" id="toggleButtons">
                                    <!-- Dynamic toggle buttons will be added here -->
                                </ul>
                            </div>
                        </div>
                        <table id="myTable" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <!-- Adjust column names based on the fields from your FPC table -->
                                    <th>ID</th>
                                    <th>FISCAL YR</th>
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
                                $data_columns = ['ID', 'FY', 'MONTH', 'DATE', 'CATEGORY', 'TRIGGER', 'NT_NF', 'ISSUE', 'PART_NO', 'PRODUCT', 'LOT_SUBLOT', 'IN_VALUE', 'OUT_VALUE', 'REJECT'];

                                // Loop through each row of data and create table rows
                                foreach ($data as $row) {
                                    echo "<tr class='table-row' id='triggerElement' data-bs-toggle='modal' data-bs-target='#reservationModal' 
                                    ";
                                    // Add data-* attributes dynamically
        foreach ($data_columns as $column) {
            echo " data-" . strtolower(str_replace('_', '-', $column)) . "='" . htmlspecialchars($row[$column] ?? '') . "'";
        }
        echo ">";

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

                                    // Close the connection
                                    odbc_close($conn);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Row Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be inserted here dynamically -->
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary edit-btn-modal">Edit</button>
                <button type="button" class="btn btn-danger delete-btn-modal">Delete</button>

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

            document.querySelectorAll(".table-row").forEach(row => {
        row.addEventListener("click", function() {
            const modalBody = document.querySelector("#reservationModal .modal-body");
            let modalContent = "<div class='container'><div class='row'>";

            // Extract all data-* attributes
            Array.from(this.attributes).forEach(attr => {
                if (attr.name.startsWith("data-")) {
                    const key = attr.name.replace("data-", "").replace("-", " ").toUpperCase();
                    const value = attr.value.trim() !== "" ? attr.value : "N/A"; // Handle empty values

                    modalContent += `
                        <div class='col-md-6 mb-2'>
                            <strong>${key}:</strong> ${value}
                        </div>
                    `;
                }
            });

            modalContent += "</div></div>";
            modalBody.innerHTML = modalContent;
        });
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
                }
            });

            var $thead = $('#myTable thead tr');
            var columnsToToggle = [];
            var hiddenColumns = ["FISCAL YR", "MONTH", "NT/NF", "LOT/ SUBLOT"]; // Columns to be hidden initially

            // Extract column names dynamically
            $thead.find('th').each(function(index) {
                if (index !== 0) { // Skip ID column
                    var colName = $(this).text().trim();
                    var isHidden = hiddenColumns.includes(colName);

                    columnsToToggle.push({
                        index: index,
                        name: colName,
                        hidden: isHidden
                    });

                    // Hide the column initially if it's in the hiddenColumns list
                    if (isHidden) {
                        table.column(index).visible(false);
                    }
                }
            });

            // Generate dropdown items dynamically
            columnsToToggle.forEach(function(col) {
                $('#toggleButtons').append(
                    `<li>
                        <a class="dropdown-item">
                            <input type="checkbox" class="toggle-column" data-column="${col.index}" ${col.hidden ? '' : 'checked'}> ${col.name}
                        </a>
                    </li>`
                );
            });

            // Toggle column visibility on checkbox change
            $(document).on('change', '.toggle-column', function() {
                var columnIdx = $(this).data('column');
                var column = table.column(columnIdx);
                column.visible(!column.visible());
            });
        })
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