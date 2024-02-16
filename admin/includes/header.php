
<!DOCTYPE html>
<html>
<head>
  <title><?php getTitile();?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    .title-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 100px;
     
      
    }
    
    .title {
      font-size: 20px;
      font-weight: bold;
      background-color: burlywood;
    }
    label {
        width: 150px;
        
    }
    a{
      margin-right: 5px;
    }
    .Visibility{
      background-color: greenyellow;
      padding: 5px;
      border-radius: 10px;
      margin-right: 5px;
    }
    .Allow_comment{
      background-color: gainsboro;
      padding: 5px;
      border-radius: 10px;
      margin-right: 5px;
    }
    .active{
      color: red;
    }
    .Allow_adv{
      background-color: blueviolet;
      padding: 5px;
      border-radius: 10px;
      margin-right: 5px;
    }
    
        /* Custom CSS for comment styling */
        .comment-container {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .user-name {
            font-weight: bold;
            color: #007bff; /* Bootstrap primary color */
        }
   
  </style>
</head>
<body>