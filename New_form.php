<?php
include "conn.php";

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
    if (empty($DATES) || empty($CATEGORIES)) {
        die("Error: Missing required data.");
    }

    // Database Insertion Loop for Multiple Records
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

        if ($db_type == "access") {
            // Use MS Access Syntax (square brackets for reserved words)
            $sql = "INSERT INTO FPC (FY, MONTH, [DATE], CATEGORY, [TRIGGER], NT_NF, ISSUE, [PART NO], PRODUCT, [LOT/SUBLOT], [IN], [OUT], REJECT) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = odbc_prepare($conn, $sql);
            $result = odbc_execute($stmt, [$FY, $MONTH_VAL, $DATE, $CATEGORY, $TRIGGER, $NT_NF, $ISSUE, $PART_NO, $PRODUCT, $LOT_SUBLOT, $IN, $OUT, $REJECT]);

            if (!$result) {
                die("Error inserting record into Access: " . odbc_errormsg($conn));
            }
        } else {
            // Use MySQL Syntax with backticks for reserved words
            $sql = "INSERT INTO FPC (FY, MONTH, DATE, CATEGORY, `TRIGGER`, NT_NF, ISSUE, PART_NO, PRODUCT, LOT_SUBLOT, IN_VALUE, OUT_VALUE, REJECT) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                die("MySQL prepare failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssiii",
                $FY,
                $MONTH_VAL,
                $DATE,
                $CATEGORY,
                $TRIGGER,
                $NT_NF,
                $ISSUE,
                $PART_NO,
                $PRODUCT,
                $LOT_SUBLOT,
                $IN,
                $OUT,
                $REJECT
            );

            $result = mysqli_stmt_execute($stmt);

            if (!$result) {
                die("Error inserting record into MySQL: " . mysqli_error($conn));
            }

            mysqli_stmt_close($stmt);
        }
    }

    // Close database connection
    if ($db_type == "access") {
        odbc_close($conn);
    } else {
        mysqli_close($conn);
    }

    // Redirect after success
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Record</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        .suggestion-box {
            border: 1px solid #ccc;
            max-width: 200px;
            background: #fff;
            position: absolute;
            z-index: 1000;
            display: none;
            max-height: 150px;
            /* Approx 5 items (each ~30px height) */
            overflow-y: auto;
            /* Enables scrolling if more than 5 items */
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .suggestion-box div {
            padding: 8px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            /* Adds subtle separator */
        }

        .suggestion-box div:last-child {
            border-bottom: none;
            /* Remove border for last item */
        }

        .suggestion-box div:hover {
            background: #f8f9fa;
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

        @media (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 10px;
            }

            .card {
                max-width: 95%;
                margin: 0 auto;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .card-header h2 {
                font-size: 1.5rem;
            }

            .form-floating label {
                font-size: 14px;
            }

            .form-control,
            .form-select {
                font-size: 14px;
                padding: 10px;
            }

            .row {
                flex-direction: column;
            }

            .col-md-6,
            .col-md-4 {
                width: 100%;
            }

            .d-grid {
                max-width: 100%;
            }

            button,
            a.btn {
                font-size: 16px;
                padding: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 ">
        <div class="card" style="max-width: 80%; margin: 0 auto;">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">Add New Record</h2>
                </div>

                <div class="card-body">
                    <?php if (!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>

                    <form method="post" id="recordForm">
                        <div id="recordContainer">
                            <div class="record-entry">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <!--<label for="MONTH" class="form-label">Month</label>-->
                                            <select class="form-select" id="MONTH" name="MONTH[]" required>
                                                <option value="">Select Month</option>
                                                <?php foreach ($selectmonths as $month) echo "<option value='$month'>$month</option>"; ?>
                                            </select>
                                            <label for="MONTH" class="form-label">Month</label>
                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="DATE" name="DATE[]" value="<?php echo date('Y-m-d'); ?>" readonly>
                                            <label for="DATE" class="form-label">Date</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <!---->
                                            <input type="text" class="form-control" id="category_input" name="CATEGORY[]" placeholder="Type Category..."
                                                data-suggestions="category_suggestions"
                                                onkeyup="fetchSuggestions(event, 'fetch_suggestion.php')" required>
                                            <div id="category_suggestions" class="suggestion-box"></div>
                                            <label for="CATEGORY" class="form-label">Category</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3 position-relative">
                                        <div class="form-floating">
                                            <!--<label for="TRIGGER" class="form-label">Trigger</label>-->
                                            <input type="text" class="form-control" id="triggers_input" name="TRIGGER[]" placeholder="Type Trigger..."
                                                data-suggestions="trigger_suggestions"
                                                onkeyup="fetchSuggestions(event, 'GET_trigger.php')" required>
                                            <div id="trigger_suggestions" class="suggestion-box"></div>
                                            <label for="TRIGGER" class="form-label">Trigger</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <!--<label for="NT_NF" class="form-label">NT/NF</label>-->
                                            <select class="form-select" id="NT_NF" name="NT_NF[]" required>
                                                <option value="">Select NT/NF</option>
                                                <?php foreach ($NT_NF as $value) echo "<option value='$value'>$value</option>"; ?>
                                            </select>
                                            <label for="NT_NF" class="form-label">NT/NF</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3 position-relative">
                                        <div class="form-floating">
                                            <!---->
                                            <input type="text" class="form-control" id="issues_input" name="ISSUE[]" placeholder="Type Issue..."
                                                data-suggestions="issues_suggestions"
                                                onkeyup="fetchSuggestions(event, 'GET_issue.php')" required>
                                            <div id="issues_suggestions" class="suggestion-box"></div>
                                            <label for="ISSUE" class="form-label">Issue</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 position-relative">
                                        <div class="form-floating">
                                            <!--<label for="PART_NO" class="form-label">Part No</label>-->
                                            <input type="text" class="form-control" id="parnum_input" name="PART_NO[]" placeholder="Type Part No..."
                                                onkeyup="fetchSuggestions_parnum()" required>
                                            <div id="suggestions" class="suggestion-box"></div>
                                            <label for="PART_NO" class="form-label">Part No</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="product_name" name="PRODUCT[]" placeholder="Part Name" required>
                                            <label for="PRODUCT" class="form-label">Product</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="LOT_SUBLOT" name="LOT_SUBLOT[]" placeholder="Part Name" required>
                                        <label for="LOT_SUBLOT" class="form-label">Lot/Sublot</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="IN" class="form-label">IN</label>
                                        <input type="number" class="form-control" id="IN" name="IN[]" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="OUT" class="form-label">OUT</label>
                                        <input type="number" class="form-control" id="OUT" name="OUT[]" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="REJECT" class="form-label">Reject</label>
                                        <input type="number" class="form-control" id="REJECT" name="REJECT[]" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-record">Remove</button>
                                <hr>
                            </div>
                        </div>

                        <div class="d-grid gap-2" style="max-width: 60%; margin: 0 auto;">
                            <button type="button" class="btn btn-info" id="addMore">Add More</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <a href="index.php" class="btn btn-danger" role="button">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById("addMore").addEventListener("click", function() {
            let container = document.getElementById("recordContainer");
            let firstEntry = container.firstElementChild;
            let newEntry = firstEntry.cloneNode(true);

            // Maintain DATE from the first entry
            let currentDate = firstEntry.querySelector('input[name="DATE[]"]').value;
            newEntry.querySelector('input[name="DATE[]"]').value = currentDate;

            // Generate unique IDs for new input fields and create matching suggestion boxes
            newEntry.querySelectorAll("input, select").forEach(function(input, index) {
                if (input.type !== "hidden" && input.name !== "DATE[]") {
                    input.value = "";

                    // Assign a unique ID
                    let uniqueId = input.name.replace("[]", "") + "_" + Date.now();
                    input.id = uniqueId;

                    // Find or create a suggestion box
                    let suggestionBox = document.createElement("div");
                    suggestionBox.classList.add("suggestion-box");
                    suggestionBox.id = uniqueId + "_suggestions";
                    input.setAttribute("data-suggestions", suggestionBox.id);

                    // Append suggestion box after the input field
                    input.parentNode.appendChild(suggestionBox);
                }
            });

            // Append the new entry
            container.appendChild(newEntry);

            // Re-attach event listeners for suggestions
            newEntry.querySelector("input[name='CATEGORY[]']").addEventListener("keyup", function(event) {
                fetchSuggestions(event, 'fetch_suggestion.php');
            });

            newEntry.querySelector("input[name='TRIGGER[]']").addEventListener("keyup", function(event) {
                fetchSuggestions(event, 'GET_trigger.php');
            });

            newEntry.querySelector("input[name='ISSUE[]']").addEventListener("keyup", function(event) {
                fetchSuggestions(event, 'GET_issue.php');
            });

            newEntry.querySelector("input[name='PART_NO[]']").addEventListener("keyup", function() {
                fetchSuggestions_parnum();
            });

            // Attach event listener for remove button
            newEntry.querySelector(".remove-record").addEventListener("click", function() {
                newEntry.remove();
            });
        });
    </script>
    <script>
        // Function to hide the suggestion box when another input is clicked
        document.addEventListener("focusin", function(event) {
            let suggestionBoxes = document.querySelectorAll('.suggestion-box');

            // Hide all suggestion boxes when any input field is focused (clicked)
            suggestionBoxes.forEach(box => {
                box.style.display = "none";
            });
        });

        function fetchSuggestions(event, fetchUrl) {
            let inputField = event.target;
            let suggestionsBox = document.getElementById(inputField.getAttribute("data-suggestions"));

            if (!suggestionsBox) {
                console.error("Error: Suggestions box not found for", inputField.id);
                return;
            }

            let inputValue = inputField.value.trim();

            if (inputValue.length === 0) {
                suggestionsBox.innerHTML = "";
                suggestionsBox.style.display = "none";
                return;
            }

            let xhr = new XMLHttpRequest();
            let url = fetchUrl + "?q=" + encodeURIComponent(inputValue);

            console.log("Sending request to:", url);

            xhr.open("GET", url, true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log("Response received:", xhr.status, xhr.responseText);

                    if (xhr.status === 200) {
                        let suggestions = xhr.responseText.trim().split("\n"); // Convert response into an array
                        let uniqueSuggestions = new Set(suggestions); // Remove duplicates

                        // Limit suggestions to 5 items
                        let limitedSuggestions = Array.from(uniqueSuggestions).slice(0, 5);

                        // Build the suggestion box content
                        let html = "";
                        limitedSuggestions.forEach(value => {
                            if (value.trim() !== "") {
                                html += `<div onclick="selectCategory('${value}', '${inputField.id}', '${suggestionsBox.id}')">${value}</div>`;
                            }
                        });

                        suggestionsBox.innerHTML = html;

                        // Show/hide suggestion box based on content
                        suggestionsBox.style.display = html === "" ? "none" : "block";
                    } else {
                        console.error("Error fetching suggestions.");
                    }
                }
            };

            xhr.send();
        }

        function selectCategory(value, inputId, suggestionsId) {
            let inputField = document.getElementById(inputId);
            let suggestionsBox = document.getElementById(suggestionsId);

            if (inputField && suggestionsBox) {
                inputField.value = value;
                suggestionsBox.innerHTML = ""; // Clear suggestions
                suggestionsBox.style.display = "none"; // Hide box
            } else {
                console.error("Error: Input field or suggestions box not found!");
            }
        }
    </script>


    <!--get partnumber then auto populate the partname -->
    <script>
        function fetchSuggestions_parnum() {
            let input = document.getElementById('parnum_input').value;
            let suggestionsBox = document.getElementById('suggestions');

            if (!suggestionsBox) {
                console.error("Error: Suggestions box not found!");
                return;
            }

            if (input.length === 0) {
                suggestionsBox.innerHTML = "";
                suggestionsBox.style.display = "none";
                return;
            }

            let xhr = new XMLHttpRequest();
            let url = "GET_partnum.php?q=" + encodeURIComponent(input);

            console.log("Sending request to:", url);

            xhr.open("GET", url, true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log("Response received:", xhr.status, xhr.responseText);

                    if (xhr.status === 200) {
                        let suggestions = xhr.responseText.trim().split("\n"); // Convert response into an array
                        let uniqueSuggestions = new Set(suggestions); // Remove duplicates

                        // Limit to 5 items
                        let limitedSuggestions = Array.from(uniqueSuggestions).slice(0, 5);

                        let html = "";

                        limitedSuggestions.forEach(item => {
                            if (item.trim() !== "") {
                                let [id, partNumber] = item.split("|"); // Extract ID and Part No
                                html += `<div onclick="select_parnum('${id}', '${partNumber}')">${partNumber}</div>`;
                            }
                        });

                        suggestionsBox.innerHTML = html;
                        suggestionsBox.style.display = html === "" ? "none" : "block";
                    } else {
                        console.error("Error fetching suggestions.");
                    }
                }
            };

            xhr.send();
        }

        function select_parnum(id, value) {
            let inputField = document.getElementById('parnum_input');
            let suggestionsBox = document.getElementById('suggestions');

            if (inputField && suggestionsBox) {
                inputField.value = value;
                suggestionsBox.innerHTML = ""; // Clear suggestions
                suggestionsBox.style.display = "none"; // Hide box

                // Fetch the corresponding product name based on ID
                fetchProductName(id);
            } else {
                console.error("Error: Input field or suggestions box not found!");
            }
        }

        function fetchProductName(partId) {
            let xhr = new XMLHttpRequest();
            let url = "GET_partname.php?id=" + encodeURIComponent(partId);

            xhr.open("GET", url, true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("product_name").value = xhr.responseText.trim();
                }
            };

            xhr.send();
        }
    </script>

</body>

</html>