<?php
session_start();
require_once "db/pdo.php";
require_once "util.php";
flashMessages();

if(isset($_POST['add'])){
  if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
    if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1){
      $_SESSION['error'] = "All values are required";
      header("Location: add.php");
      return;
    } else {
      $_SESSION['first_name'] = $_POST['first_name'];
      $_SESSION['last_name'] = $_POST['last_name'];
      $_SESSION['email'] = $_POST['email'];
      $_SESSION['headline'] = $_POST['headline'];
      $_SESSION['summary'] = $_POST['summary'];
    }
  }



  for($i=1; $i<=9; $i++){
    if(isset($_POST['year'.$i]) && isset($_POST['desc'.$i])){
      if(strlen($_POST['year'.$i])<1 || strlen($_POST['desc'.$i])<1){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        return;
      } else {
        if(filter_var($_POST['year'.$i], FILTER_VALIDATE_INT)){
          $_SESSION['year'.$i] = $_POST['year'.$i];
          $_SESSION['desc'.$i] = $_POST['desc'.$i];
          $_SESSION['add'.$i] = "Set";
        } else {
          $_SESSION['error'] = "Year has to be integer";
          header("Location: add.php");
          return;
        }
      }
    }
  }
  $_SESSION['add'] = "Set";
  header("Location: add.php");
  return;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Add Profile</title>
    <script src="jquery-3.5.1.min.js"></script>
  </head>
  <body>
    <form method="post">
      <label for="first_name">First Name</label>
      <input type="text" name="first_name" id="fn">
      <br><br>
      <label for="last_name">Last Name</label>
      <input type="text" name="last_name" id="ln">
      <br><br>
      <label for="email">Email</label>
      <input type="text" name="email" id="em">
      <br><br>
      <label for="headline">Headline</label>
      <input type="text" name="headline" id="hl">
      <br><br>
      <label for="summary">Summary</label>
      <input type="text" name="summary" id="su">
      <br><br>
      <input type="submit" id="addPos" value="+">
      <br><br>
      <div id="position_fields">
      </div>
      <br>
      <input type="submit" name="add" value="Add">
      <a href="index.php">Cancel</a>
    </form>
    <script type="text/javascript">
      countPos = 0;

      $(document).ready(function(){
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event){
          event.preventDefault();
          if(countPos >= 9){
            alert("Maximum of nine position entries allowed");
            return;
          }
          countPos++;
          window.console && console.log("Adding position "+countPos);
          $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="int" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
              onclick="$(\'#position'+countPos+'\').remove(); return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
        })
      })
    </script>
    <?php
    if(isset($_SESSION['add'])){
      // Adding to profile table
      $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
      VALUES ( :uid, :fn, :lastName, :em, :he, :su)');

      $stmt->execute(array(
        ':uid' => htmlentities($_SESSION['user_id']),
        ':fn' => htmlentities($_SESSION['first_name']),
        ':lastName' => htmlentities($_SESSION['last_name']),
        ':em' => htmlentities($_SESSION['email']),
        ':he' => htmlentities($_SESSION['headline']),
        ':su' => htmlentities($_SESSION['summary'])
      ));
      unset($_SESSION['add']);
      $_SESSION['success'] = "Succesfully added";

      $profile_id = $pdo->lastInsertId();

      // Adding to positions table
      for($i=1; $i<=9; $i++){
        $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description)
        VALUES (:pid, :rnk, :year, :description)');

        if(isset($_SESSION['add'.$i])) {
          $stmt->execute(array(
            ':pid' => $profile_id,
            ':rnk' => $i,
            ':year' => htmlentities($_SESSION['year'.$i]),
            ':description' => htmlentities($_SESSION['desc'.$i])
          ));
          unset($_SESSION['add'.$i]);
        } else {
          continue;
        }
      }
      header("Location: index.php");
      return;
    }
    ?>
  </body>
</html>
