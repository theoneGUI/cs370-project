<?php


    function connect()
    {
        $con = mysqli_connect('localhost', 'service', 'service', '370');

        if (mysqli_connect_errno()) {
            return [false, "Failed to connect to MySQL. " . mysqli_connect_error()];
        }
        return [true, $con];
    }
?>