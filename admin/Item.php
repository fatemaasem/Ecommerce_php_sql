<?php
session_start();
$titilePage='Item';
include('init.php');

if(!isset($_SESSION['name'])){
    header('location:login.php');
    exit;
}
$do='';
if(isset($_GET['do']))$do=$_GET['do'];
else $do='manage';
if($do=='manage'){
  include("includes/navbar.php");
  $allItems=[];
  $stmt=$conn->prepare("SELECT * FROM items ");
  $stmt->execute();
  $allItems=$stmt->fetchAll(PDO::FETCH_ASSOC);
 ?>
 <table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Price</th>
      <th>Date</th>
      <th>Country Made</th>
      <th>Status</th>
      <th>Rating</th>
      <th>Category name</th>
      <th>Member Name</th>
    </tr>
  </thead>
  <?php
  
  foreach($allItems as $item):
    
    $categry_name='not found';
    $member_name='not found';

    $approve=$item['Approve'];
    
    if($item['Cat_ID']!=null){
       
        $stmt=$conn->prepare("SELECT * FROM category WHERE ID=? ");
        $stmt->execute([$item['Cat_ID']]);
        $category=$stmt->fetch();
        $categry_name=$category['Name'];
        
        
    }
    if($item['Member_ID']!=null){
        $stmt=$conn->prepare("SELECT * FROM users WHERE userID=? ");
        $stmt->execute([$item['Member_ID']]);
        $member=$stmt->fetch();
        $member_name=$member['username'];
    }
  ?>
  <tr>
    
      <td><?=$item['Item_ID'];?></td>
      <td><?=$item['Name']?></td>
      <td><?=$item['Description']?></td>
      <td><?=$item['Price']?></td>
      <td><?= $item['Add_Date']?></td>
      <td><?= $item['Country_Made']?></td>
      <td><?php echo (!$item['Status'])? "not found":$item['Status'];?></td>
      <td><?php echo (!$item['Rating'])? "not found":$item['Rating']; ?></td>
      <td><?= $categry_name?></td>
      <td><?=  $member_name?></td>
      <td>
        <a href = "?do=delete&id=<?=$item['Item_ID']?>"class="btn btn-danger btn-sm">Delete</a>
        <a href="?do=edit&id=<?=$item['Item_ID']?>" class="btn btn-primary btn-sm">Edit</a>
        <?php if($approve==0):?>
         <a href="?do=approve&id=<?=$item['Item_ID']?>" class="btn btn-secondary btn-sm">Approve</a>
         <?php endif;?>
      </td>
    </tr>
  <?php endforeach;?>
  
</table>
 <hr>
 <hr>
 <a href='Item.php?do=add' class="btn btn-primary">Add new Item</a>
 <?php

}
else if($do=='add'){
    include ("includes/navbar.php");


    ?>
    
  <div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-4 col-md-6 col-sm-8">
      <h2 class="text-center mt-5">Add Item</h2>
      <form action ='?do=insert' method='POST'>
        <!-- div for add name category -->
        <div class="form-group">
          <label for="username">Name</label>
          <input type="text" name='name' required ='required'class="form-control" id="username" placeholder="Enter item name">
        </div>
        <!-- div for add description category -->
        <div class="form-group">
          <label for="Description">Description</label>
          <input type="text" name='description'   class="form-control" id="Description" placeholder="Enter Description">
        </div>
        <div class="form-group">
          <label for='text'>Price</label>
          <input type="text" name='price'   required ='required' class="form-control" id="text" placeholder="Enter the price">
        </div>
        <div class="form-group">
          <label for='country'>Country</label>
          <input type="text" name='country' required ='required'   class="form-control" id="country" placeholder="Enter the country">
        </div>
        <div class="form-group">
          <label for='status'>Status</label>
          <select name='status'>
            <option value='0'>.....</option>
            <option value='New'>New</option>
            <option value='Like New'>Like New</option>
            <option value='Used'>Used</option>
            <option value='Very Old'>Very Old</option>
          </select>
        </div>
        <div class="form-group">
          <label >Rating</label>
          <select name='rating'>
            <option value='0'>.....</option>
            <option value='1'>*</option>
            <option value='2'>**</option>
            <option value='3'>***</option>
            <option value='4'>****</option>
            <option value='5'>*****</option>
          </select>
        </div>

         <div class="form-group">
          <label >Members</label>
          <select name='memberID'>
          <option value='0'>.....</option>
            <?php
            $stmt=$conn->prepare("SELECT *FROM users");
            $stmt->execute();
            $members=$stmt->fetchAll();
           
            foreach($members as $member){
               $id=$member['userID'];
               $name=$member['username'];
               echo "<option value='$id'>$name</option>";
            }
             ?>
          </select>
        </div>
        <div class="form-group">
          <label >Category</label>
          <select name='categoryID'>
          <option value='0'>.....</option>
            <?php
            $stmt=$conn->prepare("SELECT *FROM category");
            $stmt->execute();
            $categories=$stmt->fetchAll();
           
            foreach($categories as $category){
               $id=$category['ID'];
               $name=$category['Name'];
               echo "<option value='$id'>$name</option>";
            }
             ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Item</button>
      </form>
    </div>
   </div>
  </div>
<?php
}
else if($do=='insert'){
  
    $error_insert=[];
    if(isset($_POST)){
        $name=validString_2($_POST['name']);
        $price=validString_2($_POST['price']);
        $country=validString_2($_POST['country']);
        echo var_dump($_POST['description'])."<br>";
        $description=validString_2($_POST['description']);
        echo var_dump($description)."<br>";

        $status=$_POST['status'];
        $rating=$_POST['rating'];
        $memberID=$_POST['memberID'];
        $catID=$_POST['categoryID'];
        
        foreach($_POST as $key=>$value){
            $_POST[$key]=validString_2($value);
        }
        if(empty($name)){
            $error_insert[]='The name can not empty';
        }
        if(empty($price)){
            $error_insert[]='The price can not empty';
        }
        if(empty($country)){
            $error_insert[]='The country can not empty';
        }
        
        if($status==0){
          $memberID=null;
        }
        if( $memberID==0){
            $memberID=null;
        }
        if( $catID==0){
            $catID=null;
        }
       
        if(empty($error_insert)){
            $stmt=$conn->prepare("INSERT INTO items (Name,Description,Price,Country_Made,Status,Rating,Member_ID,Cat_ID) VALUES(?,?,?,?,?,?,?,?)");
            $stmt->execute([$name,$description,$price,$country,$status,$rating,$memberID,$catID]);
            echo var_dump($description);
            redirect('back',"<div class='alert alert-success'>Insert Success</div>",7 );
        }
        else{
           
            redirect('back', printErorrs($error_insert));
        }
    }
    else{
        redirectHomeWithErorr('This page can not open direct');
        
    }
}
else if($do=='edit'){
    
  $id=$_GET['id'];
  if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
    redirectHomeWithErorr('ID must be numeric');
  }
  $id=$_GET['id'];
  if(!searchID_in_database($id,'items','Item_ID')){
    redirectHomeWithErorr('can not Edit because this id is not found');
  }
  $row=checkInDatabase(['Item_ID'],[$id],'items')[0];
  $comments=[];
  $stmt2=$conn->prepare("select Comment_ID,comment.comment,comment_date ,username from comment join users where comment.user_id=users.userID and comment.item_id=?");
  $stmt2->execute([$id]);
  $comments=$stmt2->fetchAll();
 
  ?>
  
<div class="container">
<div class="row justify-content-center">
  <div class="col-lg-4 col-md-6 col-sm-8">
    <h2 class="text-center mt-5">Edit Item</h2>
    <form action ="?do=update&id=<?=$id?>" method='POST'>
      <div class="form-group">
        <label for="username">Name</label>
        <input type="text" name='Name'value='<?= $row['Name']?>' class="form-control" id="username" placeholder="Enter a new Name">
      </div>
       <div class="form-group">
        <label for="Description">Description</label>
        <input type="text"  name='description' value='<?= $row['Description']?>' class="form-control" id="Description" placeholder="Enter the Description">
      </div>
      <div class="form-group">
          <label for="price">Price</label>
          <input type="text" name='price' value='<?= $row['Price']?>'  class="form-control" id="Price" placeholder="Enter the price">
        </div>
        <!-- div for add visible category or not-->
        <div>
        <label for="country">Country</label>
        <input type="text" name='country' value='<?= $row['Country_Made']?>'  class="form-control" id="country" placeholder="Enter the country">
        </div>
        <!-- div for add comment category or not-->
        <div class="form-group">
          <label for='status'>Status</label>
          <select name='status'>
            <option value='0' <?php if (!$row['Status']) echo 'selected';?>>.....</option>
            <option value='New' <?php if ($row['Status']=='New') echo 'selected';?>>New</option>
            <option value='Like New' <?php if ($row['Status']=='Like New') echo 'selected';?>>Like New</option>
            <option value='Used' <?php if ($row['Status']=='Used') echo 'selected';?>>Used</option>
            <option value='Very Old' <?php if ($row['Status']=='Very Old') echo 'selected';?>>Very Old</option>
          </select>
        </div>
        <div class="form-group">
          <label >Rating</label>
          <select name='rating'>
            <option value='0' <?php if (!$row['Rating']) echo 'selected';?>>.....</option>
            <option value='1' <?php if ($row['Rating']==1) echo 'selected';?>>*</option>
            <option value='2' <?php if ($row['Rating']==2) echo 'selected';?>>**</option>
            <option value='3' <?php if ($row['Rating']==3) echo 'selected';?>>***</option>
            <option value='4' <?php if ($row['Rating']==4) echo 'selected';?>>****</option>
            <option value='5' <?php if ($row['Rating']==5) echo 'selected';?>>*****</option>
          </select>
        </div>
        <div class="form-group">
          <label >Members</label>
          <select name='memberID'>
          <option value='0' <?php if (!$row['Member_ID']) echo 'selected';?>>.....</option>
            <?php
            $stmt=$conn->prepare("SELECT *FROM users");
            $stmt->execute();
            $members=$stmt->fetchAll();
           
            foreach($members as $member){
               $id=$member['userID'];
               $name=$member['username'];
               if($row['Member_ID']==$id)
               echo "<option value='$id' selected>$name</option>";
              else 
                echo "<option value='$id' >$name</option>";
            }
             ?>
          </select>
        </div>
          <div class="form-group">
          <label >Category</label>
         
          <select name='categoryID'>
            <option value='0' <?php if (!$row['Cat_ID']) echo 'selected';?>>.....</option>
            <?php
            $stmt=$conn->prepare("SELECT *FROM category");
            $stmt->execute();
            $categories=$stmt->fetchAll();
           
            foreach($categories as $category){
               $id=$category['ID'];
               $name=$category['Name'];
               if($row['Cat_ID']==$id)
                echo "<option value='$id' selected>$name</option>";
               else 
                 echo "<option value='$id' >$name</option>";
            }
             ?>
          </select>
        </div>
      <button type="submit" class="btn btn-primary">Edit</button>
    </form>
  </div>
</div>
</div>

        <!-- make table to view all comments -->
        <center>
        <h3><?=$row['Name'] ?>Management<h3>
        </center>
        <table class="table">
            <thead>
                <tr>
                
                <th>Comment</th>
                <th>User Name</th>
                <th>Added Date</th>
                <th>Control</th>
                </tr>
            </thead>
        
            <?php
                foreach($comments as $comment):
                ?>
            <tr>
                
                <td><?=$comment['comment']?></td>
                <td><?= $comment['username']?></td>
               
                <td><?= $comment['comment_date']?></td>
                <td>
                    <a href = "Comment.php?do=delete&id=<?=$comment['Comment_ID']?>"class="btn btn-danger btn-sm">Delete</a>
                    <a href="Comment.php?do=edit&id=<?=$comment['Comment_ID']?>" class="btn btn-primary btn-sm">Edit</a>
                  
                </td>
            </tr>
            <?php endforeach;?>                                                   
  
        </table>
        
<?php
 echo "<pre>";
 print_r($comments);
 echo "</pre>";
}
else if($do=='update'){
    if(!isset($_POST)){
        redirectHomeWithErorr('can not open this page direct');
      }
      $error_edit='';
      $id=$_GET['id'];
      $name=$_POST['Name'];
      $name=validString_2($_POST['Name']);
      $description=validString_2($_POST['description']);
      $price=validString_2($_POST['price']);
      $country=validString_2($_POST['country']);
      $status=$_POST['status'];
      if($status==0)$status=null;
      $rating=$_POST['rating'];
      if($rating==0)$rating=null;
      $cat_id=$_POST['categoryID'];
      if(!$cat_id)$cat_id=null;
      $mem_id=$_POST['memberID'];
      if(!$mem_id)$mem_id=null;
      if(empty($name)){
        $error_edit='The name is required';
      }
      if(!empty($error_edit)){
        redirect('back',"<div class ='alert alert-danger'> $error_edit</div>");
      }
      $sql="UPDATE items SET Name=?,Description=?,Price=?,Country_Made=?,Status=?,Rating=?,Cat_ID=?,Member_Id=? WHERE Item_ID=$id ";
      
      $stmt=$conn->prepare($sql);
      $stmt->execute([$name,$description,$price,$country,$status,$rating,$cat_id,$mem_id]);
     redirectHomeWithSuccess('Updated successfully',3,'item.php');
    }
   
      

else if($do=='delete'){
  if(!isset($_GET['id'])||!is_numeric($_GET['id'])){echo "val";
    header('location:item.php');
  }
  $id=$_GET['id'];
  if(!searchID_in_database($id,'items','Item_ID')){
    redirectHomeWithErorr('can not delete because this id is not found',5,'item.php');
  }
  $stmt=$conn->prepare("DELETE FROM items WHERE Item_ID=?");
  $stmt->execute([$id]);
  redirectHomeWithSuccess('Deleted Successfully',3,'item.php');
  
}
else if($do=='approve'){
  if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
   redirectHomeWithErorr('Can not this file directly');
  }
  $id=$_GET['id'];
  $stmt=$conn->prepare('UPDATE items  SET Approve=1 WHERE Item_ID=?');
  $stmt->execute([$id]);
  redirect('back',"<div class='alert alert-success'>Approve success</div>",5);
}

?>