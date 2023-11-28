<?php
    require 'config.php';
    $importAttempted =  ($_SERVER["REQUEST_METHOD"] == "POST");
    $importSucceeded = false;
    $importErrorMesg = "";
    $conError = false;

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

    <?php
    if ($conError) {
        echo output_error("error", $importErrorMesg);
    }
    else {
        function output_table_open() {
            echo "<table class='table'>\n";
            echo "<thead>";
            echo "<tr class='fw-bold'>\n";
            echo "  <td>SellerID</td>\n";
            echo "  <td>SellerName</td>\n";
            echo "  <td>PhoneNumber</td>\n";
            echo "  <td>EmailAddress</td>\n";
            echo "</tr>\n";
            echo "</thead>";
        }

        function output_table_close() {
            echo "</table>\n";
        }

        function output_order_row($SellerID, $SellerName, $PhoneNumber, $EmailAddress) {
            echo "<tr class='table-active'>\n";
            echo "  <td>{$SellerID}</td>\n";
            echo "  <td>{$SellerName}</td>\n";
            echo "  <td>{$PhoneNumber}</td>\n";
            echo "  <td>{$EmailAddress}</td>\n";
            echo "</tr>\n";
        }

        function output_order_details_row($items, $departments) {
            $department_string = "None";
            $item_str = "None";
            if (count($departments) != 0) {
                $department_string = implode(", ", $departments);
            }
            echo "<tr>";
            echo "<td colspan='3' class='pizzaDataDetailsCell'>";
            echo "Department Name: {$department_string} <br>\n"
                . "</td>";
            echo "</tr>";

            echo "<tr><td>Items that are sold:</td></tr>";
            echo "<tr class='fw-bold'><td></td><td>ItemName</td><td>ItemPrice</td><td>ItemQuantity</td></tr>";
            $output = "";
            foreach($items as $i){
                $output .= "<tr><td></td><td>{$i["item"]}</td><td>{$i["price"]}</td><td>{$i["quantity"]}</td></tr>";
            }
            echo $output;
        }


        $query = "SELECT t0.DepartmentID, t0.DeptName, t1.SellerID, t1.SellerName, t1.PhoneNumber,
        t1.EmailAddress, t2.ItemID, t2.SKU, t2.ItemName, t2.ItemType, t2.Price, t2.QuantityAvailable
        FROM `Department` t0
        INNER JOIN `Seller` t1 
        ON t0.DepartmentID = t1.DepartmentID
        INNER JOIN `Item` t2
        ON t1.SellerID = t2.SellerID";
        ;
        $result = mysqli_query($con, $query);
        if ( ! $result) {
            if (mysqli_errno($con)) {
                output_error("Data retrieval failure", mysqli_error($con));
            }
            else {
                echo "No Department data found!";
            }
        }
        else {
            output_table_open();

            $lastName = null;
            $pizzas = array();
            $pizzerias = array();
            $lastUser = null;
            $lastDepartment = null;
            while ($row = $result->fetch_array()) {
                if ($lastName != $row["DepartmentID"]) {
                    if ($lastName != null) {
                        output_order_details_row($pizzas, $pizzerias);
                    }
                    if($lastUser != $row["SellerID"]){
                        output_order_row($row['SellerID'], $row['SellerName'], $row['PhoneNumber'],
                        $row['EmailAddress']);
                    }
                    $pizzas = array();
                    $pizzerias = array();
                }
                if (!in_array($row["DeptName"], $pizzerias))
                    $pizzerias[] = $row["DeptName"];
                if (!in_array($row["ItemName"], $pizzas))
                    $pizzas[] = array("item" =>  $row["ItemName"],
                                        "price" => $row["Price"],
                                        "quantity" => $row["QuantityAvailable"]
                    );

                $lastName = $row["DepartmentID"];
                $lastUser = $row["SellerID"];
                $lastDepartment = $row["DepartmentID"];

            }
            output_order_details_row($pizzas, $pizzerias);

            output_table_close();
        }
    }
    ?>

    <?php include_once 'footer.php'; ?>
    </body>
</html>
