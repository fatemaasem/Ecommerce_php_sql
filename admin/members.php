<?php
session_start();
if(!isset($_SESSION['name'])){

    
    header('location:login.php');
    exit;
}
$titilePage='Members Page';
include('init.php');  
$do=isset($_GET['do'])?$_GET['do']:'manage';

if($do=='manage'){
  //in this page will appear not admin users ....the groubID='0'
  include('includes/navbar.php');
  $allMembers=[];
  $stmt=$conn->prepare("SELECT * FROM users WHERE groubID='0'");
  $stmt->execute();
  $allMembers=$stmt->fetchAll(PDO::FETCH_ASSOC);
 ?>
 <table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Full Name</th>
      <th>Register Date</th>
      <th>Control</th>
    </tr>
  </thead>
  <?php
  foreach($allMembers as $member):
  ?>
  <tr>
      <td><?= $member['userID']?></td>
      <td><?=$member['username']?></td>
      <td><?=$member['email']?></td>
      <td><?=$member['fullName']?></td>
      <td><?= $member['date']?></td>
      <td>
        <a href = "?do=Delete&id=<?=$member['userID']?>"class="btn btn-danger btn-sm">Delete</a>
        <a href="?do=edit&id=<?=$member['userID']?>" class="btn btn-primary btn-sm">Edit</a>
        <?php if($member['regStatus']==0):?>
        <a href="?do=active&id=<?=$member['userID']?>" class="btn btn-secondary btn-sm">Active</a>
        <?php endif;?>
      </td>
    </tr>
  <?php endforeach;?>
  
</table>
 <hr>
 <hr>
 <a href='members.php?do=add' class="btn btn-primary">Add new member</a>
 <?php

}
else if($do=='add'){
  include('includes/navbar.php');
  ?>
  
  <div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-4 col-md-6 col-sm-8">
      <h2 class="text-center mt-5">Add Admin</h2>
      <form action ='?do=insert' method='POST'>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" name='name' required ='required'class="form-control" id="username" placeholder="Enter username">
        </div>
         <div class="form-group">
          <label for="username">Password</label>
          <input type="password"  name='password' required ='required' class="form-control" id="password" placeholder="Enter password">
        </div>
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" name='full_name'  required ='required' class="form-control" id="full_name" placeholder="Enter name">
        </div>
        <div class="form-group">
        <label for="email">Email</label>
          <input type="email" name='email' required ='required'   class="form-control" id="email" placeholder="Enter  email">
        </div>

        <button type="submit" class="btn btn-primary">Add</button>
      </form>
    </div>
  </div>
</div>
    
<?php
}
else if($do=='insert'){
  
  $insertErrors=[];
  
  //Check if send form or not 
  if($_SERVER['REQUEST_METHOD']=='POST'){
    //validation of the values of form
    $username=validString($_POST['name']);
    $email=validString($_POST['email']);
    $fullname=validString($_POST['full_name']);
    $password=validString($_POST['password']);
    if(!$username){
      $insertErrors[]="The UserName can not be empty";
    }
    if(!$fullname){
      $insertErrors[]="The full name can not be empty";
    }
    if(!$password){
      $insertErrors[]="The password can not be empty";
    }
    
    if(!$email){
      $insertErrors[]="The email can not be empty";
    }
   if(!minLingth(3,strlen($password),'password')){
    $insertErrors[]="The length must be greater than or equal 3";
   }
   if(!validEmail($email)){
    $insertErrors[]='The email must be not empty and valid';
   }
   
   if(empty($insertErrors)){
    //search about username is found or not in the database 
    //if it found redirect to home page
    $row=checkInDatabase(['username'],[$username],'users');
    /*
    echo "<pre>";
    print_r($row);
    echo "<pre>";
    */
    if(!empty($row)){
      redirect('back',"<div class ='alert alert-danger'>'This username is already found'</div>");
    }
    //if it not found make insert
    $hashed_password=sha1($password);
    $stmt=$conn->prepare('INSERT INTO users (username,password,email,fullName,regStatus) values(?,?,?,?,?)');
    $stmt->execute([$username,$hashed_password,$email,$fullname,1]);
    redirect("back","<div class='alert alert-success'>'Add Success");
   }
   else{
    //if the data is not valid
    printErorrs($insertErrors,'back');
  
   }
  }
  else{
    //if the page is opened directly
   redirectHomeWithErorr(3,'You can not go to this page directly');
  }
}
else if($do=='edit'){
  $editErorr=[];
 if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
     redirectHomeWithErorr("There is an error",5,'members.php');
}
$id=$_GET['id'];
//know if this members is found or not
$allRows=checkInDatabase(['userID'],[$id],'users');
$row='';
if(sizeof($allRows))$row=$allRows[0];
else $row=$allRows;
$stmt=$conn->prepare('SELECT *  FROM users WHERE userID=?');

  //if is not found 
  if(empty($row)){
   
    redirectHomeWithErorr('You can not make edit because this id is not found');
  }
  
    if($_SERVER['REQUEST_METHOD']=='POST'){
      
      
      //validation in the data
        $username=validString($_POST['name']);
        $email=validString($_POST['email']);
        $fullname=validString($_POST['full_name']);
        $password=validString($_POST['password']);
        if(!validString($username)){
          $editErorr[]="The name must be not empty";
        }
       if(!validEmail($email)){
        $editErorr[]="Email must be valid";
       }
       if(strlen($password)>0){
        
        if(!minLingth(3,strlen($password))){
          $editErorr[]= "Password must be greater than or equal 3";
        }
      }
       if(empty($editErorr)){
        if(!$password)
        $password=$row['password'];
        else 
        $password=sha1($password);
        $stmt=$conn->prepare('UPDATE users SET username= ?,email=?,fullName=?,password=? WHERE userID=?');
        $stmt->execute([$username,$email,$fullname,$password,$id]);
        
        redirect("back","<div class='alert alert-success'>Updated Successfully</div>");
    }
  }
  printErorrs($editErorr);
  
  include('includes/navbar.php');
 
   ?>
    
    <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-4 col-md-6 col-sm-8">
        <h2 class="text-center mt-5">Edit Admin</h2>
        <form action ='' method='POST'>
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name='name'value='<?= $row['username']?>' class="form-control" id="username" placeholder="Enter your  new username">
          </div>
           <div class="form-group">
            <label for="username">Password</label>
            <input type="password"  name='password' class="form-control" id="password" placeholder="Enter your  new password">
          </div>
          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name='full_name' value='<?= $row['fullName']?>' class="form-control" id="full_name" placeholder="Enter your  new name">
          </div>
          <div class="form-group">
          <label for="email">Email</label>
            <input type="email" name='email' value='<?= $row['email']?>'  class="form-control" id="email" placeholder="Enter your  new email">
          </div>

          <button type="submit" class="btn btn-primary">Edit</button>
        </form>
      </div>
    </div>
  </div>
      
<?php }
else if($do=='Delete'){
  
  if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
    redirect("back","<div class ='alert alert-danger'>'There is ann error in the url'</div>");
  }
  $id=$_GET['id'];
  $row=checkInDatabase(['userID'],[$id],'users');
  if(!empty($row)){
  $stmt=$conn->prepare('DELETE FROM  users WHERE userID=?');
  $stmt->execute([$id]);
  
  redirect("back","<div class='alert alert-success'>'Deleted Success'</div>");
  }
  else{
    redirect("back","<div class ='alert alert-danger'>'This user is not found to deleted'</div>");
    
  }
}
else if($do=='pending'){
  
  include('includes/navbar.php');
  $row=checkInDatabase(['regStatus','groubID'],['0','0'],'users');
  if(empty($row)){
    echo 'There are not eny members<br>';
  }
  else{
    $allMembers=$row;
    ?>
    <table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Full Name</th>
      <th>Register Date</th>
      <th>Control</th>
    </tr>
  </thead>
  <?php
  foreach($allMembers as $member):
  ?>
  <tr>
      <td><?= $member['userID']?></td>
      <td><?=$member['username']?></td>
      <td><?=$member['email']?></td>
      <td><?=$member['fullName']?></td>
      <td><?= $member['date']?></td>
      <td>
        <a href = "?do=Delete&id=<?=$member['userID']?>"class="btn btn-danger btn-sm">Delete</a>
        <a href="?do=edit&id=<?=$member['userID']?>" class="btn btn-primary btn-sm">Edit</a>
      </td>
    </tr>
  <?php endforeach;?>
  
</table>
    <?php
  }
}
else if($do=='active'){
  
  if(!isset($_GET['id'])){
   header('location:home.php');
  }
  $id=$_GET['id'];
  $row=checkInDatabase(['userID'],[$_GET['id']],'users');
  if(empty($row))header('location:home.php');
  else{
    $stmt=$conn->prepare("UPDATE users SET regStatus='1' WHERE userID=?");
    $stmt->execute([$id]);
    redirect('back',"<div class='alert alert-success'>active successfully</div>");
  }
}


//header('location:home.php');

include ('includes/footer.php');
?>
