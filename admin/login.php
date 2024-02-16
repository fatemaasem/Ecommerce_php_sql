<?php
    session_start();
    include("init.php");  
    include("includes/header.php");
    $titilePage="Login Admin";
    $error=[];
    if(isset($_POST['submit'])){
       
            $name=validString($_POST['name']);
            $password=validString($_POST['password']);
            if(!$name||!$password){
            $error[] = 'The name and password are required';
            }
            if(!$error){
            $stmt=$conn->prepare("SELECT *FROM users WHERE username=? AND groubID=1");
            $stmt->execute([$name]);
            $admin=$stmt->fetchAll();
            if(!$admin) {
              $error[] = "This user is not found";
            }
            else{
            $hash=sha1($password);
            $check=($hash==$admin[0]['password'])?"1":"0";
            //echo $password."<br>".$admin[0]['password'].'<br>'.$hash;
            //echo $check;
            if(!$check){
              $error[] = 'This password is not found';
            }
            
           if(empty($error)){
            $_SESSION['name']=$admin[0]['fullName'];
            header("location:home.php");
            }
        }
      }
        if($error){
          printErorrs($error);
        }
    }
?>
    <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-4 col-md-6 col-sm-8">
        <h2 class="text-center mt-5">Login For Admin</h2>
        <form action ='?do=insert' method='POST'>
          <!-- div for add name category -->
          <div class="form-group">
            <label for="username">Name</label>
            <input type="text" name='name' class="form-control" id="username" placeholder="Enter the name of admin">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="text" name='password' class="form-control" id="password" placeholder="Enter password">
          </div>
          <button type="submit" name='submit' class="btn btn-primary">Login</button>
        </form>
<?php
    include("includes/footer.php");
?>