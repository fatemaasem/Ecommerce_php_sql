<!DOCTYPE html>
<html>
<head>
  <title>Home Page</title>
  <!-- Include Bootstrap CSS -->
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
  </style>
</head>
<body>
  <div class="container">
    <div class="row text-center">
      <div class="col">
        <div class="title-container">
          <div class="title">
            Total Members 
            <p>ans</p>
          </div>
          <div class="title">
            Pending Members 
            <p>ans</p>
          </div>
          <div class="title">
            total Items 
            <p >ans</p>
          </div>
          <div class="title">
            Total Comments 
            <p >ans</p>
          </div>
        </div>
      </div>
    </div>
    <br>
    <br>
    <label for="users">Latest Register Users </label>
    <input type="text" id='users'>
    <br>
    <label for="users">Latest Items </label>
    <input type="text" id='users'>
  </div>
</body>
</html>