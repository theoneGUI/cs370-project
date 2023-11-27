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
    <title>
        Amazon 2.0
    </title>
    <?php include_once 'bs.php'; ?>
</head>
<style>
    .pizzaDataTable {
        border-spacing: 0;
    }
    .pizzaDataRow td {
        padding-left: 10px;
    }
    .pizzaDataHeader td {
        padding-right: 20px;
    }

    .pizzaDataDetailsCell {
        padding-left: 20px;
    }
    .pizzaDataTable tr:nth-child(2n) {
        background-color: #cccccc;
    }
</style>
<body>
<?php include_once 'header.php'; ?>

<h1>Amazon 2.0</h1>
<?php
if ($conError) {
    echo output_error("error", $importErrorMesg);
}
else {
    function output_table_open() {
        echo "<table class='table table-striped'>\n";
        echo "<thead>";
        echo "<tr class='pizzaDataHeader'>\n";
        echo "  <td>OrderID</td>\n";
        echo "  <td>UserName</td>\n";
        echo "  <td>OrderDate</td>\n";
        echo "  <td>CreditCardNumber</td>\n";
        echo "  <td>OrderStatus</td>\n";
        echo "  <td>TotalPrice</td>\n";
        echo "</tr>\n";
        echo "</thead>";
    }

    function output_table_close() {
        echo "</table>\n";
    }

    function output_order_row($OrderID, $UserName, $OrderDate, $CreditCardNumber, $OrderStatus, $TotalPrice) {
        echo "<tr class='pizzaDataRow'>\n";
        echo "  <td>{$OrderID}</td>\n";
        echo "  <td>{$UserName}</td>\n";
        echo "  <td>{$OrderDate}</td>\n";
        echo "  <td>{$CreditCardNumber}</td>\n";
        echo "  <td>{$OrderStatus}</td>\n";
        echo "  <td>{$TotalPrice}</td>\n";
        echo "</tr>\n";
    }

    function output_order_details_row($items, $returns) {
        $items_string = "None";
        $return_str = "None";
        if (count($items) != 0) {
            $items_string = implode(", ", $items);
        }
        if (count($returns) != 0) {
            $return_str = implode(", ", $returns);
        }
        echo "<tr>";
        echo "<td colspan='3' class='pizzaDataDetailsCell'>";
        echo "Items ordered: {$items_string} <br>\n" .
            " Items returned: {$return_str}<br>\n"
            . "</td>";
        echo "</tr>";
    }


    $query = "SELECT t0.OrderDate, t0.CreditCardNumber, t0.OrderID, t0.OrderStatus, t0.TotalPrice, 
                  t2.ItemID, t4.ItemName, t3.ItemID AS ReturnItemID, t5.ItemName AS ReturnItemName, t4.ItemName, CONCAT(t1.FirstName, ' ', t1.LastName) 
                  AS UserName FROM `Order` t0 
                  INNER JOIN User t1 
                  ON t0.UserID = t1.UserID
                  INNER JOIN OrderItem t2 
                  ON t0.OrderID = t2.OrderID
                  LEFT OUTER JOIN `Return` t3 
                  ON t0.OrderID = t3.OrderID AND t2.ItemID = t3.ItemID
                  INNER JOIN Item t4 ON t2.ItemID =  t4.ItemID " .
                    "LEFT OUTER JOIN Item t5 ON t3.ItemID = t5.ItemID";
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
            if ($lastName != $row["OrderID"]) {
                if ($lastName != null) {
                    output_order_details_row($pizzas, $pizzerias);
                }
                output_order_row($row["OrderID"], $row["UserName"], $row["UserName"],
                    $row["OrderDate"], $row["CreditCardNumber"], $row["OrderStatus"], $row["TotalPrice"]);

                $pizzas = array();
                $pizzerias = array();
            }
            if (!in_array($row["ItemName"], $pizzas))
                $pizzas[] = $row["ItemName"];
            if (!in_array($row["ReturnItemName"], $pizzerias))
                $pizzerias[] = $row["ReturnItemName"];
            $lastName = $row["OrderID"];
        }
        output_order_details_row($pizzas, $pizzerias);

        output_table_close();
    }
}
?>

<?php include_once 'footer.php'; ?>
</body>
</html>
