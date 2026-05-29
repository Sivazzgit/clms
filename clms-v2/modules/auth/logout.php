<?php
/**
 * CLMS 2.0 — logout.php
 */
Auth::logout();
Helpers::redirect('/login');
