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
    .pizzaDataRow td {
        padding-left: 10px;
    }
    .pizzaDataHeader td {
        padding-right: 20px;
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
        echo "<table class='table'>\n";
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
        echo "<tr class='table-active'>\n";
        echo "  <td>{$OrderID}</td>\n";
        echo "  <td>{$UserName}</td>\n";
        echo "  <td>{$OrderDate}</td>\n";
        echo "  <td>{$CreditCardNumber}</td>\n";
        echo "  <td>{$OrderStatus}</td>\n";
        echo "  <td>{$TotalPrice}</td>\n";
        echo "</tr>\n";
    }

    function output_order_details_row($items, $returns) {
        $output = "";
        $needPrintItemHeader = true;
        foreach ($items as $i) {
            if ($i["Used"])
                $output .= "<tr><td></td><td>{$i["Name"]}</td><td>".'$'."{$i["Price"]} (x{$i["Quantity"]})</td><td>{$i["Seller"]}</td></tr>";
        }
        if ($output == "") {
            echo "<tr><td>No items ordered.</td></tr>";
        }
        else {
            $needPrintItemHeader = false;
            echo "<tr><td>Items Ordered:<td></tr>";
            echo "<tr><td></td><td>Item Name</td><td>Price (Quantity)</td><td>Seller Name</td></tr>";
            echo $output;
        }
        $output = "";
        foreach ($returns as $i) {
            if ($i["Used"])
                $output .= "<tr><td></td><td>{$i["Name"]}</td><td>".'$'."{$i["Price"]} (x{$i["Quantity"]})</td><td>{$i["Seller"]}</td></tr>";
        }
        if ($output == "") {
            echo "<tr><td>No items returned.</td></tr>";
        }
        else {
            echo "<tr><td>Items Returned:<td></tr>";
            if ($needPrintItemHeader)
                echo "<tr><td></td><td>Item Name</td><td>Price (Quantity)</td><td>Seller Name</td></tr>";
            echo $output;
        }
    }


    $query = "SELECT t0.OrderDate, t0.CreditCardNumber, t0.OrderID, t0.OrderStatus, t0.TotalPrice, 
                  t2.ItemID, t4.ItemName, t3.ItemID AS ReturnItemID, t5.ItemName AS ReturnItemName, t4.ItemName, CONCAT(t1.FirstName, ' ', t1.LastName) 
                  AS UserName, t6.ItemName as OrderItemName, t6.Price as OrderItemPrice, t2.ItemQuantity as OrderItemQuantity,
                  t7.SellerName AS OrderItemSellerName, t8.SellerName AS ReturnItemSellerName, t3.ItemQuantity AS ReturnItemQuantity, t5.Price AS ReturnItemPrice
                  FROM `Order` t0 
                  INNER JOIN User t1 
                  ON t0.UserID = t1.UserID
                  INNER JOIN OrderItem t2 
                  ON t0.OrderID = t2.OrderID
                  LEFT OUTER JOIN `Return` t3 
                  ON t0.OrderID = t3.OrderID AND t2.ItemID = t3.ItemID
                  INNER JOIN Item t4 ON t2.ItemID =  t4.ItemID " .
                    "LEFT OUTER JOIN Item t5 ON t3.ItemID = t5.ItemID ".
                    "LEFT OUTER JOIN Item t6 ON t2.ItemID = t6.ItemID ".
                    "LEFT OUTER JOIN Seller t7 ON t6.SellerID = t7.SellerID ".
                    "LEFT OUTER JOIN Seller t8 ON t5.SellerID = t8.SellerID";
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
                output_order_row($row["OrderID"], $row["UserName"],
                    $row["OrderDate"], $row["CreditCardNumber"], $row["OrderStatus"], $row["TotalPrice"]);

                $pizzas = array();
                $pizzerias = array();
            }
            if (!in_array($row["ItemName"], $pizzas))
                $pizzas[] = array(
                        "Used" => $row["ItemName"] != null,
                        "Name" => $row["ItemName"],
                        "Price" => $row["OrderItemPrice"],
                        "Seller" => $row["OrderItemSellerName"],
                        "Quantity" => $row["OrderItemQuantity"]
                );
            if (!in_array($row["ReturnItemName"], $pizzerias))
                $pizzerias[] = array(
                        "Used" => $row["ReturnItemName"] != null,
                        "Name" => $row["ReturnItemName"],
                        "Price" => $row["ReturnItemPrice"],
                        "Seller" => $row["ReturnItemSellerName"],
                        "Quantity" => $row["ReturnItemQuantity"]
                );
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
