<?php
// Define role-based access controls
$role_access = array(
    'administrator' => array('../administrator/dashboard.php', 'dashboard.php'),
    'Maintenance Personnel' => array('dashboard.php'),
    // Add all roles and their accessible pages
);
?>
