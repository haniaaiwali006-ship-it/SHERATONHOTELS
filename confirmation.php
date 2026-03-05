<?php
include 'db_connection.php';

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Fetch booking details with hotel information
$stmt = $conn->prepare("
    SELECT b.*, h.name as hotel_name, h.location, h.image_url 
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.id 
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxStay - Booking Confirmed</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lora:wght@400;500&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f5f0;
            color: #2c3e50;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
            flex: 1;
        }

        /* Header Styles */
        header {
            background-color: #1a1f2e;
            padding: 20px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #d4af37;
            text-decoration: none;
            letter-spacing: 1px;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav a {
            color: #f8f5f0;
            text-decoration: none;
            font-family: 'Lora', serif;
            font-size: 18px;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #d4af37;
        }

        /* Confirmation Card */
        .confirmation-card {
            background: white;
            border-radius: 15px;
            padding: 50px;
            margin: 60px auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
            border: 1px solid #e8e1d4;
        }

        .confirmation-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #d4af37, #b8941f);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        .confirmation-card h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .confirmation-message {
            font-size: 20px;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.8;
        }

        .booking-id {
            background: #f8f5f0;
            padding: 10px 20px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 40px;
            font-family: 'Lora', serif;
            font-size: 18px;
        }

        /* Booking Details */
        .booking-details {
            background: #f8f5f0;
            border-radius: 10px;
            padding: 30px;
            margin: 40px 0;
            text-align: left;
        }

        .booking-details h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 25px;
            color: #2c3e50;
            text-align: center;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-item h3 {
            font-family: 'Lora', serif;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .detail-item p {
            font-size: 18px;
            color: #2c3e50;
        }

        .hotel-preview {
            display: flex;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .hotel-image {
            width: 100px;
            height: 100px;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            margin-right: 20px;
        }

        .hotel-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .hotel-info p {
            color: #666;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .btn {
            padding: 15px 40px;
            border-radius: 5px;
            text-decoration: none;
            font-family: 'Lora', serif;
            font-size: 16px;
            transition: transform 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(45deg, #d4af37, #b8941f);
            color: white;
        }

        .btn-secondary {
            background: #1a1f2e;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background-color: #1a1f2e;
            color: #f8f5f0;
            padding: 60px 0 20px;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 20px;
            color: #d4af37;
        }

        .footer-section p {
            color: #bdc3c7;
            line-height: 1.8;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #34495e;
            color: #95a5a6;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .confirmation-card {
                padding: 30px 20px;
                margin: 40px 20px;
            }
            
            .confirmation-card h1 {
                font-size: 32px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">LuxStay</a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="hotels.php">Hotels</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="confirmation-card">
            <div class="confirmation-icon">✓</div>
            
            <h1>Booking Confirmed!</h1>
            
            <p class="confirmation-message">
                Thank you for choosing LuxStay. Your luxury experience is now secured.<br>
                A confirmation email has been sent to <?php echo htmlspecialchars($booking['user_email']); ?>
            </p>
            
            <div class="booking-id">
                Booking Reference: <strong>LX<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></strong>
            </div>
            
            <!-- Booking Details -->
            <div class="booking-details">
                <h2>Booking Summary</h2>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <h3>Guest Name</h3>
                        <p><?php echo htmlspecialchars($booking['user_name']); ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($booking['user_email']); ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Phone</h3>
                        <p><?php echo htmlspecialchars($booking['user_phone']); ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Booking Date</h3>
                        <p><?php echo date('F d, Y', strtotime($booking['booking_date'])); ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Check-in</h3>
                        <p><?php echo date('F d, Y', strtotime($booking['check_in'])); ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Check-out</h3>
                        <p><?php echo date('F d, Y', strtotime($booking['check_out'])); ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Guests</h3>
                        <p><?php echo $booking['guests']; ?> Guest<?php echo $booking['guests'] > 1 ? 's' : ''; ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Total Amount</h3>
                        <p>$<?php echo number_format($booking['total_price'], 2); ?></p>
                    </div>
                </div>
                
                <!-- Hotel Preview -->
                <div class="hotel-preview">
                    <div class="hotel-image" style="background-image: url('<?php echo $booking['image_url']; ?>')"></div>
                    <div class="hotel-info">
                        <h3><?php echo htmlspecialchars($booking['hotel_name']); ?></h3>
                        <p>📍 <?php echo htmlspecialchars($booking['location']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">Back to Home</a>
                <a href="hotels.php" class="btn btn-secondary">Book Another Stay</a>
            </div>
            
            <p style="margin-top: 30px; color: #666; font-size: 16px;">
                Need help? Contact our customer support at support@luxstay.com
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>LuxStay</h3>
                    <p>Your gateway to luxury accommodations worldwide. Experience premium hospitality with our curated selection of luxury hotels and resorts.</p>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <p>Email: info@luxstay.com<br>
                    Phone: +1 (555) 123-4567<br>
                    Address: 123 Luxury Lane, Premium City</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="index.php" style="color: #bdc3c7; text-decoration: none;">Home</a><br>
                    <a href="hotels.php" style="color: #bdc3c7; text-decoration: none;">All Hotels</a><br>
                    <a href="#" style="color: #bdc3c7; text-decoration: none;">Special Offers</a></p>
                </div>
            </div>
            <div class="copyright">
                &copy; 2024 LuxStay. All rights reserved. | Premium Hotel Booking Platform
            </div>
        </div>
    </footer>
</body>
</html>
