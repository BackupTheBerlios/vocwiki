<?php

include("config.php");

session_start();
session_unregister("session_login");
session_unregister("session_password");
session_destroy();
header("Location: "."http://".$WEB_LOCATION."/index.php");

?>