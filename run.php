<?php
$output = shell_exec('php artisan migrate:fresh 2>&1');
file_put_contents('cmd_output.txt', $output);
echo "Done";
