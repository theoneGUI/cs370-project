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
            echo "<table class='table table-striped'>\n";
            echo "<thead>";
            echo "<tr class='pizzaDataHeader'>\n";
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
            echo "<tr class='pizzaDataRow'>\n";
            echo "  <td>{$SellerID}</td>\n";
            echo "  <td>{$SellerName}</td>\n";
            echo "  <td>{$PhoneNumber}</td>\n";
            echo "  <td>{$EmailAddress}</td>\n";
            echo "</tr>\n";
        }

        function output_order_details_row($departments, $items) {
            $department_string = "None";
            $item_str = "None";
            if (count($departments) != 0) {
                $department_string = implode(", ", $departments);
            }
            if (count($items) != 0) {
                $item_str = implode(", ", $items);
            }
            echo "<tr>";
            echo "<td colspan='3' class='pizzaDataDetailsCell'>";
            echo "Department Name: {$department_string} <br>\n"
                . "</td>";
            echo "</tr>";

            echo "<tr><td>Items that are sold:</td></tr>";
            echo "<tr><td></td><td>$item_str</td></tr>";
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
            while ($row = $result->fetch_array()) {
                if ($lastName != $row["DepartmentID"]) {
                    if ($lastName != null) {
                        output_order_details_row($pizzerias, $pizzas);
                    }
                    if($lastUser != $row["SellerID"]){
                        output_order_row($row['SellerID'], $row['SellerName'], $row['PhoneNumber'],
                        $row['EmailAddress']);
                    }
                    $pizzas = array();
                    $pizzerias = array();
                }
                if (!in_array($row["DeptName"], $pizzas))
                    $pizzas[] = $row["DeptName"];
                if (!in_array($row["ItemName"], $pizzerias))
                    $pizzerias[] = $row["ItemName"];
                $lastName = $row["ItemName"];
                $lastUser = $row["DepartmentID"];
            }
            output_order_details_row($pizzerias, $pizzas);

            output_table_close();
        }
    }
    ?>

    <?php include_once 'footer.php'; ?>
    </body>
</html>
