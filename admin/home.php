<?php

session_start();
$titilePage='Home';
if(!isset($_SESSION['name'])){
    header("location:login.php");
}
include ("init.php"); 
include("includes/header.php");

include("includes/navbar.php");
$latestMembers=getLatest("*",'userID','users');
$latest_items=getLatest("*",'Item_ID','items');
?>
<div class="container">
    <div class="row text-center">
      <div class="col">
        <div class="title-container">
          <div class="title">
            Total Members 
            <p><a href='members.php'><?=countItem('userID','users');?></a></p>
          </div>
          <div class="title">
            Pending Members 
        
            <p><a href="members.php?do=pending"><?=countItem('regStatus','users','0');?></a></p>
          </div>
          <div class="title">
            Total Items 
            <p><a href='Item.php'><?=countItem('Item_ID','items');?></a></p>
          </div>
          <div class="title">
            Total Comments 
            <p><a href='Comment.php'><?=countItem('Comment_ID','Comment');?></a></p>
          </div>
        </div>
      </div>
    </div>
    <br>
    <br>
    <label  style="color:blue;"for="users">Latest Register Users </label>
    <p><?php 
    foreach($latestMembers as $member):
        $id=$member['userID'];
        $username=$member['username'];
         echo "<label >$username</label>";
        
         echo "<a href='members.php?do=edit&id=$id' class='btn btn-primary btn-sm'>Edit</a>";
         if($member['regStatus']==0):
            echo "<a href='members.php?do=active&id=$id' class='btn btn-secondary btn-sm'>Active</a>";
            endif;
            echo "<br>";
    endforeach;
        ?></p>
    <br>
    <label  style="color:blue;"for="users">Latest Items </label>
    <p><?php 
    foreach($latest_items as $item):
        $id=$item['Item_ID'];
        $name_item=$item['Name'];
         echo "<label >$name_item</label>";
        
         echo "<a href='item.php?do=edit&id=$id' class='btn btn-primary btn-sm'>Edit</a>";
         if($item['Approve']==0):
            echo "<a href='item.php?do=approve&id=$id' class='btn btn-secondary btn-sm'>Approve</a>";
            endif;
            echo "<br>";
    endforeach;
        ?></p>
    <br>
    <label  style="color:blue;"for="users">Latest Comments </label>
    <?php
      //to get the latest five comments and name of their users
      $stmt2=$conn->prepare("select user_id,comment,username from comment join users where user_id=userID");
      $stmt2->execute([]);
      $comments=$stmt2->fetchAll();
     
      foreach($comments as $comment):
    
    $member_id=$comment['user_id'];
    ?>
    <div class="comment-container">
    <a href="members.php?do=edit&id=<?=$member_id;?>" target="_blank" rel="noopener noreferrer"><p class="user-name"><?=$comment['username'];?></p></a>
    <p><?=$comment['comment']?></p>
    </div>

   

    <?php 
    endforeach;
      
      
    
    ?>
  </div>

<?php
 include('includes/footer.php');?>
  
    