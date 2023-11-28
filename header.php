<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon 2.0</title>
    <style>
        body {
            font-family: Baskerville, serif;
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
    </style>
</head>
<body>

<header>
    <h1>Amazon 2.0</h1>
</header>

<nav>
    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Shop</a></li>
        <li><a href="#">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="/index.php">Amazon 2.0 Home</a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
               href="#" role="button" aria-expanded="false">Data Import</a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="/OrderReturnOrderitemImport.php">Import Orders and Returns</a>
                </li>
                <li>
                    <a class="dropdown-item" href="/DepartmentSellerItemImport.php">Import Sellers and Items and Departments</a>
                </li>
                <li>
                    <a class="dropdown-item" href="/UserListListitemImport.php">Import User and Lists</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<section>
    <div class="product">
        <img src="product1.jpg" alt="Product 1">
        <h2>Product 1</h2>
        <p>Description of Product 1. </p>
        <p>$19.99</p>
        <button>Add to Cart</button>
    </div>

    <div class="product">
        <img src="product2.jpg" alt="Product 2">
        <h2>Product 2</h2>
        <p>Description of Product 2. </p>
        <p>$29.99</p>
        <button>Add to Cart</button>
    </div>

    <!-- Add more product blocks as needed -->

</section>

</body>
</html>

<div class="container">