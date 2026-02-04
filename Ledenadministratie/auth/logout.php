<?php
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::logout();

redirect('login.php');
