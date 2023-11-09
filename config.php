<?php


    function connect()
    {
        $con = @mysqli_connect('localhost', 'root', '', 'mydb');

        if (mysqli_connect_errno()) {
            return [false, "Failed to connect to MySQL. " . mysqli_connect_error()];
        }
        return [true, $con];
    }
?>