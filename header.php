<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon 2.0</title>
    <style>
        body {
            font-family: Baskerville, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #D2B48C;
            color: #ffffff;
            padding: 10px;
            text-align: center;
        }

        nav {
            background-color: #664229;
            color: #ffffff;
            padding: 10px;
            text-align: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 20px;
        }

        section {
            padding: 20px;
        }

        .product {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }

        a {
            color: #ab9797;
        }
        a:hover {
            color: #cfc8c8;
        }
    </style>
</head>
<body>

<header>
    <h1>Amazon 2.0</h1>
</header>

<nav>
    <ul>
        <li class="nav-item"><a class="nav-link" href="index.php">Amazon 2.0 Home</a></li>
        <li class="nav-item dropdown">
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="OrderReturnOrderitemImport.php">Orders and Returns Import</a>
                </li>
                <li>
                    <a class="dropdown-item" href="DepartmentSellerItemImport.php">Sellers and Items and Departments Import</a>
                </li>
                <li>
                    <a class="dropdown-item" href="UserListListitemImport.php">User and Lists Import</a>
                </li>
            </ul>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="OrderReturnOrderitemReport.php">Orders and Returns Report</a>
                </li>
                <li>
                    <a class="dropdown-item" href="DepartmentSellerItemReport.php">Sellers and Items and Departments Report</a>
                </li>
                <li>
                    <a class="dropdown-item" href="UserListListitemReport.php">User and Lists Report</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<div class="container">