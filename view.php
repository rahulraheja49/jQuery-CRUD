<?php
session_start();
require_once "db/pdo.php";
require_once "util.php";

?>

<!DOCTYPE html>
<html>
  <head>
    <title>View Profile</title>
  </head>
  <body>
    <h1>Profile Information</h1>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :profile_id");
    $stmt->execute(array(
      ':profile_id' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>
      First Name: ".$row['first_name'].
    "</p>
    <p>
    Last Name: ".$row['last_name'].
    "</p>
    <p>
    Email: ".$row['email'].
    "</p>
    <p>
    Headline: ".$row['headline'].
    "</p>
    <p>
    Summary: ".$row['summary'].
    "</p>";
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
      echo "<p> Positions: </p>";
      print_r("<ul>");
      foreach($positions as $position){
        print_r("<li>".$position['year'].":".$position['description']."</li>");
      }
      print_r("</ul>");

    }
    ?>
    <a href="index.php">Done</a>
  </body>
</html>
