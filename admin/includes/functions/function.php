<?php
function getTitile(){
    global $titilePage;
    if(isset($titilePage)){
        echo $titilePage;
    }
    else{
        echo 'default';
    }
}
//return valid string or false if empty string
function validString($str){
    $str=trim($str);
    $str=filter_var($str,FILTER_SANITIZE_STRING);
    if($str)return $str;
    else
    return false;
}
//vergion 2
function validString_2($str){
    $str=trim($str);
    $str=filter_var($str,FILTER_SANITIZE_STRING);
   return $str;
    
}
function minLingth($min,$len){
    if($len<$min)return false;
    return true;
}
function validEmail($email){
    $email=trim($email);
    $email=filter_var($email,FILTER_SANITIZE_EMAIL);
    $email=filter_var($email, FILTER_VALIDATE_EMAIL);
    if(!$email)return false;
    else
    return true;
}
function searchID_in_database($id,$tableName,$column_name='ID'){
    global $conn;
    $stmt=$conn->prepare(" SELECT * FROM $tableName WHERE $column_name=?");
    $stmt->execute([$id]);
    $count=$stmt->rowCount();
    
    if($count>0)return true;
    return false;
}
function redirectHomeWithErorr($errorMessesge,$seconds=5,$path='home.php'){
    echo "<div class ='alert alert-danger'>$errorMessesge</div>";
    header("refresh:$seconds;url=$path");
    exit();
}
function redirectHomeWithSuccess($SuccessMessesge,$seconds=5,$path='home.php'){
    echo "<div class='alert alert-success'>$SuccessMessesge</div>";

    header("refresh:$seconds;url=$path");
    exit();
}
//redirect to another page
//url ...the url of  the page to transfare 
function redirect($url,$action,$second=6){
 echo "$action";
 $url=$_SERVER['HTTP_REFERER'];
 header("refresh:$second;url=$url");
 exit;
}
function printErorrs($erorrs,$direct='the same page'){
    
    foreach($erorrs as $error){
        echo "<div class ='alert alert-danger'>$error</div>";
    }
    if($direct!='the same page'){
        $url=$_SERVER['HTTP_REFERER'];

        header("refresh:5;url=$url");
    }
    return;
}
//function to check the user is aready found in database or not if found return it as array with condition
//function accept parameter 
//$select ....field in the table 
//$from ....the name of table 
//$value the name of field 
//return 2D array 

function checkInDatabase($select ,$value,$from){
    global $conn;
    $sql="SELECT * FROM $from  WHERE ";
    foreach ($select as $selected){
        $sql.=$selected.'=? AND ';
    }
    $sql=substr($sql,0,-4);
    $stmt=$conn->prepare("$sql");
    $stmt->execute($value);
    $count=$stmt->rowCount();
    if($count)return $stmt->fetchAll(PDO::FETCH_ASSOC);
    return 0;

}
function fetch_all_data_in_table($tableName,$field,$sort='ASC'){
    global $conn;
    
    $stmt=$conn->prepare("SELECT *FROM $tableName order by $field $sort");
    $stmt->execute();
    return $stmt->fetchAll();
}
//count item in database
//item...the column name that count it
//table ..table name
//main condition is not admin ...then groubID=0
function countItem($item,$table){
    global $conn;
    
    $stmt=$conn->prepare("SELECT count($item) FROM  $table ");
    $stmt->execute();
    
   
    return $stmt->fetchColumn();
}
/*
function to get latest number of items 
order...the order will be according it
table..table name
select ..items to select it
*/
function getLatest($select,$order,$table,$limit=5){
global $conn;
$stmt=$conn->prepare("SELECT $select FROM $table ORDER BY  $order DESC LIMIT $limit");
$stmt->execute();
$row=$stmt->fetchAll();
return $row;
}
function get_all_rows($tableName){
    global $conn;
    $stmt=$conn->prepare("select * from $tableName");
    $stmt->execute();
    $row=$stmt->fetchAll();
    return $row;
}
?>