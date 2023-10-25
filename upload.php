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
            $lines = explode('\n', $contents);

            for ($i = 1; $i < count($lines); $i++) {
                $line = $lines[$i];
                $parsedLine = str_getcsv($line);
                // TODO: do the code and import to database
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
        <title>Pizza Data Import</title>
    </head>
    <body>
        <h1>Pizza Data Import</h1>
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
            File: <input type="file" name="importFile"/>
            <br><br>
            <button type="submit">Submit</button>
        </form>
    </body>
</html>
