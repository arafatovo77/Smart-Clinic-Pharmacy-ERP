-- স্মার্ট ক্লিনিক ও ফার্মেসি ম্যানেজমেন্ট ডাটাবেস (SQLite সংস্করণ — আসল MySQL database.sql থেকে রূপান্তরিত)

-- ১. ইউজার টেবিল (এডমিন, ডাক্তার, রিসেপশন)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role TEXT DEFAULT 'receptionist',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ২. ডাক্তারদের তালিকা
CREATE TABLE IF NOT EXISTS doctors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(150) NOT NULL,
    specialization VARCHAR(150),
    consultation_fee DECIMAL(10,2) DEFAULT 500.00,
    room_no VARCHAR(50),
    available_days VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ৩. রোগী বা পেশেন্ট টেবিল
CREATE TABLE IF NOT EXISTS patients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_uid VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender TEXT DEFAULT 'Male',
    age INT,
    blood_group VARCHAR(10),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ৪. অ্যাপয়েন্টমেন্ট ও সিরিয়াল
CREATE TABLE IF NOT EXISTS appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INT,
    doctor_name VARCHAR(150),
    serial_no INT NOT NULL,
    appointment_date DATE NOT NULL,
    status TEXT DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- ৫. ক্লিনিক বিলিং ও ইনভয়েস
CREATE TABLE IF NOT EXISTS billings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    patient_id INT,
    doctor_fee DECIMAL(10,2) DEFAULT 0.00,
    test_fee DECIMAL(10,2) DEFAULT 0.00,
    discount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    due_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_method TEXT DEFAULT 'Cash',
    payment_status TEXT DEFAULT 'Paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- ৬. ফার্মেসি ওষুধের তালিকা ও স্টক
CREATE TABLE IF NOT EXISTS medicines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(150) NOT NULL,
    generic_name VARCHAR(150),
    category VARCHAR(50) DEFAULT 'Tablet',
    company VARCHAR(100),
    buy_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_quantity INT DEFAULT 0,
    expiry_date DATE,
    rack_no VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ৭. ফার্মেসি ওষুধ বিক্রয় ও বিলিং হিস্ট্রি
CREATE TABLE IF NOT EXISTS pharmacy_sales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bill_no VARCHAR(50) UNIQUE NOT NULL,
    patient_id INT NULL,
    customer_name VARCHAR(100),
    payment_method TEXT DEFAULT 'Cash',
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ৮. বিক্রিত প্রতিটি ওষুধের বিবরণী
CREATE TABLE IF NOT EXISTS pharmacy_sale_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sale_id INT,
    medicine_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES pharmacy_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE SET NULL
);

-- প্রাথমিক কিছু বাংলাদেশি ওষুধের ডেমো ডেটা
INSERT INTO medicines (name, generic_name, category, company, buy_price, sale_price, stock_quantity, expiry_date, rack_no) VALUES
('Napa Extra 500mg', 'Paracetamol + Caffeine', 'Tablet', 'Beximco Pharma', 2.00, 2.50, 500, '2028-12-31', 'A-1'),
('Seclo 20mg', 'Omeprazole', 'Capsule', 'Square Pharma', 5.00, 6.00, 300, '2028-06-30', 'A-2'),
('Sergel 20mg', 'Esomeprazole', 'Capsule', 'Healthcare Pharma', 6.00, 7.00, 250, '2028-09-15', 'A-3'),
('Monas 10mg', 'Montelukast', 'Tablet', 'ACME Lab', 14.00, 16.00, 120, '2027-11-20', 'B-1'),
('Azithrocin 500mg', 'Azithromycin', 'Tablet', 'Square Pharma', 30.00, 35.00, 8, '2027-08-10', 'C-1');
