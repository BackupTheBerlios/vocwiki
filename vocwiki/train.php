<?php
include("config.php");
include("header.php");

?>

<div class="wikitext">

<?php
$show_info=0;

$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

if(/*isset($trainx)*/ $submit==" start " or $submit==" next ")
{
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
    print "<br>".("You're not logged in, there will be no statistics!!")."<br><br>";
  }

  
  // only write rating-data when a user is logged in!
  if($submit==" next " and $username != "")
  {
    if($rating=="correct")
    $correct_add=1;
    else
    $correct_add=0;

    // get id of rating data record:
    $query = "select rating_id,correct,allx from ".$DBParams["mysql_prefix"]."rating where username='".$username."' and fk_element=".$id;
	
    $result = mysql_query($query);

    if(mysql_num_rows($result)>0)
    {
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      $ratingid=$row["rating_id"];
      $correct=$row["correct"];
      $all=$row["allx"];
      $correct+=$correct_add;
      $all+=1;
      
      $coeff=$correct/$all;

      // update record
      $query = "update ".$DBParams["mysql_prefix"]."rating set correct=".$correct.", allx=".$all.", coeff=".$coeff." where rating_id=".$ratingid;

      $result = mysql_query($query);

//      print $query;
      
    }
    else
    {
      $correct=$correct_add;
      $coeff=$correct/1;
      // insert record
      $query = "INSERT into ".$DBParams["mysql_prefix"]."rating (username,fk_element,correct,allx,coeff) values ('".$username."',".$id.",".$correct.",1,".$coeff.");";

      $result = mysql_query($query);

//      print $query;
      
    }

  }

  if(isset($id))
  {
    $oldid=$id;
  }
  else
  {
    $oldid=0;
  }

  // get info an ask for it

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
  $query .= "order by a.fk_element ";

  $result = mysql_query($query);
  $num_rows = mysql_num_rows($result);

  $j=0;
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    //print $row["typ"]." - ".$row["content"]."<br>"; 
    $ids .= " element_id=".$row["id"]." or";
    $a[$j++]=$row["id"];
    $cnt++;
  }
  $ids = ereg_replace(" or$","",$ids);

  $query ="select element_id from vocwiki_element e left join vocwiki_rating v on v.fk_element=e.element_id and v.username='".$username."' where (".$ids.")";

  if($mode=="coeff")
  {
    $query.="  order by coeff, rand(NOW());";
  }
  if($mode=="random")
  {
    $query.="  order by rand(NOW());";
  }
  if($mode=="ordered")
  {
    $query.=" and e.element_id>".$oldid.";";
  }

//  print $query;

  $result = mysql_query($query);
  $num_rows = mysql_num_rows($result);

//while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $idx=$row["element_id"];
//}

  if($idx>0 and $num_rows>0)
  {
    $query="SELECT fk_element,typ,content,e.version FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on e.element_id=fk_element and d.version=e.version where (element_id=".$idx.")";
    
    $result = mysql_query($query);
    
    $thisx=0;
    $version=0;
    $vals['source']="";
    $vals['type']="";
    $vals['original']="";
    $vals['translation']="";
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
    {
      if($thisx!=$row["fk_element"] && $thisx!=0)
      {
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
	if(strlen($vals[$row['typ']])>0)
	{
	  $vals[$row['typ']].=" <br> ";
	}
	$vals[$row['typ']].=$row['content'];
      }
    }
    
    $r_askfor=-1;
    if($askfor=="both")
    {
      $r_askfor=rand(0,1);
    }

    if($askfor=="trans" or $r_askfor==0)
    {
      print "<br><b>original:</b><br><br>";
      print $vals['original']."<br><br><br>";
    }
    if($askfor=="orig" or $r_askfor==1)
    {
      print "<br><b>translation:</b><br><br>";
      print $vals['translation']."<br><br><br>";
    }

    print "<form action=\"train.php\" method=\"post\">";
    print "<input name=\"usersol\" type=\"text\" size=\"40\" maxlength=\"40\"> &nbsp;";
    print "<input type=\"hidden\" name=\"id\" value=\"".$idx."\">";
    print "<input type=\"hidden\" name=\"source\" value=\"".$source."\">";
    print "<input type=\"hidden\" name=\"type\" value=\"".$type."\">";
    print "<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">";
    print "<input type=\"hidden\" name=\"file_id\" value=\"".$file_id."\">";
    print "<input type=\"hidden\" name=\"freesearch\" value=\"".$freesearch."\">";
    print "<input type=\"hidden\" name=\"askfor\" value=\"".$askfor."\">";
    print "<input type=\"hidden\" name=\"r_askfor\" value=\"".$r_askfor."\">";
    print "<input type=\"hidden\" name=\"submit\" value=\" check \">";
    print "<input type=\"submit\" name=\"submit\" value=\" check \"></form>";
    print "<br><br>";
  }
  else
  {
    print "<br>".("all vocabulary were asked.")."<br><br>";
//    print "<a href=\"train.php?source=".$source."&type=".$type."&freesearch=".$freesearch."&submit= start &askfor=".$askfor."&file_id=".$file_id."&mode=".$mode."\">".("start over again?")."</a> <br>";
    $show_info=1;
  }
}
else
{
  if($submit==" check ")
  {
  // get values from id
    $query="SELECT fk_element,typ,content,e.version FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on e.element_id=fk_element and d.version=e.version where (element_id=".$id.")";
    
    $result = mysql_query($query);
  
    $thisx=0;
    $version=0;
    $vals['source']="";
    $vals['type']="";
    $vals['original']="";
    $vals['translation']="";
    
    $correct=false;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
    {
      if($thisx!=$row["fk_element"] && $thisx!=0)
      {
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
	if(strlen($vals[$row['typ']])>0)
	{
	  $vals[$row['typ']].=" <br> ";
	}
      $vals[$row['typ']].=$row['content'];
      if(($askfor=="trans" or $r_askfor==0) and $row['typ']=="translation" and $row['content']==$usersol)
      $correct=true;
      if(($askfor=="orig" or $r_askfor==1) and $row['typ']=="original" and $row['content']==$usersol)
      $correct=true;
      }
    }
   
    if($askfor == "trans" or $r_askfor==0)
    {
      print "<br><b>original:</b><br><br>";
      print $vals['original']."<br><br><br><br>";
      print "<b>your translation:</b><br><br>";
      print $usersol;
      print "<br><br><br>";
      print "<b>correct translation:</b><br><br>";
      print $vals['translation'];
      print "<br><br><br>";
    }

    if($askfor == "orig" or $r_askfor==1)
    {
      print "<br><b>translation:</b><br><br>";
      print $vals['translation']."<br><br><br><br>";
      print "<b>your original:</b><br><br>";
      print $usersol;
      print "<br><br><br>";
      print "<b>correct original:</b><br><br>";
      print $vals['original'];
      print "<br><br><br>";
    }
    
    
    print "<form action=\"train.php\" method=\"post\">";

    print "<input type=\"hidden\" name=\"id\" value=\"".$id."\">";
    print "<input type=\"hidden\" name=\"source\" value=\"".$source."\">";
    print "<input type=\"hidden\" name=\"file_id\" value=\"".$file_id."\">";
    print "<input type=\"hidden\" name=\"type\" value=\"".$type."\">";
    print "<input type=\"hidden\" name=\"freesearch\" value=\"".$freesearch."\">";
    print "<input type=\"hidden\" name=\"askfor\" value=\"".$askfor."\">";
    print "<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">";
    print "<input type=\"radio\" name=\"rating\" value=\"correct\" ";
    if($correct)
    print "checked";
    print ">correct&nbsp;&nbsp;";
    print "<input type=\"radio\" name=\"rating\" value=\"wrong\" ";
    if(!$correct)
    print "checked";
    print ">wrong&nbsp;&nbsp;";
    print "<input type=\"submit\" name=\"submit\" value=\" next \"></form>";
    print "<br><br>";
    print "<a href=\"edit.php?element_id=".$id."\" target=\"_blank\">";
    print ("<b>edit</b> this data record in a <b>new window</b>")."</a><br>";
  }
  else
  {
    $show_info=1;
  }
}
if($show_info==1)
{
  // show info on training before starting!
  print "<br><br>";
  
    
  print "<form action=\"train.php\" method=\"post\">";
  
  print "<input type=\"hidden\" name=\"source\" value=\"".$source."\"/>";
  print "<input type=\"hidden\" name=\"file_id\" value=\"".$file_id."\"/>";
  print "<input type=\"hidden\" name=\"type\" value=\"".$type."\"/>";
  print "<input type=\"hidden\" name=\"freesearch\" value=\"".$freesearch."\"/>";
  print "ask for <select name=\"askfor\" size=\"1\">";
  print "<option ";
  if($askfor=="trans")
  {
    print "selected=\"selected\"";
  }
  print "  value=\"trans\">translation</option>";
  print "<option "; 
  if($askfor=="orig")
  {
    print "selected=\"selected\"";
  }
  print " value=\"orig\">original</option>";
  print "<option ";
  if($askfor=="both")
  {
    print "selected=\"selected\"";
  }
  print "value=\"both\">both</option>";
  print "</select><br>";
  print "mode of <select name=\"mode\" size=\"1\">";
  print "<option ";
  if($mode=="coeff")
  {
    print "selected=\"selected\"";
  }
  print " value=\"coeff\">by coefficient (right/all)</option>";
  print "<option ";
  if($mode=="random")
  {
    print "selected=\"selected\"";
  }
  print " value=\"random\">random</option>";
  print "<option ";
  if($mode=="ordered")
  {
    print "selected=\"selected\"";
  }
  print" value=\"ordered\">order in database</option>";
  print "</select><br>";
  
  print "<input type=\"hidden\" name=\"submit\" value=\" start \">";
  print "<input type=\"submit\" name=\"submit\" value=\" start \"></form>";
  print "<br><br>";
  
  print "<a href=\"pdf/test.php?source=".$source."&type=".$type."&freesearch=".$freesearch."&file_id=".$file_id."\">".("generate a Test as PDF")."</a> (only ISO-8859-1) [no arabic, asian languages!]";
  
  print "<br><br>";
}
?>


<br>
</div>
<?php

include("footer.php");
?>
