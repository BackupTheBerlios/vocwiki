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

// configuration for vocwiki
//
// $Id: config.php,v 1.3 2004/05/05 21:36:06 bolsog Exp $	


//
// select an language
// default: "C" - English
// available: German "de"

// your weblocation:
$WEB_LOCATION="vocwiki.berlios.de";


include("dbpw.php");

// end of config part

///////////////////////////////////////////////


// enabling locale ... only php 4.1+ ?  (berlios is php4?)

// todo: save LANG in usersettings!!!

//setlocale(LC_ALL,$LANG);

//bindtextdomain("vocwiki", "./locale");
//textdomain("vocwiki");

// session
session_start();

if(!isset($login_form) || !isset($pwd_form))
{
// get it from session

  $login=$HTTP_SESSION_VARS["session_login"];
  $password=$HTTP_SESSION_VARS["session_password"];

  $session=true;
}
else
{
  $login=$login_form;
  $password=md5($pwd_form);

  $session=false;
}

if(isset($login) and isset($password))
{
  if($session==false)
  {
    $link=mysql_connect($DBParams['mysql_server'],$DBParams['mysql_user'],$DBParams['mysql_pwd']);
    mysql_select_db($DBParams['mysql_db']);
    
    $result=mysql_query("select name,password,last_file from vocwiki_users where lower(name)=lower('$login')",$link);
    
    if(mysql_num_rows($result))
    {
      $userinfo=mysql_fetch_array($result);
      
      if($userinfo["password"]==$password)
      {
	$session_login=$login;
	$session_password=$password;
	
	session_register("session_login");
	session_register("session_password");
	
	header("Location: "."http://".$WEB_LOCATION."/index.php");
	
      }
      else
      {
	print "password is wrong!!";
      }
    }
    else
    {
      print "user not found";
    }
  }
}
else
{
  session_destroy();
/*  if($PHP_SELF!='/vocwiki/register.php')
  {
    header("Location: "."http://".$WEB_LOCATION."/register.php");
  }*/
}

function getdatetime($timestamp)
{
  $y=$timestamp{0}.$timestamp{1};
  $m=$timestamp{2}.$timestamp{3};
  $d=$timestamp{4}.$timestamp{5};
  $h=$timestamp{6}.$timestamp{7};
  $mi=$timestamp{8}.$timestamp{9};
  $s=$timestamp{10}.$timestamp{11};

  return date("d M Y H:i:s", mktime ($h,$mi,$s,$m,$d,$y));
}


?>
