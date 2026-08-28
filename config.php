<?php
$host = 'localhost';
$dbname = 'abse4142_nawasena';
$username = 'abse4142_nawasena';
$password = 'Kt.nawasena123';

$conn = mysqli_connect($host,$username,$password,$dbname);

if(!$conn){
    die("Koneksi gagal: ".mysqli_connect_error());
}
?>