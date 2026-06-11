<?php
/**
 * auth/logout.php
 */
require_once __DIR__ . '/../includes/auth.php';
if (session_status() === PHP_SESSION_NONE) session_start();
logout();
header('Location: /diabetesrisk-php/auth/login.php');
exit;
