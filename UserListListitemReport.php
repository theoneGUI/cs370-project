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


<?php
require 'config.php';
$importAttempted =  ($_SERVER["REQUEST_METHOD"] == "POST");
$conError = false;
$importErrorMesg = "";

$con = @connect();
if ($con[0]) {
    $con = $con[1];

}
else {
    $importErrorMesg = $con[1];
    $conError = true;
}

function output_error($title, $error) {
    echo "<span style='color:red;'>\n";
    echo "<h2>". $title . "</h2>\n";
    echo "<h4>" . $error . "</h4>";
    echo "</span>";
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


<?php
    if ($conError) {
        echo output_error("error", $importErrorMesg);
    }
    else {
        function output_table_open() {
            echo "<table class='table table-striped'>\n";
            echo "<thead>";
            echo "<tr class='pizzaDataHeader'>\n";
            echo "  <td>UserID</td>\n";
            echo "  <td>AccountStatusID</td>\n";
            echo "  <td>UserName</td>\n";
            echo "  <td>DeliveryAddress</td>\n";
            echo "  <td>Password</td>\n";
            echo "  <td>Language</td>\n";
            echo "  <td>PhoneNumber</td>\n";
            echo "  <td>EmailAddress</td>\n";
            echo "  <td>ListID</td>\n";
            echo "  <td>ListName</td>\n";
            echo "  <td>ItemID</td>\n";
            echo "  <td>ItemQuantity</td>\n";
            echo "  <td>ItemName</td>\n";
            echo "</tr>\n";
            echo "</thead>";
        }

        function output_table_close() {
            echo "</table>\n";
        }

        function output_order_row($UserID, $AccountStatusID, $UserName, $DeliveryAddress, $Password, $Language,
        $PhoneNumber, $EmailAddress, $ListID, $ListName, $ItemID, $ItemQuantity, $ItemName) {
            echo "<tr class='pizzaDataRow'>\n";
            echo "  <td>{$UserID}</td>\n";
            echo "  <td>{$AccountStatusID}</td>\n";
            echo "  <td>{$UserName}</td>\n";
            echo "  <td>{$DeliveryAddress}</td>\n";
            echo "  <td>{$Password}</td>\n";
            echo "  <td>{$Language}</td>\n";
            echo "  <td>{$PhoneNumber}</td>\n";
            echo "  <td>{$EmailAddress}</td>\n";
            echo "  <td>{$ListID}</td>\n";
            echo "  <td>{$ListName}</td>\n";
            echo "  <td>{$ItemID}</td>\n";
            echo "  <td>{$ItemQuantity}</td>\n";
            echo "  <td>{$ItemName}</td>\n";
            echo "</tr>\n";
        }

        function output_order_details_row($users, $lists) {
            $items_string = "None";
            $return_str = "None";
            if (count($users) != 0) {
                $items_string = implode(", ", $users);
            }
            if (count($lists) != 0) {
                $return_str = implode(", ", $lists);
            }
            echo "<tr>";
                echo "<td colspan='3' class='pizzaDataDetailsCell'>";
                    echo "Users: {$items_string} <br>\n" .
                        " Lists: {$return_str}<br>\n"
                . "</td>";
            echo "</tr>";
        }


        $query = "SELECT t0.UserID, t0.AccountStatusID, CONCAT(t0.FirstName, ' ',  t0.LastName) AS UserName, t0.DeliveryAddress, 
        t0.Password, t0.Language, t0.PhoneNumber, t0.EmailAddress, t1.ListID, t1.ListName, t2.ItemID, t2.ItemQuantity,
        t3.ItemName FROM `User` t0 
        INNER JOIN `List` t1
        ON t0.UserID = t1.UserID
        INNER JOIN `ListItem` t2
        ON t1.ListID = t2.ListID
        INNER JOIN `Item` t3
        ON t2.ItemID = t3.ItemID";
        $result = mysqli_query($con, $query);
        if ( ! $result) {
            if (mysqli_errno($con)) {
                output_error("Data retrieval failure", mysqli_error($con));
            }
            else {
                echo "No order data found!";
            }
        }
        else {
            output_table_open();

            $lastName = null;
            $pizzas = array();
            $pizzerias = array();
            while ($row = $result->fetch_array()) {
                if ($lastName != $row["UserID"]) {
                    if ($lastName != null) {
                        output_order_details_row($pizzas, $pizzerias);
                    }
                    output_order_row($row["UserID"], $row["AccountStatusID"], $row["UserName"], $row["DeliveryAddress"],
                        $row["Password"], $row["Language"], $row["PhoneNumber"], $row["EmailAddress"], $row["ListID"],
                        $row["ListName"], $row["ItemID"], $row["ItemQuantity"], $row["ItemName"]);

                    $pizzas = array();
                    $pizzerias = array();
                }
                if (!in_array($row["UserID"], $pizzas))
                    $pizzas[] = $row["UserID"];
                if (!in_array($row["ListID"], $pizzerias))
                    $pizzerias[] = $row["ListID"];
                $lastName = $row["UserID"];
            }
            output_order_details_row($pizzas, $pizzerias);

            output_table_close();
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
