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
<table><tr><td valign="top">
<br/><br/>
<?php
print ("choose vocabulary file:<br/>");

$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

$result = mysql_query("SELECT file_id, title,original_name,original_code,translation_name,translation_code FROM ".$DBParams["mysql_prefix"]."file order by original_code");

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  if($row["file_id"]==3)
  {
    print "<a href=\"list.php?file_id=".$row["file_id"]."\">testarea</a><br/>"; 
  }
  else
  {
    print "<a href=\"list.php?file_id=".$row["file_id"]."\">".$row["original_name"]." (".$row["original_code"].") - ". $row["translation_name"]." (".$row["translation_code"].")</a><br/>"; 
  }
}
?>
</td>
<td width="70">
</td>
<td valign="top">
<div class="xxx">
Das vocwiki hat nun auf Berlios ein Zuhause gefunden.<br>
<br>
bugreports/feature request/forum gibt es unter: <a href="http://developer.berlios.de/projects/vocwiki/">http://developer.berlios.de/projects/vocwiki/</a><br>
<br>
<a href="http://developer.berlios.de">
<img src="http://developer.berlios.de/bslogo.php?group_id=0&type=1" width="124" height="32" border="0" alt="BerliOS Logo"></A>
</div>
</td></tr></table>
<?php
print "<br/><br/></div>";

mysql_free_result($result);
mysql_close($link);


include("footer.php");
?>
