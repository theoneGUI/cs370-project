<html>
<head>
    <?php include_once 'config.php';

    $con = @connect();
    if ($con[0]) {
        $con = $con[1];

    }
    else {
        $importErrorMesg = $con[1];
        $conError = true;
    }
    ?>
    <title>Amazon 2.0</title>
    <?php include_once 'bs.php'; ?>
</head>
<body>
<?php include_once 'header.php'; ?>
<div class="p-5 bg-dark">
    <h1 class="text-light">Amazon 2.0 Home</h1>
    <section>
        <?php
        $query = "SELECT t0.ItemName, t0.Price, t0.QuantityAvailable, t1.SellerName FROM" .
            " item t0 INNER JOIN seller t1 ON t0.SellerID = t1.SellerID".
            " ORDER BY t0.ItemType DESC LIMIT 3";
        $stmt = mysqli_query($con, $query);
        $ct = 0;
        while ($row = $stmt->fetch_assoc()) {
            $ct++;
        ?>
        <div class="product">
            <h2><?=$row["ItemName"]?></h2>
            <p>Sold by: <?=$row["SellerName"]?>. There are <?=$row["QuantityAvailable"]?> of these in stock.</p>
            <p>$<?=$row["Price"]?></p>
            <button class="btn">Add to Cart</button>
        </div>
        <?php }
            if ($ct == 0) {
                echo "There are no products to shop.";
            }
        ?>
        <!-- Add more product blocks as needed -->
    </section>
</div>
<?php include_once  'footer.php'; ?>
</body>
</html>