
<?php 
session_start();


if(!isset($_SESSION['messege'])){
    header('home.php');
}
include('includes/functions/function.php');
include('includes/header.php');?>
<center>
 <h1 style="color:green">"<?=$_SESSION['messege']?>"</h1>
 </center>
 <?php unset($_SESSION['messege']);
 header('refresh:3;url=home.php');

 ?>
 