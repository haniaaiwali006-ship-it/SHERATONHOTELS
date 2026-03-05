<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxStay - Premium Hotel Booking</title>
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

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(26, 31, 46, 0.7), rgba(26, 31, 46, 0.7)),
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-top: 80px;
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 64px;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero p {
            font-family: 'Lora', serif;
            font-size: 22px;
            margin-bottom: 40px;
            color: #f8f5f0;
        }

        /* Search Form */
        .search-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: -50px auto 50px;
            position: relative;
            z-index: 2;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-family: 'Lora', serif;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #d4af37;
        }

        .search-btn {
            background: linear-gradient(45deg, #d4af37, #b8941f);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            font-family: 'Lora', serif;
            font-weight: 500;
            letter-spacing: 1px;
            display: block;
            margin: 0 auto;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        /* Featured Hotels */
        .featured-hotels {
            padding: 80px 0;
            background-color: #f8f5f0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: #1a1f2e;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #666;
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }

        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .hotel-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .hotel-image {
            height: 220px;
            background-size: cover;
            background-position: center;
        }

        .hotel-info {
            padding: 25px;
        }

        .hotel-name {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: #1a1f2e;
            margin-bottom: 10px;
        }

        .hotel-location {
            color: #666;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .hotel-rating {
            color: #d4af37;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .hotel-price {
            font-size: 24px;
            color: #1a1f2e;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .view-btn {
            background: #1a1f2e;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            font-family: 'Lora', serif;
            font-size: 16px;
        }

        .view-btn:hover {
            background: #2c3e50;
        }

        /* Footer */
        footer {
            background-color: #1a1f2e;
            color: #f8f5f0;
            padding: 60px 0 20px;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 42px;
            }

            .hero p {
                font-size: 18px;
            }

            .search-form {
                padding: 20px;
                margin: -30px 20px 30px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .nav-container {
                flex-direction: column;
                gap: 20px;
            }

            nav ul {
                gap: 15px;
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Experience Luxury, Live Grandly</h1>
            <p>Discover the world's finest hotels and resorts, where every stay becomes a cherished memory</p>
        </div>
    </section>

    <!-- Search Form -->
    <div class="container">
        <form action="hotels.php" method="GET" class="search-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="destination">Destination</label>
                    <input type="text" id="destination" name="location" placeholder="Where do you want to stay?" required>
                </div>
                <div class="form-group">
                    <label for="checkin">Check-in Date</label>
                    <input type="date" id="checkin" name="check_in" required>
                </div>
                <div class="form-group">
                    <label for="checkout">Check-out Date</label>
                    <input type="date" id="checkout" name="check_out" required>
                </div>
                <div class="form-group">
                    <label for="guests">Guests</label>
                    <select id="guests" name="guests">
                        <option value="1">1 Guest</option>
                        <option value="2" selected>2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="5+">5+ Guests</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="search-btn">Search Hotels</button>
        </form>
    </div>

    <!-- Featured Hotels -->
    <section class="featured-hotels">
        <div class="container">
            <div class="section-title">
                <h2>Featured Luxury Stays</h2>
                <p>Handpicked selection of premium hotels offering exceptional experiences</p>
            </div>
            
            <div class="hotels-grid">
                <?php
                $stmt = $conn->query("SELECT * FROM hotels LIMIT 3");
                $featuredHotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($featuredHotels as $hotel):
                ?>
                <div class="hotel-card">
                    <div class="hotel-image" style="background-image: url('<?php echo $hotel['image_url']; ?>')"></div>
                    <div class="hotel-info">
                        <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <p class="hotel-location"><?php echo htmlspecialchars($hotel['location']); ?></p>
                        <div class="hotel-rating">★ <?php echo $hotel['rating']; ?></div>
                        <div class="hotel-price">$<?php echo number_format($hotel['price'], 2); ?><span style="font-size: 14px; color: #666;"> / night</span></div>
                        <a href="hotels.php?location=<?php echo urlencode($hotel['location']); ?>">
                            <button class="view-btn">View Details</button>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

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
        // Set minimum date for check-in to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        
        // Update checkout min date when checkin changes
        document.getElementById('checkin').addEventListener('change', function() {
            document.getElementById('checkout').min = this.value;
        });
    </script>
</body>
</html>
