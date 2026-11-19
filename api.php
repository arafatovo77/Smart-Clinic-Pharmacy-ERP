<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'config.php';

$action = $_GET['action'] ?? 'summary';

if ($action == 'summary') {
    $patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $today_clinic = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM billings WHERE DATE(created_at) = date('now')")->fetchColumn();
    $today_pharmacy = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM pharmacy_sales WHERE DATE(created_at) = date('now')")->fetchColumn();
    $low_stock = $pdo->query("SELECT COUNT(*) FROM medicines WHERE stock_quantity < 10")->fetchColumn();

    echo json_encode([
        "status" => "success",
        "total_patients" => (int)$patients,
        "today_clinic_income" => (float)$today_clinic,
        "today_pharmacy_sales" => (float)$today_pharmacy,
        "low_stock_alert" => (int)$low_stock
    ]);
}
?>
