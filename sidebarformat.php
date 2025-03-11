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
                <li class="sidebar-item">
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
                <li class="sidebar-item active">
                    <a href="#" class="sidebar-link">
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
                                                <h5>NCPR Files</h5>
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
                                                <h5>Open Files</h5>
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
                                                <h5>Product Key</h5>
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
                                                <h5>Engineer List</h5>
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
                        <h5 class="mb-3">NCPR Table</h5>
                    </div>
                    <div class="table-container table-responsive mt-3">
                        <table id="ncprTable" class="table table-bordered table-hover" style="width:100%">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Project A</td>
                                    <td>Open</td>
                                    <td>2024-03-01</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Project B</td>
                                    <td>Closed</td>
                                    <td>2024-02-28</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Project C</td>
                                    <td>In Progress</td>
                                    <td>2024-02-27</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Project D</td>
                                    <td>Open</td>
                                    <td>2024-02-26</td>
                                </tr>
                            </tbody>
                        </table>
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
        });
    </script>
</body>

</html>