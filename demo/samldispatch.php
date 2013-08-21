<?php
function print_html_start() {
  echo "<html><head><title>Horizon Workspace SAML Demo</title></head>\n" .
   "<body>\n" .
   "  <p><center><h1>Demo Web Application</h1></center></p>\n";
}

error_reporting(E_ALL);

$settings = NULL;
require 'settings.php';

$samlResponse = new OneLogin_Saml_Response($settings, $_POST['SAMLResponse']);
$userAuthenticated = FALSE;
try {
    $samlIsValid = $samlResponse->isValid();
    if ($samlIsValid) {
        $nameid = $samlResponse->getNameId();
        $attributes = $samlResponse->getAttributes();
        if (!empty($attributes)) {
            foreach ($attributes as $attributeName => $attributeValues) {
                    foreach ($attributeValues as $attributeValue) {
                        if ($attributeName == 'external') {
                          $externalid = $attributeValue;
                        } elseif ($attributeName == 'lastname') {
                          $userlastname = $attributeValue;
                        } elseif ($attributeName == 'firstname') {
                          $userfirstname = $attributeValue;
                        } elseif ($attributeName == 'principalname') {
                          $principalname = $attributeValue;
                        } elseif ($attributeName == 'username') {
                          $username = $attributeValue;
                        }
                    }
            }
        }
        $userAuthenticated = TRUE;
    }
    else {
        $userAuthError = 'Invalid SAML response.';
    }
}
catch (Exception $e) {
  $userAuthError =  'Invalid SAML response: ' . htmlentities($e->getMessage());
}

if (!$userAuthenticated) {
  # do not proceed further or access the database as user is not authenticated
  print_html_start();
  echo "  <p><center>Unauthenticated User, please use Horizon Workspace to log in.</center></p>\n";
  echo "  <p><center>Error: <font color='red'>" . $userAuthError . "</font></center></p>\n";
  echo "</body>\n";
  echo "</html>\n";
  exit;
}

try {
  $dbh = new PDO($settings->dsn);
  $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  $selectdata = array( 'nameid' => $nameid );
  $sth = $dbh->prepare("SELECT * FROM accounts WHERE nameid = :nameid");
  $sth->execute($selectdata);
  if ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
    $data = array( 'nameid' => $nameid );
    $updatesth =
      $dbh->prepare("UPDATE accounts SET logincount = logincount + 1 WHERE nameid = :nameid");
    $updatesth->execute($data);
  } elseif (!is_null($nameid)) {
    # the data we want to insert
    $data = array( 'nameid' => $nameid,
                   'logincount' => 1,
                   'principalname' => $principalname,
                   'externalid' => $externalid );
    $insertsth =
      $dbh->prepare("INSERT INTO accounts (nameid, externalid, principalname, logincount) values (:nameid, :externalid, :principalname, :logincount)");
    $insertsth->execute($data);
  } else {
    # do not proceed further as user is not authenticated
    print_html_start();
    echo "  <p><center>Unauthenticated User, please use Horizon Workspace to log in.</p>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
  }
  $dbh = null;
}
catch(PDOException $e) {
    print_html_start();
    echo '<center><font color="red">An error occurred while accessing the database: ' .
         htmlentities($e->getMessage()) . '</font></center>';
    echo "</body>\n";
    echo "</html>\n";
    exit;
}

session_start();
$_SESSION["nameid"] = $nameid;
$_SESSION["userfirstname"] = $userfirstname;
$_SESSION["userlastname"] = $userlastname;

header("Location: main.php");
exit;

?>
