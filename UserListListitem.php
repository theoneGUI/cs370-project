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
        $lastUser = null;
        $lastList = -1;
        for ($i = 1; $i < count($lines); $i++) {
            $line = $lines[$i];
            $parsedLine = str_getcsv($line);
            if (count($parsedLine) == 0) continue; // skip blank lines
            $assoc = array_combine($header, $parsedLine);
            // Import Users
            if ($assoc["UserID"] != $lastUser) {
                $stmt = mysqli_prepare($con, "INSERT INTO `user` (UserID, AccountStatusID,FirstName,LastName,DeliveryAddress,Password,`Language`,PhoneNumber,EmailAddress) VALUES (?,?,?,?,?,?,?,?,?)" .
                    " ON DUPLICATE KEY UPDATE AccountStatusID = ?, FirstName = ?, LastName = ?, DeliveryAddress = ?, Password = ?, `Language` = ?, PhoneNumber = ?, EmailAddress = ?");
                mysqli_stmt_bind_param($stmt, "iisssssisisssssis",$assoc["UserID"], $assoc["AccountStatusID"], $assoc["FirstName"], $assoc["LastName"], $assoc["DeliveryAddress"], $assoc["Password"], $assoc["Language"], $assoc["PhoneNumber"], $assoc["EmailAddress"],
                    $assoc["AccountStatusID"], $assoc["FirstName"], $assoc["LastName"], $assoc["DeliveryAddress"], $assoc["Password"], $assoc["Language"], $assoc["PhoneNumber"], $assoc["EmailAddress"]);
                mysqli_stmt_execute($stmt);
                $lastUserId = $assoc["UserID"];
                $lastUser = $assoc["UserID"];
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
                $lastUser = $assoc["UserID"];
            if ($lastList == -1)
                $lastList = $assoc["ListName"];

            if ($lastList != null && $assoc["ItemID"] != '') {
                // Import ListItems
                $stmt = mysqli_prepare($con, "INSERT INTO listitem (ListID, ItemID, ItemQuantity) VALUES (?,?,?)" .
                    " ON DUPLICATE KEY UPDATE ItemQuantity = ?");
                mysqli_stmt_bind_param($stmt, "iiii",
                    $lastListId, $assoc["ItemID"], $assoc["ItemQuantity"], $assoc["ItemQuantity"]);
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
