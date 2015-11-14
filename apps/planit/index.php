<?php
session_start();
?>
<html>
<head>
  
</head>
<body>
  <h1>GOT IT!!!!</h1>


   <?php if ($_SESSION['fb_id']): ?>
       <small><?php echo $_SESSION['fb_name']; ?></small><a href="logout.php"><button>Logout</button></a>
   <?php else: ?>
       <a href="config.php">Login</a>
   <?php endif ?>
</body>
</html>