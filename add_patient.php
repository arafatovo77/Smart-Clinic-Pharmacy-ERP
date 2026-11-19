<?php
require_once 'config.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = "P-" . rand(10000, 99999);
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $doctor_fee = (float)$_POST['doctor_fee'];
    $test_fee = (float)$_POST['test_fee'];
    $discount = (float)$_POST['discount'];
    $payment_method = $_POST['payment_method'];

    $total = ($doctor_fee + $test_fee) - $discount;

    $stmt = $pdo->prepare("INSERT INTO patients (patient_uid, full_name, phone, gender, age, blood_group) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$uid, $name, $phone, $gender, $age, $blood_group])) {
        $patient_id = $pdo->lastInsertId();

        $invoice = "INV-" . date("Ymd") . "-" . rand(100, 999);
        $stmt_bill = $pdo->prepare("INSERT INTO billings (invoice_no, patient_id, doctor_fee, test_fee, discount, total_amount, paid_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_bill->execute([$invoice, $patient_id, $doctor_fee, $test_fee, $discount, $total, $total, $payment_method]);

        $message = "রোগী সফলভাবে নিবন্ধিত হয়েছে! পেশেন্ট আইডি: <strong>$uid</strong> | ইনভয়েস: <strong>$invoice</strong> | পরিশোধিত: <strong>$total ৳ ($payment_method)</strong>";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নতুন রোগী ও বিলিং - স্মার্ট ক্লিনিক</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">👤 নতুন রোগী নিবন্ধন ও রিসেপশন বিলিং</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← ড্যাশবোর্ডে ফিরুন</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">রোগীর পুরো নাম *</label>
                            <input type="text" name="name" class="form-control" required placeholder="উদাঃ মোঃ রফিকুল ইসলাম">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">মোবাইল নম্বর *</label>
                                <input type="text" name="phone" class="form-control" required placeholder="017xxxxxxxx">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">বয়স</label>
                                <input type="number" name="age" class="form-control" value="30">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">লিঙ্গ</label>
                                <select name="gender" class="form-select">
                                    <option value="Male">পুরুষ</option>
                                    <option value="Female">মহিলা</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">রক্তের গ্রুপ</label>
                                <select name="blood_group" class="form-select">
                                    <option value="A+">A+</option>
                                    <option value="B+">B+</option>
                                    <option value="O+">O+</option>
                                    <option value="AB+">AB+</option>
                                    <option value="A-">A-</option>
                                    <option value="B-">B-</option>
                                    <option value="O-">O-</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">ডাক্তার ভিজিট ফি (৳) *</label>
                                <input type="number" name="doctor_fee" class="form-control" value="500" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">প্যাথলজি / টেস্ট ফি (৳)</label>
                                <input type="number" name="test_fee" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">ডিসকাউন্ট (৳)</label>
                                <input type="number" name="discount" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">পেমেন্ট মেথড</label>
                                <select name="payment_method" class="form-select">
                                    <option value="Cash">ক্যাশ (Cash)</option>
                                    <option value="bKash">বিকাশ (bKash)</option>
                                    <option value="Nagad">নগদ (Nagad)</option>
                                    <option value="Card">কার্ড (Card)</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">সংরক্ষণ করুন ও রসিদ তৈরি করুন</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
