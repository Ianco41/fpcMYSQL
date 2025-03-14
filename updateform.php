<?php
include "conn.php";
// Get updateID from URL
$updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : 0;
$record = [];

if ($updateID > 0) {
    // Fetch record from the database
    $sql = "SELECT FPC.*, 
                     product_list.PARTNAME, PARTNUMBER, 
                     category_tbl.cat_name, 
                     trigger_tbl.trigger_name 
              FROM FPC 
              LEFT JOIN product_list ON FPC.PART_ID = product_list.ID 
              LEFT JOIN category_tbl ON FPC.CATEGORY_ID = category_tbl.ID 
              LEFT JOIN trigger_tbl ON FPC.TRIGGER_ID = trigger_tbl.ID 
              WHERE FPC.ID = $updateID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    }
}

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
    $updateID = $_POST['updateID'] ?? null; // Check for updateID

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
            $PART_ID = null;
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

            // If updateID is provided, update the record
            if ($updateID) {
                $sql = "UPDATE FPC 
                        SET FY = ?, MONTH = ?, DATE = ?, CATEGORY_ID = ?, TRIGGER_ID = ?, NT_NF = ?, ISSUE = ?, 
                            PART_ID = ?, PRODUCT = ?, LOT_SUBLOT = ?, IN_VALUE = ?, OUT_VALUE = ?, REJECT = ? 
                        WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("MySQL prepare failed: " . $conn->error);
                }

                $stmt->bind_param(
                    "ssssssssssiiii",
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
                    $REJECT,
                    $updateID
                );

                if (!$stmt->execute()) {
                    throw new Exception("Error updating record: " . $stmt->error);
                }
                $stmt->close();
            } else {
                // If no updateID, insert new record
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
        }

        // Commit transaction if everything is successful
        mysqli_commit($conn);
        // Redirect to a confirmation page or back to the form
        header("Location: indexnew.php"); // Change 'success.php' to your desired page
        exit(); // Ensure script stops execution after redirect

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback in case of error
        die("Transaction failed: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Entry</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <style>
        .card-body {
            width: 100%;
        }

        /* Hide the default spinner buttons on number input */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            /* For Firefox */
        }
    </style>
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
    </style>
    <style>
        .suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .suggestion-item {
            padding: 8px;
            cursor: pointer;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background: #f1f1f1;
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
                    <a href="#">LOGO</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item active">
                    <a href="indexnew.php" class="sidebar-link">
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
                        <h5 class="mb-3">FPC <?php echo $updateID > 0 ? "Update Entry ID : " . $updateID : "New Entry"; ?></h5>
                    </div>
                    <div class="table-container mt-3">
                        <form method="post" id="recordForm">
                            <div id="recordContainer">
                                <div class="record-entry">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select month-input" name="MONTH[]" required>
                                                    <option value="">Select Month</option>
                                                    <?php
                                                    foreach ($selectmonths as $month) {
                                                        $selected = ($record && $record['MONTH'] == $month) ? "selected" : "";
                                                        echo "<option value='$month' $selected>$month</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="form-label">Month</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-floating">
                                                <input type="date" class="form-control date-input" name="DATE[]" value="<?php echo $record ? $record['DATE'] : ''; ?>" required>
                                                <label class="form-label">Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select month-input" name="NT_NF[]" required>
                                                    <option value="">Select NT/NF</option>
                                                    <?php
                                                    foreach ($NT_NF as $option) {
                                                        $selected = ($record && $record['NT_NF'] == $option) ? "selected" : "";
                                                        echo "<option value='$option' $selected>$option</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label class="form-label">NT/NF</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Category</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control category-input" name="CATEGORY[]" placeholder="Type at least 2 letters..." value="<?php echo $record ? $record['cat_name'] : ''; ?>" required>
                                                <button class="btn btn-secondary dropdown-toggle dropdown-category" type="button"></button>
                                            </div>
                                            <div class="suggestions category-suggestions"></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Trigger</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control trigger-input" name="TRIGGER[]" placeholder="Type Trigger..." value="<?php echo $record ? $record['trigger_name'] : ''; ?>" required>
                                                <button class="btn btn-secondary dropdown-toggle dropdown-trigger" type="button"></button>
                                            </div>
                                            <div class="suggestions trigger-suggestions"></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Issue</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control issue-input" name="ISSUE[]" placeholder="Type Issue..." value="<?php echo $record ? $record['ISSUE'] : ''; ?>" required>
                                                <button class="btn btn-secondary dropdown-toggle dropdown-issues" type="button"></button>
                                            </div>
                                            <div class="suggestions issue-suggestions"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Part No</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control partnum-input" name="PART_NO[]" placeholder="Type Part No..." value="<?php echo $record ? $record['PARTNUMBER'] : ''; ?>" required>
                                                <button class="btn btn-secondary dropdown-toggle dropdown-partnum" type="button"></button>
                                            </div>
                                            <div class="suggestions partnum-suggestions"></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Product</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control product-name-input" name="PRODUCT[]" placeholder="Type Part No..." value="<?php echo $record ? $record['PARTNAME'] : ''; ?>" required>
                                                <button class="btn btn-secondary dropdown-toggle dropdown-product" type="button"></button>
                                            </div>
                                            <div class="suggestions product-suggestions"></div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Lot/Sublot</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control lot-sublot-input" name="LOT_SUBLOT[]" placeholder="Lot/Sublot" value="<?php echo $record ? $record['LOT_SUBLOT'] : ''; ?>" required>
                                                <button class="btn btn-secondary dropdown-toggle dropdown-lot" type="button"></button>
                                            </div>
                                            <div class="suggestions lot-suggestions"></div>
                                        </div>
                                        <div class="col-md mb-3">
                                            <label class="form-label">Qty-In</label>
                                            <input type="number" class="form-control in-input" name="IN[]" value="<?php echo $record ? $record['IN_VALUE'] : ''; ?>" required>
                                        </div>
                                        <div class="col-md mb-3">
                                            <label class="form-label">Qty-Out</label>
                                            <input type="number" class="form-control out-input" name="OUT[]" value="<?php echo $record ? $record['OUT_VALUE'] : ''; ?>" required>
                                        </div>
                                        <div class="col-md mb-3">
                                            <label class="form-label">Reject</label>
                                            <input type="number" class="form-control reject-input" name="REJECT[]" value="<?php echo $record ? $record['REJECT'] : ''; ?>" required>
                                        </div>
                                        <div class="col-md mb-3">
                                            <label class="form-label">Minutes.</label>
                                            <input type="number" class="form-control reject-input" name="REJECT[]" value="<?php echo $record ? $record['MINUTES'] : ''; ?>" required>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2" style=" margin: 0 auto;">
                                <!-- <button type="button" class="btn btn-info" id="addMore">Add More</button> -->
                                <input type="hidden" name="updateID" value="<?php echo $_GET['updateID'] ?? ''; ?>">
                                <button type="submit" class="btn btn-success"><?php echo $updateID > 0 ? "Update" : "Submit"; ?></button>
                                <!-- <a href="index.php" class="btn btn-danger" role="button">Cancel</a> -->
                            </div>
                        </form>

                    </div>
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
            document.querySelectorAll(".in-input, .out-input").forEach(input => {
                input.addEventListener("input", function() {
                    let row = this.closest(".row"); // Get the parent row
                    let qtyIn = parseInt(row.querySelector(".in-input").value) || 0;
                    let qtyOutInput = row.querySelector(".out-input");
                    let qtyOut = parseInt(qtyOutInput.value);
                    let rejectInput = row.querySelector(".reject-input");

                    // If QTY OUT has a value, calculate Reject. Otherwise, keep it empty.
                    if (!isNaN(qtyOut)) {
                        rejectInput.value = Math.max(qtyIn - qtyOut, 0); // Prevent negative values
                    } else {
                        rejectInput.value = ""; // Keep it empty
                    }
                });
            });
            document.getElementById("recordForm").addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent actual form submission

                // Show a SweetAlert confirmation message
                Swal.fire({
                    title: "Success!",
                    text: "Your entry has been submitted successfully.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    this.submit(); // Submit the form after confirmation
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            class SuggestionDropdown {
                constructor(input, dropdownButton, suggestionBox, fetchUrl) {
                    this.input = input;
                    this.dropdownButton = dropdownButton;
                    this.suggestionBox = suggestionBox;
                    this.fetchUrl = fetchUrl;
                    this.suggestions = [];
                    this.activeIndex = -1;
                    this.isDropdownOpen = false;

                    this.init();
                }

                async fetchSuggestions() {
                    try {
                        const response = await fetch(this.fetchUrl);
                        this.suggestions = await response.json();
                    } catch (error) {
                        console.error("Error fetching suggestions:", error);
                    }
                }

                handleSelection(item) {
                    this.input.value = item.textContent;
                    this.suggestionBox.style.display = "none";
                    this.isDropdownOpen = false;

                    // If extended by PartNumberDropdown, fetch additional data
                    if (this.fetchPartName) {
                        this.fetchPartName(item.textContent);
                    }
                    // Move to the next input field if available
                    let formElements = Array.from(document.querySelectorAll("input, select, textarea"));
                    let currentIndex = formElements.indexOf(this.input);

                    if (currentIndex >= 0 && currentIndex < formElements.length - 1) {
                        formElements[currentIndex + 1].focus();
                    }

                    // Reset activeIndex
                    this.activeIndex = -1;
                }

                showSuggestions(filteredList) {
                    this.suggestionBox.innerHTML = "";
                    if (filteredList.length > 0) {
                        this.suggestionBox.style.display = "block";
                        filteredList.forEach((item, index) => {
                            let div = document.createElement("div");
                            div.classList.add("suggestion-item");
                            div.textContent = item;
                            div.setAttribute("data-index", index);
                            // Handle click selection
                            div.addEventListener("click", () => this.handleSelection(div));
                            this.suggestionBox.appendChild(div);
                        });
                        // Add event listener to keep the dropdown open when interacting with it
                        this.suggestionBox.addEventListener("mousedown", (event) => {
                            event.preventDefault(); // Prevent input blur when clicking suggestions
                        });

                        // Add event listener to hide dropdown when input loses focus
                        this.input.addEventListener("blur", () => {
                            setTimeout(() => {
                                this.suggestionBox.style.display = "none";
                            }, 200); // Delay to allow click events to register
                        });
                    } else {
                        this.suggestionBox.style.display = "none";
                    }
                }

                updateActiveItem() {
                    let items = this.suggestionBox.querySelectorAll(".suggestion-item");

                    // Remove 'active' class from all items
                    items.forEach(item => item.classList.remove("active"));

                    if (this.activeIndex >= 0 && this.activeIndex < items.length) {
                        let activeItem = items[this.activeIndex];
                        activeItem.classList.add("active");

                        // Ensure the active item is visible
                        activeItem.scrollIntoView({
                            block: "nearest",
                            behavior: "smooth"
                        });
                    }
                }

                addEventListeners() {
                    this.input.addEventListener("keydown", (event) => {
                        let items = this.suggestionBox.querySelectorAll(".suggestion-item");

                        if (event.key === "ArrowDown" && items.length > 0) {
                            event.preventDefault();
                            this.activeIndex = Math.min(this.activeIndex + 1, items.length - 1); // Stop at last item
                            this.updateActiveItem();
                        } else if (event.key === "ArrowUp" && items.length > 0) {
                            event.preventDefault();
                            this.activeIndex = Math.max(this.activeIndex - 1, 0); // Stop at first item
                            this.updateActiveItem();
                        } else if (event.key === "Enter") {
                            event.preventDefault();

                            if (this.activeIndex >= 0) {
                                // Select the highlighted suggestion
                                this.handleSelection(items[this.activeIndex]);
                            } else {
                                // Move to the next input if no selection
                                let formElements = Array.from(document.querySelectorAll("input, select, textarea"));
                                let currentIndex = formElements.indexOf(this.input);

                                if (currentIndex >= 0 && currentIndex < formElements.length - 1) {
                                    formElements[currentIndex + 1].focus();
                                }
                            }
                        }
                    });

                    this.input.addEventListener("keyup", (event) => {
                        let filter = this.input.value.toLowerCase();

                        // Only reset activeIndex when typing letters/numbers
                        if (!["ArrowUp", "ArrowDown"].includes(event.key)) {
                            this.activeIndex = -1;
                        }

                        if (filter.length >= 2) {
                            let filtered = this.suggestions.filter(item => item.toLowerCase().includes(filter));
                            this.showSuggestions(filtered);
                        } else {
                            this.suggestionBox.style.display = "none";
                        }
                    });

                    this.dropdownButton.addEventListener("click", () => {
                        if (this.isDropdownOpen) {
                            this.suggestionBox.style.display = "none";
                            this.isDropdownOpen = false;
                        } else {
                            this.activeIndex = -1;
                            this.showSuggestions(this.suggestions);
                            this.isDropdownOpen = true;
                        }
                    });

                    document.addEventListener("click", (e) => {
                        if (!this.input.contains(e.target) && !this.suggestionBox.contains(e.target) && !this.dropdownButton.contains(e.target)) {
                            this.suggestionBox.style.display = "none";
                            this.isDropdownOpen = false;
                        }
                    });
                }

                async init() {
                    await this.fetchSuggestions();
                    this.addEventListeners();
                }
            }

            class PartNumberDropdown extends SuggestionDropdown {
                constructor(input, dropdownButton, suggestionBox, fetchUrl, productNameInput, partNameFetchUrl) {
                    super(input, dropdownButton, suggestionBox, fetchUrl);
                    this.productNameInput = productNameInput;
                    this.partNameFetchUrl = partNameFetchUrl;
                    this.suggestions = []; // Store suggestions

                    // Listen for input changes
                    this.input.addEventListener("input", () => {
                        if (!this.suggestions.includes(this.input.value.trim())) {
                            this.productNameInput.value = ""; // Clear product name if input is not from suggestions
                        }
                    });
                }

                async fetchPartName(partNumber) {
                    try {
                        const response = await fetch(`${this.partNameFetchUrl}?part_no=${encodeURIComponent(partNumber)}`);
                        const data = await response.json();

                        if (data.success && data.PARTNAME) {
                            this.productNameInput.value = data.PARTNAME;
                        } else {
                            this.productNameInput.value = "";
                        }
                    } catch (error) {
                        console.error("Error fetching part name:", error);
                    }
                }

                showSuggestions(filteredList) {
                    this.suggestionBox.innerHTML = "";
                    if (filteredList.length > 0) {
                        this.suggestionBox.style.display = "block";
                        filteredList.forEach((item, index) => {
                            let div = document.createElement("div");
                            div.classList.add("suggestion-item");
                            div.textContent = item;
                            div.setAttribute("data-index", index);
                            div.onclick = () => {
                                this.input.value = item;
                                this.suggestionBox.style.display = "none";
                                this.isDropdownOpen = false;
                                this.fetchPartName(item);
                            };
                            this.suggestionBox.appendChild(div);
                        });
                    } else {
                        this.suggestionBox.style.display = "none";
                    }
                }
            }

            function initializeDropdowns(container) {
                new SuggestionDropdown(
                    container.querySelector(".category-input"),
                    container.querySelector(".dropdown-category"),
                    container.querySelector(".category-suggestions"),
                    "fetch_suggestions.php"
                );
                new SuggestionDropdown(
                    container.querySelector(".trigger-input"),
                    container.querySelector(".dropdown-trigger"),
                    container.querySelector(".trigger-suggestions"),
                    "fetch_triggers.php"
                );
                new SuggestionDropdown(
                    container.querySelector(".issue-input"),
                    container.querySelector(".dropdown-issues"),
                    container.querySelector(".issue-suggestions"),
                    "fetch_issues.php"
                );

                new PartNumberDropdown(
                    container.querySelector(".partnum-input"),
                    container.querySelector(".dropdown-partnum"),
                    container.querySelector(".partnum-suggestions"),
                    "fetch_partnum.php",
                    container.querySelector(".product-name-input"),
                    "fetch_partname.php"
                );
            }

            // Remove the event listener related to "add more" and cloning
            // Initialize the existing records only
            document.querySelectorAll(".record-entry").forEach(initializeDropdowns);
        });
    </script>

    <script>
        $(document).ready(function() {
            function adjustWidth() {
                $(".suggestions").width($(".input-group").outerWidth());
            }

            adjustWidth();
            $(window).resize(adjustWidth);
        });
        document.addEventListener("keydown", (event) => {
            if (event.key === "Tab") {
                let activeElement = document.activeElement;

                // Check if the active element is an input, select, or textarea
                if (["INPUT", "SELECT", "TEXTAREA"].includes(activeElement.tagName)) {
                    event.preventDefault(); // Prevent default form submission

                    // Find the closest form section (.record-entry) to scope the search
                    let formSection = activeElement.closest(".record-entry");

                    if (formSection) {
                        // Get all visible and enabled input/select/textarea elements within the same form section
                        let formElements = Array.from(formSection.querySelectorAll("input, select, textarea"))
                            .filter(el => el.offsetParent !== null && !el.disabled); // Only focusable elements

                        let currentIndex = formElements.indexOf(activeElement);

                        if (currentIndex >= 0 && currentIndex < formElements.length - 1) {
                            formElements[currentIndex + 1].focus(); // Move to the correct next input field
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>