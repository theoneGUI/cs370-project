<?php
require 'config.php';
$importAttempted =  ($_SERVER["REQUEST_METHOD"] == "POST");
$conError = false;
$importErrorMesg = "";
$importSucceeded = false;

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
    <title>Amazon 2.0 Data Report</title>
    <?php include_once 'bs.php'; ?>
</head>
<body>
<?php include_once 'header.php';?>
<h1>Amazon 2.0 Data Report</h1>
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
            echo "<table class='table'>\n";
            echo "<thead>";
            echo "<tr class='fw-bold'>\n";
            echo "  <td>UserID</td>\n";
            echo "  <td>AccountStatus</td>\n";
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
            echo "<tr class='table-active'>\n";
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
            if (count($lists) != 0) {
                $list_string = implode(", ", $lists);
            }
            echo "<tr>";
                echo "<td colspan='4' class='fw-bold'>";
                    echo "ListName: {$list_string} <br>\n"
                . "</td>";
            echo "</tr>";

            echo "<tr><td colspan='4'>Items in List:</td></tr>";
            echo "<tr class='fw-bold'><td class='table-borderless'></td><td>ItemName</td><td>ItemPrice</td><td>ItemQuantity</td></tr>";
            $output = "";
            foreach($items as $i){
                $output .= "<tr><td class='table-borderless'></td><td>{$i["item"]}</td><td>{$i["price"]}</td><td>{$i["quantity"]}</td></tr>";
            }
            echo $output;
        }

        $query = "SELECT t0.UserID, t4.AccountStatusName AS AccountStatusID, CONCAT(t0.FirstName, ' ',  t0.LastName) AS UserName, t0.DeliveryAddress, 
        t0.Password, t0.Language, t0.PhoneNumber, t0.EmailAddress, t1.ListID, t1.ListName, t2.ItemID, t2.ItemQuantity,
        t3.ItemName, t3.Price FROM `user` t0 
        INNER JOIN `list` t1
        ON t0.UserID = t1.UserID
        INNER JOIN `listitem` t2
        ON t1.ListID = t2.ListID
        INNER JOIN `item` t3
        ON t2.ItemID = t3.ItemID "
        . "INNER JOIN accountstatus t4 ON t4.AccountStatusID = t0.AccountStatusID";
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
                    $pizzas[] = array(
                            "item" => $row["ItemName"],
                            "price" => $row["Price"],
                            "quantity" => $row["ItemQuantity"]
                    );
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
