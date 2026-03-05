<?php 
include 'db_connection.php';

// Get search parameters
$location = isset($_GET['location']) ? $_GET['location'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$guests = isset($_GET['guests']) ? $_GET['guests'] : '2';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : 1000;

// Build query
$query = "SELECT * FROM hotels WHERE 1=1";
$params = [];

if (!empty($location)) {
    $query .= " AND location LIKE :location";
    $params[':location'] = "%$location%";
}

if ($min_price > 0) {
    $query .= " AND price >= :min_price";
    $params[':min_price'] = $min_price;
}

if ($max_price < 1000) {
    $query .= " AND price <= :max_price";
    $params[':max_price'] = $max_price;
}

// Add sorting
switch ($sort) {
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'rating_desc':
        $query .= " ORDER BY rating DESC";
        break;
    case 'price_asc':
    default:
        $query .= " ORDER BY price ASC";
        break;
}

// Prepare and execute query
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxStay - Luxury Hotels</title>
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

        /* Filters Section */
        .filters-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 100px auto 30px;
            max-width: 1200px;
        }

        .filters-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            margin-bottom: 0;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-family: 'Lora', serif;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: white;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #d4af37;
        }

        .filter-btn {
            background: linear-gradient(45deg, #d4af37, #b8941f);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s;
            font-family: 'Lora', serif;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
        }

        /* Results Header */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .results-count {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #1a1f2e;
        }

        .sort-options {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-options label {
            font-family: 'Lora', serif;
        }

        /* Hotels Grid */
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
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

        .hotel-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
            font-size: 15px;
        }

        .hotel-rating {
            color: #d4af37;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .amenity-tag {
            background: #f8f5f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            color: #2c3e50;
        }

        .price-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .price {
            font-size: 28px;
            color: #1a1f2e;
            font-weight: 700;
        }

        .price span {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }

        .book-btn {
            background: #1a1f2e;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            font-family: 'Lora', serif;
            font-size: 16px;
        }

        .book-btn:hover {
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

        /* Responsive */
        @media (max-width: 768px) {
            .filters-container {
                grid-template-columns: 1fr;
            }
            
            .results-header {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .hotels-grid {
                grid-template-columns: 1fr;
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
                    <li><a href="hotels.php" style="color: #d4af37;">Hotels</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Filters Section -->
    <div class="container">
        <div class="filters-section">
            <form method="GET" action="hotels.php" class="filters-container">
                <div class="filter-group">
                    <label for="location">Destination</label>
                    <input type="text" id="location" name="location" placeholder="City or hotel name" value="<?php echo htmlspecialchars($location); ?>">
                </div>
                <div class="filter-group">
                    <label for="check_in">Check-in</label>
                    <input type="date" id="check_in" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                </div>
                <div class="filter-group">
                    <label for="check_out">Check-out</label>
                    <input type="date" id="check_out" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                </div>
                <div class="filter-group">
                    <label for="sort">Sort By</label>
                    <select id="sort" name="sort">
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating_desc" <?php echo $sort == 'rating_desc' ? 'selected' : ''; ?>>Best Rated</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="min_price">Min Price ($)</label>
                    <input type="range" id="min_price" name="min_price" min="0" max="1000" step="50" value="<?php echo $min_price; ?>">
                    <span id="min_price_value">$<?php echo $min_price; ?></span>
                </div>
                <div class="filter-group">
                    <label for="max_price">Max Price ($)</label>
                    <input type="range" id="max_price" name="max_price" min="0" max="2000" step="50" value="<?php echo $max_price; ?>">
                    <span id="max_price_value">$<?php echo $max_price; ?></span>
                </div>
                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </div>

        <!-- Results Header -->
        <div class="results-header">
            <h1 class="results-count"><?php echo count($hotels); ?> Luxury Hotels Found</h1>
            <div class="sort-options">
                <form method="GET" id="sortForm">
                    <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                    <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                    <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                    <label for="sort_select">Sort by:</label>
                    <select id="sort_select" name="sort" onchange="document.getElementById('sortForm').submit()">
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating_desc" <?php echo $sort == 'rating_desc' ? 'selected' : ''; ?>>Best Rated</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Hotels Grid -->
        <div class="hotels-grid">
            <?php if (count($hotels) > 0): ?>
                <?php foreach ($hotels as $hotel): ?>
                    <div class="hotel-card">
                        <div class="hotel-image" style="background-image: url('<?php echo $hotel['image_url']; ?>')"></div>
                        <div class="hotel-info">
                            <h2 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                            <p class="hotel-location">📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
                            <p class="hotel-description"><?php echo htmlspecialchars($hotel['description']); ?></p>
                            <div class="hotel-rating">★ <?php echo $hotel['rating']; ?> / 5.0</div>
                            
                            <?php if (!empty($hotel['amenities'])): ?>
                                <div class="hotel-amenities">
                                    <?php 
                                    $amenities = explode(',', $hotel['amenities']);
                                    foreach ($amenities as $amenity):
                                    ?>
                                        <span class="amenity-tag"><?php echo trim($amenity); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="price-section">
                                <div class="price">
                                    $<?php echo number_format($hotel['price'], 2); ?>
                                    <span>per night</span>
                                </div>
                                <a href="booking.php?hotel_id=<?php echo $hotel['id']; ?>&check_in=<?php echo urlencode($check_in); ?>&check_out=<?php echo urlencode($check_out); ?>&guests=<?php echo urlencode($guests); ?>">
                                    <button class="book-btn">Book Now</button>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0;">
                    <h2 style="font-family: 'Playfair Display', serif; margin-bottom: 20px;">No hotels found matching your criteria</h2>
                    <p style="color: #666; margin-bottom: 30px;">Try adjusting your search filters</p>
                    <a href="hotels.php" style="background: #d4af37; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-family: 'Lora', serif;">Show All Hotels</a>
                </div>
            <?php endif; ?>
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
        // Update price display
        document.getElementById('min_price').addEventListener('input', function() {
            document.getElementById('min_price_value').textContent = '$' + this.value;
        });
        
        document.getElementById('max_price').addEventListener('input', function() {
            document.getElementById('max_price_value').textContent = '$' + this.value;
        });
        
        // Set date restrictions
        const today = new Date().toISOString().split('T')[0];
        if (document.getElementById('check_in')) {
            document.getElementById('check_in').min = today;
            document.getElementById('check_in').addEventListener('change', function() {
                document.getElementById('check_out').min = this.value;
            });
        }
    </script>
</body>
</html>
