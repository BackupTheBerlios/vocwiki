<?php
include("config.php");
include("header.php");

?>
<div class="wikitext">
<br><br>
<form action="index.php" method="post">
<table>
<tr><td>
<?php
print ("login").":"; 
?>
</td><td>
<input name="login_form" type="text" size="30">
</td></tr>
<tr><td>
<?php
print ("password").":"; 
?>
</td><td>
<input name="pwd_form" type="password" size="30">
</td></tr>
<tr><td>
<input type="submit" value="<?php print (" send "); ?>">
</td></tr>
</table>
<br>
<?php
//    print "<a href=\"wakka/\">".("register")."</a> ";
print "registrationform to be done ...<br>closed test";
?>
<br><br></div>

<?php

include("footer.php");
?>
