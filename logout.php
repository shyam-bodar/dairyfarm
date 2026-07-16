<?php
require_once __DIR__ . '/config/db.php';
session_unset();
session_destroy();
header('Location: ' . SITE_URL . 'login.php');
exit;
