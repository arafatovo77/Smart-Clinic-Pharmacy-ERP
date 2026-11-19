<?php
require_once 'config.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sell_medicine'])) {
    $med_id = $_POST['medicine_id'];
    $qty = (int)$_POST['quantity'];
    $customer = $_POST['customer_name'] ?: 'সাধারণ গ্রাহক';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE id = ?");
    $stmt->execute([$med_id]);
    $med = $stmt->fetch();

    if ($med && $med['stock_quantity'] >= $qty) {
        $subtotal = $med['sale_price'] * $qty;
        $bill_no = "PHARM-" . date("Ymd") . "-" . rand(100, 999);

        $stmt_sale = $pdo->prepare("INSERT INTO pharmacy_sales (bill_no, customer_name, payment_method, total_amount, paid_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt_sale->execute([$bill_no, $customer, $payment_method, $subtotal, $subtotal]);
        $sale_id = $pdo->lastInsertId();

        $stmt_item = $pdo->prepare("INSERT INTO pharmacy_sale_items (sale_id, medicine_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt_item->execute([$sale_id, $med_id, $qty, $med['sale_price'], $subtotal]);

        $stmt_update = $pdo->prepare("UPDATE medicines SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmt_update->execute([$qty, $med_id]);

        $msg = "বিক্রি সফল! মেমো নং: <strong>$bill_no</strong> | মোট বিল: <strong>$subtotal ৳ ($payment_method)</strong>";
    } else {
        $msg = "ত্রুটি: পর্যাপ্ত স্টক নেই!";
    }
}

$medicines = $pdo->query("SELECT * FROM medicines ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ফার্মেসি পিওএস ও সেলস কাউন্টার</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">💊 ফার্মেসি পিওএস (POS) ও ঔষধ বিক্রয়</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← ড্যাশবোর্ডে ফিরুন</a>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">ঔষধ বিক্রয় কাউন্টার</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($msg): ?>
                        <div class="alert alert-info"><?= $msg ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="sell_medicine" value="1">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">গ্রাহক / রোগীর নাম</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="সাধারণ গ্রাহক">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ঔষধ নির্বাচন করুন *</label>
                            <select name="medicine_id" class="form-select" required>
                                <option value="">-- ঔষধ পছন্দ করুন --</option>
                                <?php foreach ($medicines as $m): ?>
                                    <option value="<?= $m['id'] ?>">
                                        <?= htmlspecialchars($m['name']) ?> (মজুদ: <?= $m['stock_quantity'] ?> | দাম: <?= $m['sale_price'] ?>৳)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">পরিমাণ (Qty) *</label>
                                <input type="number" name="quantity" class="form-control" value="10" min="1" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">পেমেন্ট মেথড</label>
                                <select name="payment_method" class="form-select">
                                    <option value="Cash">ক্যাশ</option>
                                    <option value="bKash">বিকাশ</option>
                                    <option value="Nagad">নগদ</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold">ক্যাশ মেমো প্রিন্ট ও সেল নিশ্চিত করুন</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">বর্তমান ঔষধ স্টক ও মেয়াদ</h5>
                    <a href="medicines.php" class="btn btn-sm btn-primary">+ নতুন ঔষধ যুক্ত করুন</a>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>নাম</th>
                                <th>কোম্পানি</th>
                                <th>মূল্য</th>
                                <th>মজুদ</th>
                                <th>মেয়াদ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicines as $m): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m['name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($m['generic_name']) ?></small></td>
                                <td><?= htmlspecialchars($m['company']) ?></td>
                                <td><?= $m['sale_price'] ?> ৳</td>
                                <td>
                                    <?php if ($m['stock_quantity'] < 10): ?>
                                        <span class="badge bg-danger"><?= $m['stock_quantity'] ?> (স্বল্প স্টক)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $m['stock_quantity'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $m['expiry_date'] ?? 'N/A' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
