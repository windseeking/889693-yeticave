<?php

require_once('init.php');

unset($_SESSION['user']);
header("Location: index.php");
