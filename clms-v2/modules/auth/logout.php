<?php
/**
 * CLMS 2.0 — logout.php
 */
Auth::logout();
header('Location: /login');
exit;
