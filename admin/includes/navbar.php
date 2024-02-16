<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="home.php">home</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="Category.php">Categories</a>
      </li>
     
      
      <li class="nav-item">
        <a class="nav-link" href="Item.php">Items</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Comment.php">Comments</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="members.php?do=manage">Members</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Statistics</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">logs</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">
         <?=$_SESSION['name']?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="members.php?do=edit">Edit Profile</a>
          <a class="dropdown-item" href="#">Option 2</a>
          <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
      </li>
      
    </ul>
  </div>
</nav>
