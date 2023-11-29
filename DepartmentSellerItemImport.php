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
            $lastDept = null;
            $lastSeller = null;
            for ($i = 1; $i < count($lines); $i++) {
                $line = $lines[$i];
                $parsedLine = str_getcsv($line);
                if (count($parsedLine) == 0) continue; // skip blank lines
                $assoc = array_combine($header, $parsedLine);
                // Import departments
                if ($assoc["DeptName"] != $lastDept) {
                    $stmt = mysqli_prepare($con, "INSERT INTO department (DeptName) VALUES (?) ON DUPLICATE KEY UPDATE DeptName = ?");
                    mysqli_stmt_bind_param($stmt, "ss", $assoc["DeptName"], $assoc["DeptName"]);
                    mysqli_stmt_execute($stmt);
                    $lastDeptId = $con->insert_id;
                    if ($lastDeptId == 0) {
                        $word = addslashes($assoc["DeptName"]);
                        $result = mysqli_query($con, "SELECT DepartmentID FROM department WHERE DeptName = '{$word}'");
                        $result = mysqli_fetch_assoc($result);
                        $lastDeptId = $result["DepartmentID"];
                    }
                    $lastDept = $assoc["DeptName"];
                }
                // Import Sellers
                if ($assoc["SellerName"] != $lastSeller) {
                    $stmt = mysqli_prepare($con, "INSERT INTO seller (SellerName, DepartmentID, PhoneNumber,EmailAddress) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE DepartmentID = ?, PhoneNumber = ?, EmailAddress = ?");
                    mysqli_stmt_bind_param($stmt, "siisiis", $assoc["SellerName"], $lastDeptId, $assoc["PhoneNumber"], $assoc["EmailAddress"], $lastDeptId, $assoc["PhoneNumber"], $assoc["EmailAddress"]);
                    mysqli_stmt_execute($stmt);
                    $lastSellerId = $con->insert_id;
                    if ($lastSellerId == 0) {
                        $word = addslashes($assoc["SellerName"]);
                        $result = mysqli_query($con, "SELECT SellerID FROM seller WHERE SellerName = '{$word}'");
                        $result = mysqli_fetch_assoc($result);
                        $lastSellerId = $result["SellerID"];
                    }
                    $lastSeller = $assoc["SellerName"];
                }
                // Import each item
                $stmt = mysqli_prepare($con, "INSERT INTO item (SKU, ItemName, ItemType, SellerID, Price, QuantityAvailable) VALUES (?,?,?,?,?,?)" .
                                                    " ON DUPLICATE KEY UPDATE Price = ?, QuantityAvailable = ?");
                mysqli_stmt_bind_param($stmt, "issididi",
                    $assoc["SKU"], $assoc["ItemName"], $assoc["ItemType"], $lastSellerId, $assoc["Price"], $assoc["QuantityAvailable"],
                        $assoc["Price"], $assoc["QuantityAvailable"]);
                mysqli_stmt_execute($stmt);

                if ($lastSeller == null)
                    $lastSeller = $assoc["SellerName"];
                if ($lastDept == null)
                    $lastDept = $assoc["DeptName"];
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
    <h1>Amazon 2.0 Data Import</h1>
    <h3>Departments, Sellers, and Items</h3>
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
