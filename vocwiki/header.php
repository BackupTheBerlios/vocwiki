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



if($file_id>0)
{
  $session_file=$file_id;
  session_register("session_file");
}
else
{
  $file_id=$HTTP_SESSION_VARS["session_file"];
}

?>

<?php
print "<?xml version=\"1.0\"?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" charset="utf-8" href="vocwiki.css" />
<title>vocwiki</title>
</head>
<body>
<table width="100%">
<tr><td width="50">
<?php
print "<a href=\"index.php\">".("index")."</a> ";
?>
</td><td width="50">
<?php 
if($file_id>0)
{
print "<a href=\"list.php\">".("list")."</a> ";
}
?>
<?php 
print "</td><td width=\"50\">";
print "<a href=\"stats.php\">".("stats")."</a>";
?>
</td><td align="right">
<?php

if($file_id>0)
{
  $link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
      or die(("no connection possible: ") . mysql_error());
  mysql_select_db($DBParams["mysql_db"]) or die(("database select failed."));
  
  $result = mysql_query("SELECT title FROM ".$DBParams["mysql_prefix"]."file where file_id=".$file_id);
  
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  print $row["title"]; 

  mysql_free_result($result);
  mysql_close($link);
}
?>

</td></tr>
</table>
<hr />
