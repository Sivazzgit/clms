<?php
/**
 * config/local.php — Production overrides
 *
 * Copy this file to config/local.php on the production server and fill in
 * the values. This file is ignored by git (never commit it).
 *
 * This is the ONLY file you need to create on the production server.
 * Combined with editing RewriteBase in .htaccess, the app will work
 * in any subfolder.
 */
return [
    // Subfolder path — must match the folder name in public_html.
    // No trailing slash. Leave as '' for root deployment.
    'APP_BASE'  => '/clms',

    // Database credentials on the production server
    'DB_HOST'   => 'localhost',
    'DB_NAME'   => 'clms_v2',
    'DB_USER'   => 'clmsadmin',
    'DB_PASS'   => 'ClmsAdmin@123',
];
