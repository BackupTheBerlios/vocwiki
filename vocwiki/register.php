<?php
/*
    Copyright (C) 2004 Andreas Madsack

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

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

	$query="select name from ".$DBParams["mysql_prefix"]."users where name='$loginx'";


	$result=mysql_query($query,$link);
	
	if(mysql_num_rows($result)==0)
	{
	  $pw=md5($password1);

	  $query="INSERT INTO ".$DBParams["mysql_prefix"]."users (name,password,email,signuptime) VALUES ('$loginx','$pw','$email',NOW())";

	  $result = mysql_query($query,$link);
	  if(mysql_affected_rows()==0)
	  {
	    print ("problems with saving!!<br><br>please write a bugreport.");
	  }
	  else
	  {
	    print ("your data was saved successfully.<br><br><a href=\"login.php\">login now</a>");
	    
	  }

	}
	else
	{
	    print ("This user is already in database.<br><br><a href=\"register.php\">register an other account</a> or <a href=\"login.php\">login</a>");
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
