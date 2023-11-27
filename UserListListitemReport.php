<?php
require 'config.php';
$importAttempted =  ($_SERVER["REQUEST_METHOD"] == "POST");
$importSucceeded = false;
$importErrorMesg = "";
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
            echo "</tr>\n";
            echo "</thead>";
        }

        function output_table_close() {
            echo "</table>\n";
        }

        function output_order_row($UserID, $AccountStatusID, $UserName, $DeliveryAddress, $Password, $Language,
        $PhoneNumber, $EmailAddress) {
            echo "<tr class='pizzaDataRow'>\n";
            echo "  <td>{$UserID}</td>\n";
            echo "  <td>{$AccountStatusID}</td>\n";
            echo "  <td>{$UserName}</td>\n";
            echo "  <td>{$DeliveryAddress}</td>\n";
            echo "  <td>{$Password}</td>\n";
            echo "  <td>{$Language}</td>\n";
            echo "  <td>{$PhoneNumber}</td>\n";
            echo "  <td>{$EmailAddress}</td>\n";
            echo "</tr>\n";
        }

        function output_order_details_row($lists, $items) {
            $list_string = "None";
            $item_str = "None";
            if (count($lists) != 0) {
                $list_string = implode(", ", $lists);
            }
            if (count($items) != 0) {
                $item_str = implode(", ", $items);
            }
            echo "<tr>";
                echo "<td colspan='3' class='pizzaDataDetailsCell'>";
                    echo "ListName: {$list_string} <br>\n" .
                        " Items: {$item_str}<br>\n"
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
            $lastUser = null;
            while ($row = $result->fetch_array()) {
                if ($lastName != $row["ListName"]) {
                    if ($lastName != null) {
                        output_order_details_row($pizzerias, $pizzas);
                    }
                    if($lastUser != $row["UserID"]){
                        output_order_row($row["UserID"], $row["AccountStatusID"], $row["UserName"], $row["DeliveryAddress"],
                            $row["Password"], $row["Language"], $row["PhoneNumber"], $row["EmailAddress"]);
                    }
                    $pizzas = array();
                    $pizzerias = array();
                }
                if (!in_array($row["ItemName"], $pizzas))
                    $pizzas[] = $row["ItemName"];
                if (!in_array($row["ListName"], $pizzerias))
                    $pizzerias[] = $row["ListName"];
                $lastName = $row["ListName"];
                $lastUser = $row["UserID"];
            }
            output_order_details_row($pizzerias, $pizzas);

            output_table_close();
        }
    }
?>
<?php include_once 'footer.php'; ?>
</body>
</html>
