<?php
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';

$vehicles = $conn->query("
    SELECT vehicle_id, vehicle_name, vehicle_type, vehicle_price, vehicle_availability_number, vehicle_description, vehicle_route 
    FROM vehicle 
    WHERE vehicle_availability_number > 0 
    ORDER BY vehicle_route ASC
");

$today = date('Y-m-d', strtotime('+1 day')); // Tomorrow
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Vehicle - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">

    <?php include 'global_css.php'; ?>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="container py-5">
        <h3 class="text-success fw-bold mb-4 text-center">Available Vehicles</h3>

        <div class="row mb-5 justify-content-center">
            <?php if ($vehicles->num_rows > 0): ?>
            <?php while ($v = $vehicles->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-success shadow-sm">
                    <div class="card-header bg-success text-white text-center">
                        <h1 class="h5 mb-0"><?php echo $v['vehicle_route']; ?></h1>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo $v['vehicle_name']; ?></h5>
                        <p class="mb-1"><strong>Type:</strong> <?php echo $v['vehicle_type']; ?></p>
                        <p class="mb-1"><strong>Price:</strong> <?php echo $v['vehicle_price']; ?> BDT</p>
                        <p class="mb-1"><strong>Available:</strong> <?php echo $v['vehicle_availability_number']; ?></p>
                        <p class="mb-0 text-muted"><strong>Description:</strong>
                            <?php echo $v['vehicle_description']; ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="text-center text-danger">No vehicles available right now.</p>
            <?php endif; ?>
        </div>

        <h3 class="text-success fw-bold mb-4 text-center">Book a Vehicle</h3>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow p-4">
                    <form action="book_vehicle_manager.php" method="POST">
                        <!-- Route Selection -->
                        <div class="mb-3">
                            <label for="pickup_location" class="form-label fw-semibold">Select Route</label>
                            <select name="pickup_location" id="pickup_location" class="form-select" required>
                                <option value="">-- Choose Route --</option>
                                <?php
                                $vehicles->data_seek(0);
                                while ($v = $vehicles->fetch_assoc()):
                                ?>
                                <option value="<?php echo $v['vehicle_route']; ?>">
                                    <?php echo $v['vehicle_route']; ?> (<?php echo $v['vehicle_availability_number']; ?>
                                    available)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Destination -->
                        <div class="mb-3">
                            <label for="destination" class="form-label fw-semibold">Destination</label>
                            <input type="text" name="destination" id="destination" class="form-control"
                                placeholder="Enter destination" required>
                        </div>

                        <!-- Number of Vehicles -->
                        <div class="mb-3">
                            <label for="number_of_vehicles" class="form-label fw-semibold">Number of Vehicles</label>
                            <input type="number" name="number_of_vehicles" id="number_of_vehicles" class="form-control"
                                placeholder="Enter number of vehicles" min="1" required>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="date" class="form-label fw-semibold">Travel Date</label>
                            <input type="date" name="date" id="date" class="form-control" required
                                min="<?php echo $today; ?>">
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label for="time" class="form-label fw-semibold">Travel Time</label>
                            <input type="time" name="time" id="time" class="form-control" required>
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