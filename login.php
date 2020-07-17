<?php
session_start();
require_once "db/pdo.php";
require_once "util.php";

flashMessages();

if(isset($_POST['email']) && isset($_POST['pass'])){
  $check = hash('md5', $salt.$_POST['pass']);
  $stmt = $pdo->prepare('SELECT user_id, name FROM users
  WHERE email = :em AND password = :pw');
  $stmt->execute(array(
    ':em' => $_POST['email'],
    ':pw' => $check));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ( $row !== false ) {
  $_SESSION['name'] = $row['name'];
  $_SESSION['user_id'] = $row['user_id'];
  $_SESSION['logged_in'] = "True";
  header("Location: index.php");
  return;
  } else {
    $_SESSION['error'] = "Incorrect username or password";
    header("Location: login.php");
    return;
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Rahul Raheja's Login Page</title>
  </head>
  <body>
    <h1>Please Log In</h1>
    <div class="container">
      <form method="post">
        <p>
          <label for="email">Email</label>
          <input type="text" name="email" id="email">
        </p>
        <p>
          <label for="pass">Password</label>
          <input type="password" name="pass" id="pass">
        </p>
          <input type="submit" onclick="return doValidate();" value="Log In">
          <a href="index.php">Cancel</a>
      </form>
    </div>
    <script type="text/javascript">
      function doValidate() {
        console.log('Validating...');
        try{
          addr = document.getElementById('email').value;
          pw = document.getElementById('pass').value;
          console.log("Validating addr="+addr+" pw="+pw);
          if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
          }
          if( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
          }
          return true;
        } catch(e) {
          return false;
        }
        return false;
      }
    </script>
  </body>
</html>
