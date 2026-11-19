<?php
require_once 'config.php';

// লাইভ কাউন্টার কুয়েরি
$total_patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$today_clinic_income = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM billings WHERE DATE(created_at) = date('now')")->fetchColumn();
$today_pharmacy_income = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM pharmacy_sales WHERE DATE(created_at) = date('now')")->fetchColumn();
$low_stock_count = $pdo->query("SELECT COUNT(*) FROM medicines WHERE stock_quantity < 10")->fetchColumn();

// সাম্প্রতিক ৫ জন রোগী
$recent_patients = $pdo->query("SELECT * FROM patients ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d6efd">
    <link rel="manifest" href="manifest.json">
    <title>স্মার্ট ক্লিনিক ও ফার্মেসি ম্যানেজমেন্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #1e293b; color: #fff; }
        .sidebar a { color: #cbd5e1; text-decoration: none; padding: 12px 20px; display: block; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #0d6efd; color: #fff; }
        .stat-card { border: none; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .mobile-nav { display: none; }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            body { padding-bottom: 75px; }
            .mobile-nav {
                display: flex; position: fixed; bottom: 0; left: 0; right: 0;
                background: #ffffff; box-shadow: 0 -2px 12px rgba(0,0,0,0.1);
                justify-content: space-around; padding: 10px 0; z-index: 1000;
            }
            .mobile-nav a { text-decoration: none; color: #475569; font-size: 13px; text-align: center; font-weight: 600; }
            .mobile-nav a.active { color: #0d6efd; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- পিসি/ল্যাপটপ সাইডবার -->
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="text-white text-center py-2 border-bottom border-secondary mb-3">🏥 স্মার্ট ক্লিনিক</h5>
            <a href="index.php" class="active">📊 ড্যাশবোর্ড</a>
            <a href="add_patient.php">👤 নতুন রোগী ও বিলিং</a>
            <a href="pharmacy_pos.php">💊 ফার্মেসি পিওএস (POS)</a>
            <a href="medicines.php">📦 ঔষধ স্টক ইনভেন্টরি</a>
            <a href="reports.php">📑 দৈনিক হিসাব রিপোর্ট</a>
        </div>

        <!-- মূল কন্টেন্ট -->
        <div class="col-md-9 col-lg-10 p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0 fw-bold">ক্লিনিক ও ফার্মেসি ড্যাশবোর্ড</h4>
                    <small class="text-muted">পিসি ও অ্যান্ড্রয়েড মোবাইল লাইভ সিঙ্ক</small>
                </div>
                <a href="add_patient.php" class="btn btn-primary btn-sm">+ নতুন রোগী</a>
            </div>

            <!-- ৪টি মূল সামারি কার্ড -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-primary border-4">
                        <small class="text-muted">মোট নিবন্ধিত রোগী</small>
                        <h3 class="fw-bold text-primary mb-0"><?= $total_patients ?></h3>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-success border-4">
                        <small class="text-muted">আজকের ক্লিনিক আয়</small>
                        <h3 class="fw-bold text-success mb-0"><?= number_format($today_clinic_income, 0) ?> ৳</h3>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-info border-4">
                        <small class="text-muted">আজকের ফার্মেসি বিক্রি</small>
                        <h3 class="fw-bold text-info mb-0"><?= number_format($today_pharmacy_income, 0) ?> ৳</h3>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-danger border-4">
                        <small class="text-muted">স্বল্প স্টক এলার্ট</small>
                        <h3 class="fw-bold text-danger mb-0"><?= $low_stock_count ?> টি</h3>
                    </div>
                </div>
            </div>

            <!-- কুইক অ্যাকশন বাটন -->
            <div class="card stat-card p-3 mb-4">
                <h6 class="fw-bold mb-3">দ্রুত মডিউল অ্যাক্সেস</h6>
                <div class="row g-2">
                    <div class="col-12 col-md-3">
                        <a href="add_patient.php" class="btn btn-outline-primary w-100 py-3 fw-semibold">👤 রোগী ভর্তি ও ইনভয়েস</a>
                    </div>
                    <div class="col-12 col-md-3">
                        <a href="pharmacy_pos.php" class="btn btn-outline-success w-100 py-3 fw-semibold">💊 ফার্মেসি ঔষধ বিক্রি</a>
                    </div>
                    <div class="col-12 col-md-3">
                        <a href="medicines.php" class="btn btn-outline-secondary w-100 py-3 fw-semibold">📦 নতুন ঔষধ স্টক যুক্ত</a>
                    </div>
                    <div class="col-12 col-md-3">
                        <a href="reports.php" class="btn btn-outline-dark w-100 py-3 fw-semibold">📊 আজকের ক্যাশ রিপোর্ট</a>
                    </div>
                </div>
            </div>

            <!-- সাম্প্রতিক রোগী তালিকা -->
            <div class="card stat-card p-3">
                <h6 class="fw-bold mb-3">সাম্প্রতিক নিবন্ধিত রোগী</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>আইডি (UID)</th>
                                <th>নাম</th>
                                <th>মোবাইল</th>
                                <th>বয়স ও লিঙ্গ</th>
                                <th>রক্তের গ্রুপ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_patients as $p): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($p['patient_uid']) ?></span></td>
                                <td><strong><?= htmlspecialchars($p['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($p['phone']) ?></td>
                                <td><?= $p['age'] ?> বছর (<?= $p['gender'] ?>)</td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars($p['blood_group']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- অ্যান্ড্রয়েড মোবাইলের বটম নেভিগেশন বার -->
<div class="mobile-nav">
    <a href="index.php" class="active">📊<br>হোম</a>
    <a href="add_patient.php">👤<br>রোগী এন্ট্রি</a>
    <a href="pharmacy_pos.php">💊<br>ফার্মেসি</a>
    <a href="medicines.php">📦<br>স্টক</a>
    <a href="reports.php">📑<br>রিপোর্ট</a>
</div>

</body>
</html>
