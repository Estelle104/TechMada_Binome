<?php

$root = __DIR__ . '/..';
$writable = $root . '/writable';
$dbFile = $writable . '/database.sqlite';
$sqlFile = $root . '/table.sql';

if (!is_dir($writable)) {
    if (!mkdir($writable, 0777, true) && !is_dir($writable)) {
        echo "ERROR: cannot create writable directory\n";
        exit(1);
    }
}

if (!file_exists($sqlFile)) {
    echo "ERROR: table.sql introuvable: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
// Remove SQL comments that start with --
$sql = preg_replace('/^\s*--.*$/m', '', $sql);

try {
    $db = new SQLite3($dbFile);
    $db->busyTimeout(5000);
    $db->exec('PRAGMA foreign_keys = ON;');

    // Execute statements. SQLite3::exec accepts multiple statements.
    $result = $db->exec($sql);

    if ($result === false) {
        $err = $db->lastErrorMsg();
        echo "ERROR: import SQL failed: $err\n";
        exit(1);
    }

    // List tables as verification
    $res = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
    $tables = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $tables[] = $row['name'];
    }

    echo "IMPORT_OK\n";
    echo "Tables: " . implode(', ', $tables) . "\n";
    exit(0);

} catch (Exception $e) {
    echo "ERROR: Exception: " . $e->getMessage() . "\n";
    exit(1);
}
