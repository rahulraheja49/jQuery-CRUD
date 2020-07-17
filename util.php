<?php
// To display flash messages
function flashMessages(){
  if(isset($_SESSION['logged_in'])){
    if(isset($_SESSION['success'])){
      echo '<span style="color:green;text-align:center;">'.$_SESSION['success'].'</span>';
      unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])){
      echo '<span style="color:red;text-align:center;">'.$_SESSION['error'].'</span>';
      unset($_SESSION['error']);
    }
  }
}

// To validate profile when filling form
function validateProfile(){
  if( strlen($_POST['first_name'])==0 || strlen($_POST['last_name'])==0 || strlen($_POST['email'])==0 || strlen($_POST['headline'])==0 || strlen($_POST['summary'])==0 ){
    return "All fields are required";
  }

  if( strpos($_POST['email'], '@') == false){
    return "Email address must contain @";
  }

  return true;
}

// To look through the post data and return true or error message
function validatePos(){
  for($i=1; $i<=9; $i++){
    if( !isset($_POST['year'.$i]) ) continue;
    if( !isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if( strlen($year)==0 || strlen($desc)==0 ){
      return "All fields are required";
    }

    if( !is_numeric($year) ){
      return "Position year must be numeric";
    }
  }
  return true;
}

function loadPos($pdo, $profile_id){
  $stmt = $pdo->prepare('SELECT * FROM position
    WHERE profile_id = :profile_id ORDER BY rank');
  $stmt->execute(array(
    ':profile_id' => $profile_id
  ));
  $positions = array();
  while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
    $positions[] = $row;
  }
  return $positions;
}

// To fetch the rows in the table profile
function fetchProfiles($pdo, $user_id){
  $stmt = $pdo->prepare("SELECT first_name, headline FROM profile WHERE user_id = :user_id");
  $stmt->execute(array(
    ":user_id" => $_SESSION['user_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = :user_id");
  $stmt->execute(array(':user_id' => $_SESSION['user_id']));
  return $stmt;
}

?>
