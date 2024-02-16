<?php
session_start();
if(!isset($_SESSION['name'])){
    header('location:index.php');
    exit;
}
$titilePage='Categories';
include('init.php');
$do=(isset($_GET['do']))?$_GET['do']:'manage';
if($do=='manage'){
  $sort='ASC';
  if(isset($_GET['sort'])){
    $sort=$_GET['sort'];
    $sort=strtoupper($sort);
    if($sort!='DESC')$sort='ASC';
  }
 $allCategory= fetch_all_data_in_table('category','Ordering',$sort);
 print_r($allCategory);
 echo "<center><h1>Manage Category</h1></center>";?>
 <div class="ordering text-right">Ordering by 
  <a href='?sort=ASC' class ="<?php if($sort=='ASC')echo 'active'; ?>">ASC</a>
  <a href='?sort=DESC' class ="<?php  if($sort=='DESC')echo 'active'; ?>">DESC</a>
 </div>
 <?php

 foreach($allCategory as $category){
 
  foreach($category as $key=>$value){
    if($key=='Name')echo"<h3>$value</h3>";
    if($key=='Description'){if(empty($value))echo "<p>The description is empty</p>";else echo "<p>$value</p>";}
    if($key=='Visibility'){if($value==1)echo "<span class='Visibility'>Hidden</span>";}
    if($key=='Allow_comment'){if($value==1)echo "<span class='Allow_comment'>Comment Disable</span>";}
    if($key=='Allow_adv'){if($value==1)echo "<span class='Allow_adv'>advertise Disable</span>";}
    if($key=='ID')$id=$value;
  }
    echo"<a href = '?do=delete&id=$id' class='btn btn-danger btn-sm'>Delete</a>";
    echo"<a href = '?do=edit&id=$id' class='btn btn-primary btn-sm'>Edit</a>";
    
 }?>
 <br>
 <br>
 <a href='members.php?do=add' class="btn btn-primary">Add new member</a>
 <?php
}
else if($do=='add'){
    //page to add new category
?>
    
  <div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-4 col-md-6 col-sm-8">
      <h2 class="text-center mt-5">Add Category</h2>
      <form action ='?do=insert' method='POST'>
        <!-- div for add name category -->
        <div class="form-group">
          <label for="username">Name</label>
          <input type="text" name='name' required ='required'class="form-control" id="username" placeholder="Enter category name">
        </div>
        <!-- div for add description category -->
        <div class="form-group">
          <label for="Description">Description</label>
          <input type="text" name='description'   class="form-control" id="Description" placeholder="Enter Description">
        </div>
        <div class="form-group">
          <label for="email">Ordering</label>
          <input type="text" name='ordering'   class="form-control" id="ordering" placeholder="Enter the ordering">
        </div>
        <!-- div for add visible category or not-->
        <div>
        <label>visible</label>
            <div>
               
                <input type="radio" name='visible' id='visible-yes' value=1>
                <label for='visible-yes'>Yes</label>
                <br>
                <input type="radio" name='visible' id='visible' value='0'>
                <label for='visible'>No</label>
            </div>
        </div>
        <!-- div for add comment category or not-->
        <div>
            <label>Allow Comment</label>
            <div>
                
                <input type="radio" name='comment' id='comment-yes' value="1">
                <label for='comment-yes'>Yes</label>
                <br>
                <input type="radio" name='comment' id='comment-no' value='0'>
                <label for='comment-no'>No</label>
            </div>
        </div>
        <!-- div for add Advertise category or not-->
        <div>
        <label>Allow Advertise</label>
            <div>
                <input type="radio" name='adv' id='adv-yes' value="1">
                <label for='adv-yes'>Yes</label>
                <br>
                <input type="radio" name='adv' id='adv-no' value='0'>
                <label for='adv-no'>No</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add category</button>
      </form>
    </div>
   </div>
  </div>
<?php
}
else if($do=='insert'){
    
  $insertErrors=[];
  $titilePage='Insert Page';
  include('includes/header.php');
  //Check if send form or not 
  if($_SERVER['REQUEST_METHOD']=='POST'){
    //validation of the values of form
    echo "post sucess<br>";
    
    $insert_category=[];
    $insert_category['Name']=validString_2($_POST['name']);
    if(isset($_POST['visible']))
    $insert_category['Visibility']=validString_2($_POST['visible']);
    if(isset($_POST['description']))
    $insert_category['Description']=validString_2($_POST['description']);
    if(isset($_POST['comment']))
    $insert_category['Allow_comment']=validString_2($_POST['comment']);
    if(isset($_POST['adv']))
    $insert_category['Allow_adv']=validString_2($_POST['adv']);
    if(isset($_POST['ordering']))
    $insert_category['Ordering']=validString_2($_POST['ordering']);
    if(validString($insert_category['Name'],'name')){
      $insertErrors[]=validString($insert_category['Name'],'name');
    }
    echo "visible".$_POST['visible']."<br>";
    //make an array for field and value of this field
    $field=[];
    $arr_value=[];
    $sql="INSERT INTO category (";
    foreach($insert_category as $key=>$value){
        if(empty($value))continue;
        if($key!='Name'&&$key!='Description'){
            if($value!=0&&$value!=1){
              echo "val".$value;
                $insertErrors[]='The value of '.$key.' must be equal zero or one';
                continue;
            }  
        }
        $field[]=$key;
        $arr_value[]=$value; 
        $sql.=$key.',';
    }
    echo $sql."<br>";
    $sql=substr($sql,0,-1).') values (';
    $values=str_repeat("?,",sizeof($field));
    $sql.= substr($values,0,-1).')';
    echo $sql."<br>";
   if(empty($insertErrors)){
    //search about name is found or not in the database 
    //if it found redirect to home page
   
    $row=checkInDatabase(['Name'],[ $insert_category['Name']],'category');
    
    echo "<pre>";
    print_r($row);
    echo "<pre>";
    
    if(!empty($row)){
      redirect('back',"<div class ='alert alert-danger'>'This username is already found'</div>");
    }
    //if it not found make insert
    $stmt=$conn->prepare("$sql");
    $stmt->execute($arr_value);
    redirectHomeWithSuccess('Insert success');
   }
   else{
    //if the data is not valid
    printErorrs($insertErrors);
    header('refresh:5;url=Category.php?do=add');
   }
  }
  else{
    //if the page is opened directly
   redirectHomeWithErorr('You can not go to this page directly');
  }
}
else if($do=='edit'){
  
  $id=$_GET['id'];
  if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
    redirectHomeWithErorr('ID must be numeric');
  }
  $id=$_GET['id'];
  if(!searchID_in_database($id,'category')){
    redirectHomeWithErorr('can not Edit because this id is not found');
  }
  $row=checkInDatabase(['ID'],[$id],'category')[0];
  ?>
  
<div class="container">
<div class="row justify-content-center">
  <div class="col-lg-4 col-md-6 col-sm-8">
    <h2 class="text-center mt-5">Edit Admin</h2>
    <form action ="?do=update&id=<?=$id?>" method='POST'>
      <div class="form-group">
        <label for="username">Name</label>
        <input type="text" name='Name'value='<?= $row['Name']?>' class="form-control" id="username" placeholder="Enter a new Name">
      </div>
       <div class="form-group">
        <label for="Description">Description</label>
        <input type="text"  name='Description' value='<?= $row['Description']?>' class="form-control" id="Description" placeholder="Enter the Description">
      </div>
      <div class="form-group">
          <label for="email">Ordering</label>
          <input type="text" name='Ordering' value='<?= $row['Ordering']?>'  class="form-control" id="ordering" placeholder="Enter the ordering">
        </div>
        <!-- div for add visible category or not-->
        <div>
        <label>visible</label>
            <div>
               
                <input type="radio" name='Visibility' id='visible-yes' value=1 <?php if( $row['Visibility']) echo 'checked';?>>
                <label for='visible-yes'>Yes</label>
                <br>
                <input type="radio" name='Visibility' id='visible' value='0' <?php if( !$row['Visibility']) echo 'checked';?>>
                <label for='visible'>No</label>
            </div>
        </div>
        <!-- div for add comment category or not-->
        <div>
            <label>Allow Comment</label>
            <div>
                
                <input type="radio" name='Allow_comment' id='comment-yes' value="1" <?php if( $row['Allow_comment']) echo 'checked';?>>
                <label for='comment-yes'>Yes</label>
                <br>
                <input type="radio" name='Allow_comment' id='comment-no' value='0' <?php if( !$row['Allow_comment']) echo 'checked';?>>
                <label for='comment-no'>No</label>
            </div>
        </div>
        <!-- div for add Advertise category or not-->
        <div>
        <label>Allow Advertise</label>
            <div>
                <input type="radio" name='Allow_adv' id='adv-yes' value="1" <?php if( $row['Allow_adv']) echo 'checked';?>>
                <label for='adv-yes'>Yes</label>
                <br>
                <input type="radio" name='Allow_adv' id='adv-no' value='0' <?php if( !$row['Allow_adv']) echo 'checked';?>>
                <label for='adv-no'>No</label>
            </div>
        </div>
      <button type="submit" class="btn btn-primary">Edit</button>
    </form>
  </div>
</div>
</div>
  
<?php
 
   
}
else if($do=='update'){
  if(!isset($_POST)){
    redirectHomeWithErorr('can not open this page direct');
  }
  $error_edit='';
  $id=$_GET['id'];
  $name=$_POST['Name'];
  $name=validString_2($_POST['Name']);
  $_POST['Description']=validString_2($_POST['Description']);
 
  if(empty($name)){
    $error_edit='The name is required';
  }
  if(!empty($error_edit)){
    redirect('back',"<div class ='alert alert-danger'> $error_edit</div>");
  }
  $sql="UPDATE category SET ";
  foreach($_POST as $key=>$value){
    if(empty($value))continue;
    $sql.=$key."='".$value."',";
  }
  if($sql[-1]==',');
  $sql=substr($sql,0,-1);
  $sql.=" WHERE ID=?";
  $stmt=$conn->prepare($sql);
  $stmt->execute([$id]);
 redirectHomeWithSuccess('Updated successfully',3,'category.php');
}
else if($do=='delete'){
  if(!isset($_GET['id'])||!is_numeric($_GET['id'])){
    redirectHomeWithErorr('ID must be numeric');
  }
  $id=$_GET['id'];
  if(!searchID_in_database($id,'category')){
    redirectHomeWithErorr('can not delete because this id is not found');
  }
  $stmt=$conn->prepare("DELETE FROM category WHERE ID=?");
  $stmt->execute([$id]);
  redirectHomeWithSuccess('Deleted Successfully',3,'category.php');
  
}
?>
<?php
include('includes/footer.php');
?>