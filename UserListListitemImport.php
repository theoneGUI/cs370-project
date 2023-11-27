<?php
require 'config.php';
$importAttempted =  ($_SERVER["REQUEST_METHOD"] == "POST");
$importSucceeded = false;
$importErrorMesg = "";

if ($importAttempted) {
    $con = connect();
    if ($con[0]) {
        $con = $con[1];
        function getSellerId($sellerName) {
            global $con;
            $word = addslashes($sellerName);
            $result = mysqli_query($con, "SELECT SellerID FROM seller WHERE SellerName = '{$word}'");
            $result = mysqli_fetch_assoc($result);
            return $result["SellerID"];
        }
        function getItemId($itemName, $sellerId) {
            global $con;
            $word = addslashes($itemName);
            $result = mysqli_query($con, "SELECT ItemID FROM item WHERE ItemName = '{$word}' AND SellerID = {$sellerId}");
            $result = mysqli_fetch_assoc($result);
            return $result["ItemID"];
        }
        function getAcctStat($acctStatName) {
            global $con;
            $word = addslashes($acctStatName);
            $result = mysqli_query($con, "SELECT AccountStatusID FROM accountstatus WHERE AccountStatusName = '{$word}'");
            $result = mysqli_fetch_assoc($result);
            return $result["AccountStatusID"];
        }
        $contents = file_get_contents($_FILES["importFile"]['tmp_name']);
        $lines = explode("\n", $contents);
        $header = str_getcsv($lines[0]);
        $lastUser = null;
        $lastList = -1;
        for ($i = 1; $i < count($lines); $i++) {
            $line = $lines[$i];
            $parsedLine = str_getcsv($line);
            if (count($parsedLine) == 0) continue; // skip blank lines
            $assoc = array_combine($header, $parsedLine);
            // Import Users
            if ($assoc["EmailAddress"] != $lastUser) {
                $fetchedStatus = getAcctStat($assoc["AccountStatus"]);
                $stmt = mysqli_prepare($con, "INSERT INTO `user` (AccountStatusID,FirstName,LastName,DeliveryAddress,Password,`Language`,PhoneNumber,EmailAddress) VALUES (?,?,?,?,?,?,?,?)" .
                    " ON DUPLICATE KEY UPDATE AccountStatusID = ?, FirstName = ?, LastName = ?, DeliveryAddress = ?, Password = ?, `Language` = ?, PhoneNumber = ?");
                mysqli_stmt_bind_param($stmt, "isssssisisssssi",$fetchedStatus, $assoc["FirstName"], $assoc["LastName"], $assoc["DeliveryAddress"], $assoc["Password"], $assoc["Language"], $assoc["PhoneNumber"], $assoc["EmailAddress"],
                    $fetchedStatus, $assoc["FirstName"], $assoc["LastName"], $assoc["DeliveryAddress"], $assoc["Password"], $assoc["Language"], $assoc["PhoneNumber"]);
                mysqli_stmt_execute($stmt);
                $lastUserId = $con->insert_id;
                $lastUser = $assoc["EmailAddress"];
            }
            // Import Lists
            if ($assoc["ListName"] != $lastList && $assoc["ListName"] != null) {
                $stmt = mysqli_prepare($con, "INSERT INTO list (UserID, ListName) VALUES (?, ?) " .
                    " ON DUPLICATE KEY UPDATE ListName = ?");
                mysqli_stmt_bind_param($stmt, "iss", $lastUserId, $assoc["ListName"], $assoc["ListName"]);
                mysqli_stmt_execute($stmt);
                $lastListId = $con->insert_id;
                if ($lastListId == 0) {
                    $word = addslashes($assoc["ListName"]);
                    $result = mysqli_query($con, "SELECT ListID FROM list WHERE ListName = '{$word}' AND UserID = {$lastUserId}");
                    $result = mysqli_fetch_assoc($result);
                    $lastListId = $result["ListID"];
                }
                $lastList = $assoc["ListName"];
            }

            if ($lastUser == null)
                $lastUser = $assoc["EmailAddress"];
            if ($lastList == -1)
                $lastList = $assoc["ListName"];

            if ($lastList != null && $assoc["SellerName"] != '') {
                // Import ListItems
                $fetchedItem = getItemId($assoc["ItemName"], getSellerId($assoc["SellerName"]));
                $stmt = mysqli_prepare($con, "INSERT INTO listitem (ListID, ItemID, ItemQuantity) VALUES (?,?,?)" .
                    " ON DUPLICATE KEY UPDATE ItemQuantity = ?");
                mysqli_stmt_bind_param($stmt, "iiii",
                    $lastListId, $fetchedItem, $assoc["ItemQuantity"], $assoc["ItemQuantity"]);
                mysqli_stmt_execute($stmt);
            }
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
<h3>Users and Lists</h3>
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
