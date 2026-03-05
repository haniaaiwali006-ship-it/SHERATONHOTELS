<?php
include 'db_connection.php';

// Get hotel details
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$guests = isset($_GET['guests']) ? intval($_GET['guests']) : 2;

// Fetch hotel details
$stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    header('Location: hotels.php');
    exit();
}

// Calculate nights and total price
if ($check_in && $check_out) {
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_price = $hotel['price'] * $nights;
} else {
    $nights = 1;
    $total_price = $hotel['price'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guests = $_POST['guests'];
    
    // Calculate final total
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_price = $hotel['price'] * $nights;
    
    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (hotel_id, user_name, user_email, user_phone, check_in, check_out, guests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$hotel_id, $name, $email, $phone, $check_in, $check_out, $guests, $total_price]);
    
    $booking_id = $conn->lastInsertId();
    
    // Redirect to confirmation page
    header("Location: confirmation.php?booking_id=$booking_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxStay - Complete Your Booking</title>
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
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: #1a1f2e;
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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

        /* Booking Layout */
        .booking-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            margin: 40px 0;
        }

        /* Hotel Summary */
        .hotel-summary {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .hotel-summary h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 20px;
            color: #1a1f2e;
        }

        .hotel-image-large {
            height: 300px;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .hotel-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item h3 {
            font-family: 'Lora', serif;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .detail-item p {
            font-size: 16px;
            color: #2c3e50;
        }

        /* Booking Form */
        .booking-form-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px;
        }

        .booking-form-container h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 30px;
            color: #1a1f2e;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-family: 'Lora', serif;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
        }

        /* Price Summary */
        .price-summary {
            background: #f8f5f0;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .price-row:last-child {
            border-bottom: none;
        }

        .price-row.total {
            font-size: 20px;
            font-weight: 700;
            color: #1a1f2e;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
        }

        .confirm-btn {
            background: linear-gradient(45deg, #d4af37, #b8941f);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s;
            font-family: 'Lora', serif;
            width: 100%;
            margin-top: 20px;
        }

        .confirm-btn:hover {
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background-color: #1a1f2e;
            color: #f8f5f0;
            padding: 60px 0 20px;
            margin-top: 60px;
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
        @media (max-width: 900px) {
            .booking-layout {
                grid-template-columns: 1fr;
            }
            
            .booking-form-container {
                position: static;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 20px;
            }
            
            body {
                padding-top: 120px;
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
        <div class="booking-layout">
            <!-- Left Column: Hotel Summary -->
            <div class="booking-details">
                <div class="hotel-summary">
                    <h2>Booking Details</h2>
                    <div class="hotel-image-large" style="background-image: url('<?php echo $hotel['image_url']; ?>')"></div>
                    
                    <h2 style="margin-bottom: 10px;"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;">📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
                    
                    <div class="hotel-details-grid">
                        <div class="detail-item">
                            <h3>Check-in Date</h3>
                            <p id="display_check_in"><?php echo date('F d, Y', strtotime($check_in)); ?></p>
                        </div>
                        <div class="detail-item">
                            <h3>Check-out Date</h3>
                            <p id="display_check_out"><?php echo date('F d, Y', strtotime($check_out)); ?></p>
                        </div>
                        <div class="detail-item">
                            <h3>Guests</h3>
                            <p id="display_guests"><?php echo $guests; ?> Guest<?php echo $guests > 1 ? 's' : ''; ?></p>
                        </div>
                        <div class="detail-item">
                            <h3>Nights</h3>
                            <p id="display_nights"><?php echo $nights; ?> Night<?php echo $nights > 1 ? 's' : ''; ?></p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <h3 style="font-family: 'Lora', serif; margin-bottom: 10px;">Amenities Included:</h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php 
                            $amenities = explode(',', $hotel['amenities']);
                            foreach ($amenities as $amenity):
                            ?>
                                <span style="background: #f8f5f0; padding: 8px 15px; border-radius: 20px; font-size: 14px;"><?php echo trim($amenity); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Form -->
            <div class="booking-form-container">
                <h2>Complete Your Booking</h2>
                
                <form method="POST" action="" id="bookingForm">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                    
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="check_in">Check-in Date *</label>
                        <input type="date" id="check_in" name="check_in" value="<?php echo $check_in; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="check_out">Check-out Date *</label>
                        <input type="date" id="check_out" name="check_out" value="<?php echo $check_out; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="guests">Number of Guests *</label>
                        <select id="guests" name="guests" required>
                            <option value="1" <?php echo $guests == 1 ? 'selected' : ''; ?>>1 Guest</option>
                            <option value="2" <?php echo $guests == 2 ? 'selected' : ''; ?>>2 Guests</option>
                            <option value="3" <?php echo $guests == 3 ? 'selected' : ''; ?>>3 Guests</option>
                            <option value="4" <?php echo $guests == 4 ? 'selected' : ''; ?>>4 Guests</option>
                            <option value="5" <?php echo $guests == 5 ? 'selected' : ''; ?>>5 Guests</option>
                        </select>
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="price-summary">
                        <h3 style="font-family: 'Lora', serif; margin-bottom: 20px; color: #1a1f2e;">Price Summary</h3>
                        
                        <div class="price-row">
                            <span><?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?> × $<?php echo number_format($hotel['price'], 2); ?></span>
                            <span>$<?php echo number_format($hotel['price'] * $nights, 2); ?></span>
                        </div>
                        
                        <div class="price-row">
                            <span>Service fee</span>
                            <span>$0.00</span>
                        </div>
                        
                        <div class="price-row">
                            <span>Taxes</span>
                            <span>$<?php echo number_format($hotel['price'] * $nights * 0.1, 2); ?></span>
                        </div>
                        
                        <div class="price-row total">
                            <span>Total (USD)</span>
                            <span id="total_price">$<?php echo number_format($total_price * 1.1, 2); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="confirm-btn">Confirm Booking</button>
                </form>
            </div>
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

    <script>
        // Update dates and prices dynamically
        const hotelPrice = <?php echo $hotel['price']; ?>;
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        const guestsInput = document.getElementById('guests');
        
        function updateBookingSummary() {
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const guests = guestsInput.value;
            
            // Update display
            if (!isNaN(checkIn.getTime())) {
                document.getElementById('display_check_in').textContent = checkIn.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
            }
            
            if (!isNaN(checkOut.getTime())) {
                document.getElementById('display_check_out').textContent = checkOut.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
            }
            
            document.getElementById('display_guests').textContent = guests + ' Guest' + (guests > 1 ? 's' : '');
            document.getElementById('display_nights').textContent = nights + ' Night' + (nights > 1 ? 's' : '');
            
            // Update price
            const subtotal = hotelPrice * nights;
            const tax = subtotal * 0.1;
            const total = subtotal + tax;
            
            document.getElementById('total_price').textContent = '$' + total.toFixed(2);
        }
        
        // Set minimum dates
        const today = new Date().toISOString().split('T')[0];
        checkInInput.min = today;
        
        checkInInput.addEventListener('change', function() {
            checkOutInput.min = this.value;
            updateBookingSummary();
        });
        
        checkOutInput.addEventListener('change', updateBookingSummary);
        guestsInput.addEventListener('change', updateBookingSummary);
        
        // Initialize
        updateBookingSummary();
    </script>
</body>
</html>
