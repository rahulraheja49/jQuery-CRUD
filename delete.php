<?php
require_once "db/pdo.php";
session_start();

if( isset($_POST['delete']) && isset($_POST['profile_id']) ){
  $sql = "DELETE FROM profile WHERE profile_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":id" => $_POST['profile_id']));

  $stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :id");
  $stmt->execute(array(
    ":id" => $_POST['profile_id']));

  $_SESSION['success'] = 'Record deleted';
  header("Location: index.php");
  return;
}

if(!isset($_GET['profile_id'])) {
  $_SESSION['error'] = "Missing profile_id";
  header("Location: index.php");
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :profile_id");
$stmt->execute(array(
  ":profile_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row===false){
  $_SESSION['error'] = "Bad value for profile_id";
  header("Location: index.php");
  return;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Delete Page</title>
  </head>
  <body>
    <p>Confirm: Deleting <?= htmlentities($row['first_name']) ?></p>
    <form method="post">
      <input type="hidden" name="profile_id" value="<?=$row['profile_id'] ?>">
      <input type="submit" name="delete" value="Delete">
      <a href="index.php">Cancel</a>
    </form>
  </body>
</html>
