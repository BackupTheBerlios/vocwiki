<?php
include("config.php");
include("header.php");

?>

<div class="wikitext">
<form action="list.php" method="post">
<table>
<tr>
<td width="100">
<?php
print ("source: ");
?>
</td><td width="70">
<?php
print ("type: ");
?>
</td><td width="230">
<?php
print ("search in translation, original: ");
?>
</td><td width="100">
<?php
print ("show per page: ");
?>
</td><td></td></tr>
<tr><td>

<?php
$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

if(!isset($HTTP_SESSION_VARS["session_file"]) and !isset($file_id))
{
  print ("no file selected!!!");
}
else if(!isset($file_id))
{
  $file_id=$HTTP_SESSION_VARS["session_file"];
}

// get username
if(strlen($HTTP_SESSION_VARS["session_login"])>0)
{
  $query = "select name from vocwiki_users where lower(name)=lower('".$HTTP_SESSION_VARS["session_login"]."')";
  $result = mysql_query($query);
  
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $username=$row["name"];
}
else
{
  $username="";
}

// sources
$query = "SELECT distinct content FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on d.fk_element=e.element_id where e.fk_file=".$file_id." and typ='source' order by content";

$result = mysql_query($query);

// print $query."<br/>";
?>

<select name="source" size="1">
<option selected="selected">-</option>
 
<?php
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  print "<option>".$row["content"]."</option>"; 
}
?>

</select>
</td><td>

<?php
// type
$query = "SELECT distinct content FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on d.fk_element=e.element_id where e.fk_file=".$file_id." and typ='type' order by content";

$result = mysql_query($query);

?>

<select name="type" size="1">
<option selected="selected">-</option>

<?php
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  print "<option>".$row["content"]."</option>"; 
}
?>
</select>
</td><td>


<?php
// free search  (translation, original)
?>
<input name="freesearch" type="text" size="40" maxlength="40"<?php
if(isset($freesearch))
{
    print " value=\"".$freesearch."\"";
}
print "/>";
?>
</td><td align="right">
<?php

// number of ds per page

?>
<select name="count" size="1">
<option selected="selected">25</option>
<option>50</option>
<option>100</option>
</select>
</td><td>

<?php
print "<input type=\"hidden\" name=\"file_id\" value=\"".$file_id."\"/>";
?>
<input type="submit" name="submit" value=" go "/>


</td></tr></table></form>
<?php if(isset($submit)): ?>

<?php

$query="SELECT distinct a.fk_element as id FROM ".$DBParams["mysql_prefix"]."data a left join ".$DBParams["mysql_prefix"]."element e on e.element_id=a.fk_element ";
$where = " e.fk_file=".$file_id." and ";
if($source != '-')
{
  $query .= " inner join ".$DBParams["mysql_prefix"]."data b on a.fk_element=b.fk_element ";
  $where .= " (b.typ='source' and b.content='".$source."') and ";
}
if($type != '-')
{
  $query .= " inner join ".$DBParams["mysql_prefix"]."data c on a.fk_element=c.fk_element ";
  $where .= " (c.typ='type' and c.content='".$type."') and ";
}

if(strlen($freesearch)>0)
{
  $where .= " ((a.typ='translation' or a.typ='original') and a.content like '%".$freesearch."%') and ";
}

if(strlen($where)>0)
{
  $where = ereg_replace("and $","",$where);
  $where =" where ".$where;
}
$query .= $where;


if(!isset($start))
{
  $start=0;
}
$query .= "order by a.fk_element ";


$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

$result = mysql_query($query);
$num_rows = mysql_num_rows($result);

$query .=" limit ".$start.",".$count.";";

//print "<br/>".$query."<br/>";
print "<br/>";

$result = mysql_query($query);

$ids="";
$cnt=0;

if($num_rows>0)
{
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $ids .= " element_id=".$row["id"]." or";
    $cnt++;
  }
  $ids = ereg_replace(" or$","",$ids);
// print $cnt."<br/>";

  $query="SELECT d.fk_element,subtype,typ,content,e.version,r.coeff,r.allx FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on e.element_id=d.fk_element and d.version=e.version left join ".$DBParams["mysql_prefix"]."rating r on r.username='".$username."' and r.fk_element=e.element_id where (".$ids.") and e.fk_file=".$file_id." order by d.fk_element,d.version desc";


// print $query;
  $result = mysql_query($query);

  $thisx=0;
  $version=0;
  $vals['source']="";
  $vals['type']="";
  $vals['original']="";
  $vals['translation']="";
  
  if(mysql_num_rows($result))
  {
    print "<table>";
    print "<tr><th>ver</th><th>source</th><th>typ</th><th>original</th><th>translation</th><th></th><th>correct</th><th> / cnt</th></tr>";
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
    {
//  print $version;
      if($thisx!=$row["fk_element"] && $thisx!=0)
      {
	if($allx>0)
	{
	  $tmp=$coeff*100;
	  $tmp="<td align=\"right\">".$tmp."%</td><td> / ".$allx."</td>";
	}
	else
	  $tmp="";

	print "<tr><td>$version</td><td>".$vals['source']."</td><td>".$vals['type']."</td><td>".$vals['original']."</td><td>".$vals['translation']."</td><td><a href=\"edit.php?element_id=".$thisx."\" target=\"_blank\">(edit)</a></td>".$tmp."</tr>";
	$vals['source']="";
	$vals['type']="";
	$vals['original']="";
	$vals['translation']="";
	$thisx=$row["fk_element"];
	$version=0;
      }
      if($thisx==0)
      {
	$thisx=$row["fk_element"];
      }
      if($version==0)
      {
	$version=$row["version"];
      }
      
      if($version==$row['version'])
      {
	if(!(ereg("^example",$row["subtype"])))
	{
	  if(strlen($vals[$row['typ']])>0)
	  {
	    $vals[$row['typ']].=" / ";
	  }
	  $vals[$row['typ']].=$row['content'];
	}
      }
      $allx=$row["allx"];
      $coeff=$row["coeff"];
    }
    
    if($allx>0)
    {
      $tmp=$coeff*100;
      $tmp="<td align=\"right\">".$tmp."%</td><td> / ".$allx."</td>";
    }
    else
      $tmp="";

    print "<tr><td>$version</td><td>".$vals['source']."</td><td>".$vals['type']."</td><td>".$vals['original']."</td><td>".$vals['translation']."</td><td><a href=\"edit.php?element_id=".$thisx."\" target=\"_blank\">(edit)</a></td>".$tmp."</tr>";
  }
}

//SELECT count(distinct a.fk_element) FROM vocwiki_data a inner join vocwiki_data b on a.fk_element=b.fk_element and (b.typ='translation' or b.typ='original') and b.content like '%gehen%' order by a.fk_element;
?>

<?php
// show links to next pages:

if(mysql_num_rows($result))
{
  if($num_rows>$count)
  {
    $j=0;
    
    print "<tr><td>&nbsp;</td></tr>";
    print "<tr><td colspan=\"6\" align=\"right\">";
    
    for($i=0;$i<$num_rows;$i+=$count)
    {
      $j++;
      if($start!=$i)
      {
	print "<a href=\"list.php?start=".$i."&count=".$count."&source=".$source."&type=".$type."&freesearch=".$freesearch."&file_id=".$file_id."&submit=yo\">".$j."</a> ";
      }
      else
      {
	print "&nbsp; ";
      }
    }
  }

  print "</td></tr>";
  print "<tr><td colspan=\"3\">";
  print "<a href=\"train.php?source=".$source."&type=".$type."&freesearch=".$freesearch."&file_id=".$file_id."\">".("training")."</a> ";
  print "</td></tr></table>";

  mysql_free_result($result);
  mysql_close($link);
}
?>

<?php endif; ?>
<br/>
<a href="edit.php">
<?php
print ("insert new data record")."</a><br/>";
?>

<br/>
</div>
<?php

include("footer.php");
?>
