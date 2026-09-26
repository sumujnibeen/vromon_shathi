<?php

include 'navbar.php';
include 'auth.php';
include 'is_admin.php';


// Fetch all available guides (availability > 0)
$guides = $conn->query("
    SELECT guide_id, name, phone, email, language, price, photo, rating, availability
    FROM guide 
    WHERE availability > 0 
    ORDER BY rating ASC
");

$today = date('Y-m-d', strtotime('+1 day')); // tomorrow
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Guide - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="container py-5">
        <h3 class="text-success fw-bold mb-4 text-center">Available Guides</h3>

        <div class="row mb-5 justify-content-center">
            <?php if ($guides->num_rows > 0): ?>
            <?php while ($g = $guides->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-success shadow-sm">
                    <img src="uploads/<?php echo $g['photo']; ?>" class="card-img-top" alt="Guide Photo"
                        style="height:250px;object-fit:cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($g['name']); ?></h5>
                        <p><strong>Language:</strong> <?php echo htmlspecialchars($g['language']); ?></p>
                        <p><strong>Rating:</strong> ⭐ <?php echo htmlspecialchars($g['rating']); ?>/5</p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($g['price']); ?> BDT/day</p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="text-center text-danger">No guides are available right now.</p>
            <?php endif; ?>
        </div>

        <h3 class="text-success fw-bold mb-4 text-center">Book a Guide</h3>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow p-4">
                    <form action="book_guide_manager.php" method="POST">
                        <!-- Select Guide -->
                        <div class="mb-3">
                            <label for="guide_id" class="form-label fw-semibold">Select Guide</label>
                            <select name="guide_id" id="guide_id" class="form-select" required>
                                <option value="">-- Choose Guide --</option>
                                <?php
                                $guides->data_seek(0);
                                while ($g = $guides->fetch_assoc()):
                                ?>
                                <option value="<?php echo $g['guide_id']; ?>">
                                    <?php echo htmlspecialchars($g['name']); ?>
                                    (<?php echo htmlspecialchars($g['language']); ?>)
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

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="date" class="form-label fw-semibold">Travel Date</label>
                            <input type="date" name="date" id="date" class="form-control" required
                                min="<?php echo $today; ?>">
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label for="time" class="form-label fw-semibold">Meeting Time</label>
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