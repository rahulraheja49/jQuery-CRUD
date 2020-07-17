<script src="jquery-3.5.1.min.js"></script>

<?php
require_once "db/pdo.php";
require_once "util.php";
session_start();

flashMessages();

// This is to delete the entries before and make new entries
if(isset($_POST['save'])){
  // Deleting position entry
  $stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :id");
  $stmt->execute(array(
    ":id" => $_GET['profile_id']));


// Data validation for update
  if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
    if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1){
      $_SESSION['error'] = "All values are required";
      header("Location: edit.php?profile_id=".$_GET['profile_id']);
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
        header("Location: edit.php?profile_id=".$_GET['profile_id']);
        return;
      } else {
        $_SESSION['year'.$i] = $_POST['year'.$i];
        $_SESSION['desc'.$i] = $_POST['desc'.$i];
        $_SESSION['add'.$i] = "Set";
      }
    }
  }
  $_SESSION['update'] = "Set";
  header("Location: edit.php?profile_id=".$_GET['profile_id']);
  return;
}




// All the php after this point just loads the data from the tables
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :pid");
$stmt->execute(array(
  ":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
  $_SESSION['error'] = 'Bad value for profile_id';
  header( 'Location: index.php' ) ;
  return;
}
$fn = $row['first_name'];
$ln = $row['last_name'];
$em = $row['email'];
$hl = $row['headline'];
$su = $row['summary'];


$positions = array();
$stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :pid");
$stmt->execute(array(
  ":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
  ?>
  <script type="text/javascript">
      countPos = 0;
  </script>
  <?php
} else {
  $stmt = $pdo->prepare('SELECT * FROM position
    WHERE profile_id = :profile_id ORDER BY rank');
  $stmt->execute(array(
    ':profile_id' => $_GET['profile_id']
  ));
  $positions = array();
  while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
    $positions[] = $row;
  }
  if(count($positions) != 0){
      foreach($positions as $position){
        ?>
        <script type="text/javascript">
            $(document).ready(function(){
              $('#position_fields').append(
                '<div id="position'+countPos+'"> \
                <p>Year: <input type="int" name="year'+countPos+'" value="<?= $position['year']; ?>" /> \
                <input type="button" value="-" \
                  onclick="$(\'#position'+countPos+'\').remove(); return false;"></p> \
                <input type="text" name="desc'+countPos+'" value="<?= $position['description']; ?>">\
                </div>');
              });

        </script>
  <script type="text/javascript">
    countPos = <?= count($positions) ?>;
  </script>
  <?php
    }
  }
}


?>

<!DOCTYPE html>
<html>
  <head>
    <title>Rahul Raheja's Edit Page</title>
  </head>
  <body>
    <h1>Editing profiles for UMSI</h1>
    <div class="container">
      <form method="post">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" value="<?= $fn ?>" id="fn">
        <br><br>
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" value="<?= $ln ?>" id="ln">
        <br><br>
        <label for="email">Email</label>
        <input type="text" name="email" value="<?= $em ?>" id="em">
        <br><br>
        <label for="headline">Headline</label>
        <input type="text" name="headline" value="<?= $hl ?>" id="hl">
        <br><br>
        <label for="summary">Summary</label>
        <input type="text" name="summary" value="<?= $su ?>" id="su">
        <br><br>
        <input type="button" id="addPos" value="+">
        <br><br>
        <div id="position_fields">
        </div>
        <br>
        <input type="submit" name="save" value="Save">
        <a href="index.php">Cancel</a>
      </form>
    </div>
    <script type="text/javascript">
        countPos = <?= count($positions) ?>

        $(document).ready(function(){
          window.console && console.log('Document ready called');
          $('#addPos').click(function(event){
              event.preventDefault();
              if ( countPos >= 9 ) {
                  alert("Maximum of nine position entries exceeded");
                  return;
              }
              countPos++;
              window.console && console.log("Adding position "+countPos);
              $('#position_fields').append(
                  '<div id="position'+countPos+'"> \
                  <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                  <input type="button" value="-" \
                  onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                  <input type="text" name="desc'+countPos+'" >\
                  </div>');
          });
        });
    </script>
    <?php
    if(isset($_SESSION['update'])){
      // Adding to profile table
      $sql = "UPDATE profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary WHERE profile_id = :profile_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':first_name' => htmlentities($_SESSION['first_name']),
        ':last_name' => htmlentities($_SESSION['last_name']),
        ':email' => htmlentities($_SESSION['email']),
        ':headline' => htmlentities($_SESSION['headline']),
        ':summary' => htmlentities($_SESSION['summary']),
        ':profile_id' => htmlentities($_GET['profile_id'])
      ));
      $_SESSION['success'] = "Record updated";
      unset($_SESSION['update']);
      unset($_SESSION['first_name']);
      unset($_SESSION['last_name']);
      unset($_SESSION['email']);
      unset($_SESSION['headline']);
      unset($_SESSION['summary']);
      unset($_SESSION['profile_id']);


      // Adding to positions table
      for($i=1; $i<=9; $i++){

        if(isset($_SESSION['add'.$i])) {
          $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description)
          VALUES (:pid, :rnk, :year, :description)');
          $stmt->execute(array(
            ':pid' => $_GET['profile_id'],
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
