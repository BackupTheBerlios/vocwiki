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
// $Id: config.php,v 1.6 2004/07/10 15:41:24 bolsog Exp $	


//
// select an language
// default: "C" - English
// available: German "de"

// your weblocation:

include("dbpw.php");

// end of config part

///////////////////////////////////////////////


// enabling locale ... only php 4.1+ ?  (berlios is php4?)

// todo: save LANG in usersettings!!!

//setlocale(LC_ALL,$LANG);

//bindtextdomain("vocwiki", "./locale");
//textdomain("vocwiki");

// session

// we want more than 1440 seconds ...
ini_set('session.gc_maxlifetime','604800');

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


// both encode functions are from: ronen at greyzone dot com

/**
* takes a string of utf-8 encoded characters and converts it to a string of unicode entities
* each unicode entitiy has the form &#nnnnn; n={0..9} and can be displayed by utf-8 supporting
* browsers
* @param $source string encoded using utf-8 [STRING]
* @return string of unicode entities [STRING]
* @access public
*/
function utf8ToUnicodeEntities ($source) {
   // array used to figure what number to decrement from character order value
   // according to number of characters used to map unicode to ascii by utf-8
   $decrement[4] = 240;
   $decrement[3] = 224;
   $decrement[2] = 192;
   $decrement[1] = 0;
  
   // the number of bits to shift each charNum by
   $shift[1][0] = 0;
   $shift[2][0] = 6;
   $shift[2][1] = 0;
   $shift[3][0] = 12;
   $shift[3][1] = 6;
   $shift[3][2] = 0;
   $shift[4][0] = 18;
   $shift[4][1] = 12;
   $shift[4][2] = 6;
   $shift[4][3] = 0;
  
   $pos = 0;
   $len = strlen ($source);
   $encodedString = '';
   while ($pos < $len) {
       $asciiPos = ord (substr ($source, $pos, 1));
       if (($asciiPos >= 240) && ($asciiPos <= 255)) {
           // 4 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 4);
           $pos += 4;
       }
       else if (($asciiPos >= 224) && ($asciiPos <= 239)) {
           // 3 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 3);
           $pos += 3;
       }
       else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
           // 2 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 2);
           $pos += 2;
       }
       else {
           // 1 char (lower ascii)
           $thisLetter = substr ($source, $pos, 1);
           $pos += 1;
       }

       // process the string representing the letter to a unicode entity
       $thisLen = strlen ($thisLetter);
       $thisPos = 0;
       $decimalCode = 0;
       while ($thisPos < $thisLen) {
           $thisCharOrd = ord (substr ($thisLetter, $thisPos, 1));
           if ($thisPos == 0) {
               $charNum = intval ($thisCharOrd - $decrement[$thisLen]);
               $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
           }
           else {
               $charNum = intval ($thisCharOrd - 128);
               $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
           }

           $thisPos++;
       }

       if ($thisLen == 1)
           $encodedLetter = "&#". str_pad($decimalCode, 3, "0", STR_PAD_LEFT) . ';';
       else
           $encodedLetter = "&#". str_pad($decimalCode, 5, "0", STR_PAD_LEFT) . ';';

       $encodedString .= $encodedLetter;
   }

   return $encodedString;
}


/**
* takes a string of unicode entities and converts it to a utf-8 encoded string
* each unicode entitiy has the form &#nnn(nn); n={0..9} and can be displayed by utf-8 supporting
* browsers.  Ascii will not be modified.
* @param $source string of unicode entities [STRING]
* @return a utf-8 encoded string [STRING]
* @access public
*/
function utf8Encode ($source) {
   $utf8Str = '';
   $entityArray = explode ("&#", $source);
   $size = count ($entityArray);
   for ($i = 0; $i < $size; $i++) {
       $subStr = $entityArray[$i];
       $nonEntity = strstr ($subStr, ';');
       if ($nonEntity !== false) {
           $unicode = intval (substr ($subStr, 0, (strpos ($subStr, ';') + 1)));
           // determine how many chars are needed to reprsent this unicode char
           if ($unicode < 128) {
               $utf8Substring = chr ($unicode);
           }
           else if ($unicode >= 128 && $unicode < 2048) {
               $binVal = str_pad (decbin ($unicode), 11, "0", STR_PAD_LEFT);
               $binPart1 = substr ($binVal, 0, 5);
               $binPart2 = substr ($binVal, 5);
          
               $char1 = chr (192 + bindec ($binPart1));
               $char2 = chr (128 + bindec ($binPart2));
               $utf8Substring = $char1 . $char2;
           }
           else if ($unicode >= 2048 && $unicode < 65536) {
               $binVal = str_pad (decbin ($unicode), 16, "0", STR_PAD_LEFT);
               $binPart1 = substr ($binVal, 0, 4);
               $binPart2 = substr ($binVal, 4, 6);
               $binPart3 = substr ($binVal, 10);
          
               $char1 = chr (224 + bindec ($binPart1));
               $char2 = chr (128 + bindec ($binPart2));
               $char3 = chr (128 + bindec ($binPart3));
               $utf8Substring = $char1 . $char2 . $char3;
           }
           else {
               $binVal = str_pad (decbin ($unicode), 21, "0", STR_PAD_LEFT);
               $binPart1 = substr ($binVal, 0, 3);
               $binPart2 = substr ($binVal, 3, 6);
               $binPart3 = substr ($binVal, 9, 6);
               $binPart4 = substr ($binVal, 15);
      
               $char1 = chr (240 + bindec ($binPart1));
               $char2 = chr (128 + bindec ($binPart2));
               $char3 = chr (128 + bindec ($binPart3));
               $char4 = chr (128 + bindec ($binPart4));
               $utf8Substring = $char1 . $char2 . $char3 . $char4;
           }
          
           if (strlen ($nonEntity) > 1)
               $nonEntity = substr ($nonEntity, 1); // chop the first char (';')
           else
               $nonEntity = '';

           $utf8Str .= $utf8Substring . $nonEntity;
       }
       else {
           $utf8Str .= $subStr;
       }
   }

   return $utf8Str;
}


?>
