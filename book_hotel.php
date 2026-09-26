<?php
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';

// Fetch all available hotels
$hotels = $conn->query("
    SELECT hotel_id, name, description, location, photo, rating, price, rooms_available 
    FROM hotel 
    WHERE rooms_available > 0 
    ORDER BY location ASC
");

$today = date('Y-m-d', strtotime('+1 day')); // Tomorrow
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotel - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="container py-5">
        <h3 class="text-success fw-bold mb-4 text-center">Available Hotels</h3>

        <div class="row mb-5 justify-content-center">
            <?php if ($hotels->num_rows > 0): ?>
            <?php while ($h = $hotels->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-success shadow-sm">
                    <div class="card-header bg-success text-white text-center">
                        <h1 class="h5 mb-0"><?php echo $h['location']; ?></h1>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($h['photo'])): ?>
                        <img src="<?php echo $h['photo']; ?>" class="img-fluid rounded mb-3" alt="Hotel Photo">
                        <?php endif; ?>
                        <h5 class="card-title fw-bold"><?php echo $h['name']; ?></h5>
                        <p class="mb-1"><strong>Rating:</strong> <?php echo $h['rating']; ?>/5</p>
                        <p class="mb-1"><strong>Price:</strong> <?php echo $h['price']; ?> BDT/night</p>
                        <p class="mb-1"><strong>Rooms Available:</strong> <?php echo $h['rooms_available']; ?></p>
                        <p class="mb-0 text-muted"><strong>Description:</strong> <?php echo $h['description']; ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="text-center text-danger">No hotels available right now.</p>
            <?php endif; ?>
        </div>

        <h3 class="text-success fw-bold mb-4 text-center">Book a Hotel</h3>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow p-4">
                    <form action="hotel_booking_manager.php" method="POST">
                        <!-- Select Hotel -->
                        <div class="mb-3">
                            <label for="hotel_id" class="form-label fw-semibold">Select Hotel</label>
                            <select name="hotel_id" id="hotel_id" class="form-select" required>
                                <option value="">-- Choose Hotel --</option>
                                <?php
                                $hotels->data_seek(0);
                                while ($h = $hotels->fetch_assoc()):
                                ?>
                                <option value="<?php echo $h['hotel_id']; ?>">
                                    <?php echo $h['name']; ?> - <?php echo $h['location']; ?>
                                    (<?php echo $h['rooms_available']; ?> rooms)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Number of Rooms -->
                        <div class="mb-3">
                            <label for="rooms" class="form-label fw-semibold">Number of Rooms</label>
                            <input type="number" name="rooms" id="rooms" class="form-control"
                                placeholder="Enter number of rooms" min="1" required>
                        </div>

                        <!-- Check-in Date -->
                        <div class="mb-3">
                            <label for="checkin" class="form-label fw-semibold">Check-in Date</label>
                            <input type="date" name="checkin" id="checkin" class="form-control" required
                                min="<?php echo $today; ?>">
                        </div>

                        <!-- Check-out Date -->
                        <div class="mb-3">
                            <label for="checkout" class="form-label fw-semibold">Check-out Date</label>
                            <input type="date" name="checkout" id="checkout" class="form-control" required
                                min="<?php echo $today; ?>">
                        </div>

                        <!-- Contact -->
                        <div class="mb-3">
                            <label for="contact" class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact" id="contact" class="form-control"
                                placeholder="Enter phone number" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success rounded-pill py-2 fw-semibold">
                                Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>