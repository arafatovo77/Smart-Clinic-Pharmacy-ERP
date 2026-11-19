<?php
// ডাটাবেস কানেকশন — SQLite (Render ফ্রি ডেপ্লয়ের জন্য; আসল MySQL কোডের বদলে)
// প্রথমবার চালু হলে schema.sql থেকে টেবিল + ডেমো ওষুধ তৈরি হবে।
$dbFile = __DIR__ . '/clinic.db';
$needsInit = !file_exists($dbFile);

try {
    $pdo = new PDO("sqlite:" . $dbFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("PRAGMA foreign_keys = ON");
    if ($needsInit) {
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($schema);
    }
} catch (PDOException $e) {
    die("ডাটাবেস কানেক্ট হতে পারেনি: " . $e->getMessage());
}
?>
