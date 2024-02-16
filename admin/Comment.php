<?php
    session_start();

    //if The Admin do not make login ..redirect to login page
    if(!isset($_SESSION['name'])){
        header('location:login.php');
        exit;
    }
    //titile of this page 
    $titilePage='Comments Page';
    include('init.php'); 
    include('includes/navbar.php'); 
    //if the main page (there is not found do on url) the page will be manage 
    $do=isset($_GET['do'])?$_GET['do']:'manage';
    if($do== 'manage') {
        //all comments in database in array all_comments
        $all_comments=[];
        $stmt=$conn->prepare("SELECT * FROM comment ");
        $stmt->execute();
        $all_comments=$stmt->fetchAll(PDO::FETCH_ASSOC);
        
        ?>
        <!-- make table to view all comments -->
        <table class="table">
            <thead>
                <tr>
                <th>ID</th>
                <th>Comment</th>
                <th>Item Name</th>
                <th>User Name</th>
                <th>Added Date</th>
                <th>Control</th>
                </tr>
            </thead>
        
            <?php 
             foreach($all_comments as $row): 
                $item_name='';
                $user_name='';
                
               $stmt= $conn->prepare('SELECT Name from items WHERE Item_ID=?');
               $stmt->execute([$row['item_id']]);
               $item_name=$stmt->fetch(PDO::FETCH_ASSOC)['Name'];
               $stmt= $conn->prepare('SELECT username from users WHERE userID=?');
               $stmt->execute([$row['user_id']]);
               $user_name=$stmt->fetch(PDO::FETCH_ASSOC)['username'];
           
                //$user_name
            
            ?>
                
            <tr>
                <td><?= $row['Comment_ID']?></td>
                <td><?=$row['comment']?></td>
                <td><?= $item_name?></td>
                <td><?=$user_name?></td>
                <td><?= $row['comment_date'].$row['status']?></td>
                <td>
                    <a href = "?do=delete&id=<?=$row['Comment_ID']?>"class="btn btn-danger btn-sm">Delete</a>
                    <a href="?do=edit&id=<?=$row['Comment_ID']?>" class="btn btn-primary btn-sm">Edit</a>
                    <?php if($row['status']==0):?>
                    <a href="?do=active&id=<?=$row['Comment_ID']?>" class="btn btn-secondary btn-sm">Active</a>
                    <?php endif;?>
                </td>
            </tr>
            <?php endforeach;?>                                                   
  
        </table>
        <hr>
        <hr> 
        <a href='?do=add' class="btn btn-primary">Add new comment</a>
        <?php

            
    }else if($do== 'add') {
        $items=get_all_rows('items');
        $users=get_all_rows('users');
     
        ?>
        
        <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6 col-sm-8">
            <h2 class="text-center mt-5">Add Comment</h2>
            <form action ='?do=insert' method='POST'>
              <div class="form-group">
                <label for='comment'>Comment</label>
                <input type="text" name='comment' required ='required'class="form-control" id='comment' placeholder="Enter username">
            </div>
            <div class="form-group">
              <label for="item">Item Name</label><br>
               <select name="item" id="item">
                    <option value=0>......</option>";
                    <?php foreach($items as $item){
                        $name=$item['Name'];
                        $id=$item['Item_ID'];
                        
                        echo "<option value='$id'>$name</option>";
                    }
                    ?>
               </select><br>
            </div>
            <div class="form-group">
              <label for="user">User Name</label><br>
               <select name="user" id="user">
                    <option value=0>......</option>";
                    <?php foreach($users as $user){
                        $name=$user['username'];
                        $id=$user['userID'];
                        
                        echo "<option value='$id'>$name</option>";
                    }
                    ?>
                   
                
               </select><br>
            </div>
            <div class="form-group">
                <label for="states">Status</label><br>
                <select name="status" id='status'>
                    <option value="-1">....</option>
                    <option value="1">YES</option>
                    <option value="0">NO</option>
                </select>
            </div>
              <button type="submit" class="btn btn-primary">Add Comment</button>
            </form>
          </div>
        </div>
      </div>
          
      <?php

    }else if($do== 'insert') {
        $insertErrors=[];
  
  //Check if send form or not 
  if($_SERVER['REQUEST_METHOD']=='POST'){
    //validation of the values of form
    $comment=validString($_POST['comment']);
    $item_id=$_POST['item'];
    $user_id=$_POST['user'];
    echo $user_id;
    $status=$_POST['status'];
    if(!$comment){
      $insertErrors[]="The comment can not be empty";
    }
    if(!$item_id){
      $insertErrors[]="There is not found item";
    }
    if(!$user_id){
      $insertErrors[]="There is not found user";
    }
    if($status==-1){
        $insertErrors[]="the status is empty";
    }

    
   if(empty($insertErrors)){

   
    $stmt=$conn->prepare('INSERT INTO comment (`comment`,`item_id`,`user_id`,`status`) values(?,?,?,?)');
    $stmt->execute([$comment,$item_id,$user_id,$status]);
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
    else if($do== 'edit') {
         
        include('includes/navbar.php');
        $editErorr=[];
        if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
            redirectHomeWithErorr("There is an error",5,'Comment.php');
        }
        $id=$_GET['id'];
        //know if this members is found or not
        $allRows=checkInDatabase(['Comment_ID'],[$id],'comment');
        $row='';
        $all_items=[];
        $all_users=[];
        $item_name='';
        $user_name="";
        if(sizeof($allRows))$row=$allRows[0];
        else $row=$allRows;
        $stmt=$conn->prepare('SELECT *  FROM comment WHERE Comment_ID=?');
        $stmt->execute([$id]);
        //if is not found 
        if(empty($row)){
        
            redirectHomeWithErorr('You can not make edit because this id is not found',5,'comment.php');
            exit;
        }
        $stmt2=$conn->prepare("SELECT Name FROM items WHERE Item_ID=?");
        $stmt2->execute([$row['item_id']]);
        $item_name=$stmt2->fetch(PDO::FETCH_ASSOC)['Name'];
        echo "item_name". $item_name."<br>";
        $stmt3=$conn->prepare("SELECT username FROM users WHERE userID=?");
        $stmt3->execute([$row['user_id']]);
        $user_name=$stmt3->fetch(PDO::FETCH_ASSOC)['username'];
        echo "user_name". $user_name."<br>";
        $stmt2=$conn->prepare("SELECT * FROM items ");
        $stmt2->execute();
        $all_items=$stmt2->fetchAll(PDO::FETCH_ASSOC);
        $stmt3=$conn->prepare("SELECT * FROM users ");
        $stmt3->execute();
        $all_users=$stmt3->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
       // print_r($all_users);
        echo "</pre>";
        ?>
            
            <div class="container">
            <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-8">
                <h2 class="text-center mt-5">Edit Comment</h2>
                <form action ="?do=update&id=<?=$id?>" method='POST'>
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <input type="text" name='comment'value='<?= $row['comment']?>' class="form-control" id="comment" placeholder="Enter your new comment">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <input type="text"  name='status' value='<?= $row['status']?>'  class="form-control" id="status" placeholder="Enter your  new status">
                </div>
                <div class="form-group">
                    <label for="item_name">Item Name</label>
                    <input type="text" name='item_name' value='<?= $item_name?>' class="form-control" id="item_name" placeholder="Enter your  new item name">
                </div>
                <div class="form-group">
                <label for="user_name">User Name</label>
                    <input type="text" name='user_name' value='<?= $user_name?>'  class="form-control" id="user_name" placeholder="Enter your  new user_name">
                </div>
                <div>
                    <label>Items</label><br>
                    <select name='item_name'>
                        <?php 
                        
                       
                            foreach($all_items as $item){
                                $name=$item['Name'];
                                $id=$item['Item_ID'];
                                if($id==$row['item_id'])
                                    echo "<option value=$id checked>$name</option";
                                else   
                                    echo "<option value=$id checked>$name</option";
                            }
                        ?>
                    </select> 
                </div>
                
                <button type="submit" class="btn btn-primary">Edit</button>
                </form>
            </div>
            </div>
        </div>
            
        <?php
    }else if($do== 'update') {
        include('includes/navbar.php');
        if($_SERVER['REQUEST_METHOD']=='POST'){
            //validation in the data
                $comment=validString($_POST['comment']);
                
                if(!validString($comment)){
                $editErorr[]="The name must be not empty";
                }
            
                if(empty($editErorr)){
                    $stmt=$conn->prepare('UPDATE comment SET comment= ?,email=?,fullName=?,password=? WHERE userID=?');
                    $stmt->execute([$username,$email,$fullname,$password,$id]);
                    
                    redirect("back","<div class='alert alert-success'>Updated Successfully</div>");
                }
        }
        printErorrs($editErorr);
        
        
    }else if($do== 'active') {
        
  if(!isset($_GET['id'])){
    header('location:home.php');
   }
   $id=$_GET['id'];
   $row=checkInDatabase(['Comment_ID'],[$_GET['id']],'comment');
   if(empty($row))header('location:home.php');
   else{
     $stmt=$conn->prepare("UPDATE comment SET status='1' WHERE Comment_ID=?");
     $stmt->execute([$id]);
     redirect('back',"<div class='alert alert-success'>active successfully</div>");
   }
    }
    else if($do=='delete'){
        echo "kl;h";
        if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
          header('location:comment.php');
        }
        $id=$_GET['id'];
        if(!searchID_in_database($id,'comment','Comment_ID')){
          redirectHomeWithErorr('can not delete because this id is not found',5,'Comment.php');
        }
        $stmt=$conn->prepare("DELETE FROM comment WHERE Comment_ID=?");
        $stmt->execute([$id]);
        redirectHomeWithSuccess('Deleted Successfully',3,'Comment.php.php');
        
      }
 ?>