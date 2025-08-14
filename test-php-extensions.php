<?php
echo "Loaded extensions:\n";
foreach (get_loaded_extensions() as $extension) {
    echo "- $extension\n";
}

echo "\nPDO drivers:\n";
if (class_exists('PDO')) {
    foreach (PDO::getAvailableDrivers() as $driver) {
        echo "- $driver\n";
    }
}
