<?php
require_once 'config.php';

$clinic_bills = $pdo->query("SELECT b.*, p.full_name, p.patient_uid FROM billings b LEFT JOIN patients p ON b.patient_id = p.id ORDER BY b.id DESC LIMIT 20")->fetchAll();
$pharmacy_bills = $pdo->query("SELECT * FROM pharmacy_sales ORDER BY id DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>দৈনিক আয় ও ক্যাশ রিপোর্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">📑 ক্লিনিক ও ফার্মেসি হিসাব রিপোর্ট</h4>
        <div>
            <button onclick="window.print()" class="btn btn-dark btn-sm me-2">🖨️ রিপোর্ট প্রিন্ট করুন</button>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">← ড্যাশবোর্ডে ফিরুন</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">ক্লিনিক রিসেপশন ইনভয়েস তালিকা</h6>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ইনভয়েস</th>
                                <th>রোগীর নাম</th>
                                <th>মেথড</th>
                                <th>টাকা</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clinic_bills as $cb): ?>
                            <tr>
                                <td><small><?= htmlspecialchars($cb['invoice_no']) ?></small></td>
                                <td><?= htmlspecialchars($cb['full_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= $cb['payment_method'] ?></span></td>
                                <td class="fw-bold"><?= number_format($cb['paid_amount'], 0) ?> ৳</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">ফার্মেসি ক্যাশ মেমো তালিকা</h6>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>মেমো নং</th>
                                <th>গ্রাহক</th>
                                <th>মেথড</th>
                                <th>টাকা</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pharmacy_bills as $pb): ?>
                            <tr>
                                <td><small><?= htmlspecialchars($pb['bill_no']) ?></small></td>
                                <td><?= htmlspecialchars($pb['customer_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= $pb['payment_method'] ?></span></td>
                                <td class="fw-bold"><?= number_format($pb['paid_amount'], 0) ?> ৳</td>
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
