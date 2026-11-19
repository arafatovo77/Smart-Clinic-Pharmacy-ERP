<?php
require_once 'config.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $generic = $_POST['generic_name'];
    $category = $_POST['category'];
    $company = $_POST['company'];
    $buy_price = (float)$_POST['buy_price'];
    $sale_price = (float)$_POST['sale_price'];
    $stock = (int)$_POST['stock_quantity'];
    $expiry = $_POST['expiry_date'];
    $rack = $_POST['rack_no'];

    $stmt = $pdo->prepare("INSERT INTO medicines (name, generic_name, category, company, buy_price, sale_price, stock_quantity, expiry_date, rack_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $generic, $category, $company, $buy_price, $sale_price, $stock, $expiry, $rack])) {
        $msg = "নতুন ঔষধ সফলভাবে স্টকে যুক্ত হয়েছে!";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ঔষধ স্টক ইনভেন্টরি - স্মার্ট ক্লিনিক</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">📦 নতুন ঔষধ স্টক ইনভেন্টরি যুক্ত করুন</h4>
        <a href="pharmacy_pos.php" class="btn btn-outline-secondary btn-sm">← ফার্মেসি পিওএস-এ ফিরুন</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <?php if ($msg): ?>
                <div class="alert alert-success"><?= $msg ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">ঔষধের ব্র্যান্ড নাম *</label>
                        <input type="text" name="name" class="form-control" required placeholder="উদাঃ Napa Extra 500mg">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">জেনেরিক নাম</label>
                        <input type="text" name="generic_name" class="form-control" placeholder="Paracetamol">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">কোম্পানি</label>
                        <input type="text" name="company" class="form-control" placeholder="Square / Beximco">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">ধরণ (Category)</label>
                        <select name="category" class="form-select">
                            <option value="Tablet">Tablet</option>
                            <option value="Capsule">Capsule</option>
                            <option value="Syrup">Syrup</option>
                            <option value="Injection">Injection</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">ক্রয়মূল্য (৳)</label>
                        <input type="number" step="0.01" name="buy_price" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">বিক্রয়মূল্য (৳)</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">স্টক পরিমাণ</label>
                        <input type="number" name="stock_quantity" class="form-control" value="100" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">মেয়াদের তারিখ</label>
                        <input type="date" name="expiry_date" class="form-control" required>
                    </div>
                </div>
                <input type="hidden" name="rack_no" value="A-1">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">স্টকে যুক্ত করুন</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
