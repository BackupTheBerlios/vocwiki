<?php
include("config.php");
include("header.php");

?>

<div class="wikitext">
<?php if(!isset($submit)): ?>

<?php
print "<br><br><b>";
print _("register new user:<br>");
print "</b>";

?>

<form action="register.php" method="post">
<table cellspacing="0" cellpadding="1" border="0">
<tr>
<td>
<?php
print _("login: ");
?>
</td><td>
<input name="loginx" type="text" size="15">
</td>
</tr>

<tr>
<td>
<?php
print _("email: ");
?>
</td><td>
<input name="email" type="text" size="30">
</td>
</tr>

<tr>
<td>
<?php
print ("password: ");
?>
</td><td>
<input name="password1" type="password" size="10">
<input name="password2" type="password" size="10">
</td>
</tr>

<tr>
<td>

</td><td align="right">
<input type="hidden" name="op" value="add">
<input type="submit" name="submit" value="<?php print ("send");?>">
</td>
</tr>
</table>

<?php else: ?>

<?php
if(strlen($loginx)>0 and strlen($password1)>0)
{
    if($password1!=$password2)
    {
	print ("both password arn't the same.");
    }
    else
    {
	$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
	or die("connection failed: " . mysql_error());

	mysql_select_db($DBParams["mysql_db"]) or die("problems, connecting to database");

	$query="select login from ".$DBParams["mysql_prefix"]."user where login='$loginx'";

	$result=mysql_query($query,$link);
	
	if(mysql_num_rows($result)==0)
	{
	  $pw=md5($password1);

	  $query="INSERT INTO ".$DBParams["mysql_prefix"]."user (login,password,level,email,lastchange) VALUES ('$loginx','$pw',1,'$email',NOW())";

	  $result = mysql_query($query,$link);
	  if(mysql_affected_rows()==0)
	  {
	    print ("problems with saving!!");
	  }
	  else
	  {
	    print ("all saved");
	  }

	}
	else
	{
	    print ("user already in database");
	}
	mysql_close ($link);
    }
}

?>

<?php endif; ?>

<br>
</div>

<?php
include("footer.php");
?>
