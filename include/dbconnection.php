<?php
date_default_timezone_set("Asia/Colombo");
$con=mysqli_connect("localhost", "root", "", "resturant_system");
if(mysqli_connect_errno()){
echo "Connection Fail".mysqli_connect_error();
}