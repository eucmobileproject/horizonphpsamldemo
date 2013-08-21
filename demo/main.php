<?php
session_start();
if (isset($_SESSION["nameid"])) {
  # user is authenticated
  $nameid = $_SESSION["nameid"];
  $userfirstname = $_SESSION["userfirstname"];
  $userlastname = $_SESSION["userlastname"];
} else {
  header("Location: unauth.html");
  exit;
}
include ("style_top.html");

$settings = NULL;
require 'settings.php';

echo "<p>User Email: " . htmlentities($nameid) . "</p>";

try {
  $dbh = new PDO($settings->dsn);
  $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  if($_POST['formSubmit'] == "Submit")
  {
    $errorMessage = "";
    if(empty($_POST['formNote']))
    {
      $errorMessage .= 'Please enter some text before clicking Submit. ';
    } else {
      $varNote = $_POST['formNote'];
      $notedata = array( 'author' => ($userfirstname . ' ' . $userlastname),
                         'note' => $varNote );
      $insertsth = $dbh->prepare("INSERT INTO notes (author, note) values (:author, :note)");
      $insertsth->execute($notedata);
    }
  }
    $sth = $dbh->prepare("SELECT * FROM notes ORDER BY id DESC");
    $sth->execute();
    $resultIter = new IteratorIterator($sth);
    $dbh = null;
}
catch(PDOException $e) {
  echo '<center><font color="red">An error occurred while accessing the database: ' .
       htmlentities($e->getMessage()) . '</font></center>';
}

?>
  <div class="block">
    <h3 class="block-title">Note Entry</h3>
    <div class="with-padding">
		<!-- content here -->
                <form action="main.php" method="post">
		<p>
			Please enter a one line note to share:<br>
			<input type="text" name="formNote" maxlength="300"/>
  <?php
    if(!empty($errorMessage)) {
      echo '<br>&nbsp;&nbsp;&nbsp;<font color="red">' .
           htmlentities($errorMessage) . "</font>\n";
    }
  ?>
		</p>
		<input type="submit" name="formSubmit" value="Submit" />
                </form>
    </div>
  </div>
  <p/>
  <?php
  $atLeastOneNoteShown = FALSE;
  $emptyNotes = TRUE;
   foreach($resultIter as $row) {
  ?>
  <?php
     if (!$atLeastOneNoteShown) {
       $atLeastOneNoteShown = TRUE;
       $emptyNotes = FALSE;
  ?>
  <div class="block">
    <h3 class="block-title">Users have shared these notes</h3>
    <div class="with-padding">
      <!-- content here -->
      <table border=0>
        <tr>
            <td><b>User</b></td>
            <td><b>Note</b></td>
        </tr>
  <?php
     } # if
  ?>
        <tr>
            <td><?php
            echo htmlentities($row["author"]);
            ?>&nbsp;&nbsp;&nbsp;</td>
            <td><?php
            echo htmlentities($row["note"]);
            ?></td>
        </tr>
  <?php
   } # end for resultIter
  ?>
  <?php
     if (!$emptyNotes) {
  ?>
      </table>
    </div>
  </div>
  <?php
     } # if
  ?>
<?php
include ("style_bottom.html");
?>
