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
<br/><br/>
<?php

$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

print ("Vocabularies edited:")."<br>";

$result = mysql_query("SELECT DISTINCT author , count(*) as cnt FROM ".$DBParams["mysql_prefix"]."data GROUP BY author order by cnt desc");

print "<br><table>";
print "<tr><th>Author</th><td>&nbsp;</td><th><em>Count</th></tr>";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  print "<tr><td>".$row["author"]."</td><td>&nbsp;</td><td>".$row["cnt"]."</td></tr>"; 
}
print "</table>";

print "<br><br>";
print ("Vocabularies tested:")."<br>";

$result = mysql_query("SELECT DISTINCT username, count(*) as cnt, sum(allx) as allasked FROM ".$DBParams["mysql_prefix"]."rating GROUP BY username order by cnt desc");

print "<br><table>";
print "<tr><th>Author</th><td>&nbsp;</td><th><em>Vocs</em></th><td>&nbsp;</td><th><em>queries</em></th></tr>";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  if(strlen($row["username"])>0)
  {
    print "<tr><td>".$row["username"]."</td><td>&nbsp;</td><td>".$row["cnt"]."</td><td>&nbsp;</td><td>".$row["allasked"]."</td></tr>"; 
  }
}
print "</table>";


?>

<?php
print "<br/><br/></div>";

mysql_free_result($result);
mysql_close($link);


include("footer.php");
?>
