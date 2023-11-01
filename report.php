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
        Pizza Data Report
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

    <h1>Pizza Data report</h1>
    <?php
    if ($conError) {
        echo output_error("error", $importErrorMesg);
    }
    else {
        function output_table_open() {
            echo "<table class='table table-striped'>\n";
            echo "<thead>";
            echo "<tr class='pizzaDataHeader'>\n";
            echo "  <td>Name</td>\n";
            echo "  <td>Age</td>\n";
            echo "  <td>Gender</td>\n";
            echo "</tr>\n";
            echo "</thead>";
        }

        function output_table_close() {
            echo "</table>\n";
        }

        function output_person_row($name, $age, $gender) {
            echo "<tr class='pizzaDataRow'>\n";
            echo "  <td>{$name}</td>\n";
            echo "  <td>{$age}</td>\n";
            echo "  <td>{$gender}</td>\n";
            echo "</tr>\n";
        }

        function output_person_details_row($pizzas, $pizzerias) {
            $pizzas_string = "None";
            $pizzerias_str = "None";
            if (count($pizzas) != 0) {
                $pizzas_string = implode(", ", $pizzas);
            }
            if (count($pizzerias) != 0) {
                $pizzerias_str = implode(", ", $pizzerias);
            }
            echo "<tr>";
                echo "<td colspan='3' class='pizzaDataDetailsCell'>";
                    echo "Pizzas eaten: {$pizzas_string} <br>\n" .
                        " Pizzerias Frequented: {$pizzerias_str}<br>\n"
                . "</td>";
            echo "</tr>";
        }


        $query = " SELECT t0.name, t0.age, t0.gender, t1.pizza, t2.pizzeria"
                ." FROM person t0 LEFT OUTER JOIN eats t1 ON t0.name = t1.name"
                ." LEFT OUTER JOIN frequents t2 ON t0.name = t2.name";
        $result = mysqli_query($con, $query);
        if ( ! $result) {
            if (mysqli_errno($con)) {
                output_error("Data retrieval failure", mysqli_error($con));
            }
            else {
                echo "No pizza data found";
            }
        }
        else {
            output_table_open();

            $lastName = null;
            $pizzas = array();
            $pizzerias = array();
            while ($row = $result->fetch_array()) {
                if ($lastName != $row["name"]) {
                    if ($lastName != null) {
                        output_person_details_row($pizzas, $pizzerias);
                    }
                    output_person_row($row["name"], $row["age"], $row["gender"]);

                    $pizzas = array();
                    $pizzerias = array();
                }
                if (!in_array($row["pizza"], $pizzas))
                    $pizzas[] = $row["pizza"];
                if (!in_array($row["pizzeria"], $pizzerias))
                    $pizzerias[] = $row["pizzeria"];
                $lastName = $row["name"];
            }
            output_person_details_row($pizzas, $pizzerias);

            output_table_close();
        }
    }
    ?>

<?php include_once 'footer.php'; ?>
</body>
</html>
