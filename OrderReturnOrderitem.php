<?php
require 'config.php';
$importAttempted =  ($_SERVER["REQUEST_METHOD"] == "POST");
$importSucceeded = false;
$importErrorMesg = "";

if ($importAttempted) {
    $con = connect();
    if ($con[0]) {
        $con = $con[1];
        $contents = file_get_contents($_FILES["importFile"]['tmp_name']);
        $lines = explode("\n", $contents);
        $header = str_getcsv($lines[0]);
        $lastOrder = null;
        for ($i = 1; $i < count($lines); $i++) {
            $line = $lines[$i];
            $parsedLine = str_getcsv($line);
            if (count($parsedLine) == 0) continue; // skip blank lines
            $assoc = array_combine($header, $parsedLine);
            // Import Orders
            if ($assoc["OrderID"] != $lastOrder) {
                $stmt = mysqli_prepare($con, "INSERT INTO `order` (OrderID, UserID, TotalPrice, OrderStatus, CreditCardNumber, OrderDate) VALUES (?, ?, ?, ?, ?, ?)" .
                    " ON DUPLICATE KEY UPDATE TotalPrice = ?, OrderStatus = ?, CreditCardNumber = ?, OrderDate = ?");
                mysqli_stmt_bind_param($stmt, "iidsssdsss", $assoc["OrderID"], $assoc["UserID"], $assoc["TotalPrice"], $assoc["OrderStatus"], $assoc["CreditCardNumber"], $assoc["OrderDate"],
                    $assoc["TotalPrice"], $assoc["OrderStatus"], $assoc["CreditCardNumber"], $assoc["OrderDate"]);
                mysqli_stmt_execute($stmt);
                $lastOrderId = $assoc["OrderID"];
                $lastOrder = $assoc["OrderID"];
            }
            // Import OrderItems
            $stmt = mysqli_prepare($con, "INSERT INTO orderitem (OrderID, ItemID, ItemQuantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE ItemQuantity = ?");
            mysqli_stmt_bind_param($stmt, "iiii", $lastOrderId, $assoc["ItemID"], $assoc["OrderItemQuantity"], $assoc["OrderItemQuantity"]);
            mysqli_stmt_execute($stmt);
            // Import Returns
            if ($assoc["ReturnItemQuantity"] != null && $assoc["ReturnDate"] != null) {
                $stmt = mysqli_prepare($con, "INSERT INTO `return` (OrderID, ItemID, ItemQuantity, ReturnDate) VALUES (?, ?, ?, ?)" .
                    " ON DUPLICATE KEY UPDATE ItemQuantity = ?, ReturnDate = ?");
                mysqli_stmt_bind_param($stmt, "iiisis",
                    $lastOrderId, $assoc["ItemID"], $assoc["ReturnItemQuantity"], $assoc["ReturnDate"], $assoc["ReturnItemQuantity"], $assoc["ReturnDate"]);
                mysqli_stmt_execute($stmt);
            }

            if ($lastOrder == null)
                $lastOrder = $assoc["OrderID"];
        }
        $importSucceeded = true;
    }
    else {
        $importErrorMesg = $con[1];
        $importSucceeded = false;
    }
}
?>

<html>
<head>
    <title>Amazon 2.0 Data Import</title>
    <?php include_once 'bs.php'; ?>
</head>
<body>
<?php include_once 'header.php';?>
<h1>>Amazon 2.0 Data Import</h1>
<h3>Orders, Items on Orders, and Returns</h3>
<?php

if ($importAttempted) {
    if ($importSucceeded) {
        ?>
        <h1 style="color:green;">Import Succeeded</h1>
        <?php
    }
    else {

        ?>
        <span style="color: red;">
                            <h1>Import Failed</h1>
                            <?php echo $importErrorMesg; ?>
                        </span>
        <?php
        echo "<br><br>";
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <div class="input-group">

    </div>
    <span class="input-group-text">File:</span>
    <input class="input-form-control" type="file" name="importFile"/>
    <br><br>
    <button class="btn btn-primary" type="submit">Submit</button>
</form>
<?php include_once 'footer.php'; ?>
</body>
</html>
