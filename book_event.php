<?php


// Fetch all events that are active (availability > 0)
$events = $conn->query("
    SELECT event_id, name, description, location, start_date, end_date, photo, ticket_price, rating, Seats_available
    FROM event
    WHERE availability > 0
    ORDER BY start_date ASC
");

$today = date('Y-m-d', strtotime('+1 day'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Event - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="container py-5">
        <h3 class="text-success fw-bold mb-4 text-center">Available Events</h3>

        <div class="row mb-5 justify-content-center">
            <?php if ($events->num_rows > 0): ?>
            <?php while ($e = $events->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-success shadow-sm">
                    <?php if (!empty($e['photo'])): ?>
                    <img src="uploads/<?php echo $e['photo']; ?>" class="card-img-top" alt="Event Photo"
                        style="height:250px; object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-success"><?php echo $e['name']; ?></h5>
                        <p><strong>Location:</strong> <?php echo $e['location']; ?></p>
                        <p><strong>Dates:</strong> <?php echo $e['start_date']; ?> → <?php echo $e['end_date']; ?></p>
                        <p><strong>Ticket Price:</strong> <?php echo $e['ticket_price']; ?> BDT</p>
                        <p><strong>Seats Available:</strong> <?php echo $e['Seats_available']; ?></p>
                        <p><strong>Rating:</strong> ⭐ <?php echo $e['rating']; ?>/5</p>
                        <p class="text-muted mb-0"><strong>Description:</strong> <?php echo $e['description']; ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="text-center text-danger">No events available right now.</p>
            <?php endif; ?>
        </div>

        <h3 class="text-success fw-bold mb-4 text-center">Book an Event</h3>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow p-4">
                    <form action="book_event_manager.php" method="POST">
                        <!-- Event Selection -->
                        <div class="mb-3">
                            <label for="event_id" class="form-label fw-semibold">Select Event</label>
                            <select name="event_id" id="event_id" class="form-select" required>
                                <option value="">-- Choose Event --</option>
                                <?php
                                $events->data_seek(0); // reset pointer
                                while ($e = $events->fetch_assoc()):
                                    if ($e['Seats_available'] > 0): // only show events with seats left
                                ?>
                                <option value="<?php echo $e['event_id']; ?>">
                                    <?php echo $e['name']; ?> (<?php echo $e['Seats_available']; ?> seats available)
                                </option>
                                <?php
                                    endif;
                                endwhile;
                                ?>
                            </select>
                        </div>

                        <!-- Number of Seats -->
                        <div class="mb-3">
                            <label for="number_of_seats" class="form-label fw-semibold">Number of Seats</label>
                            <input type="number" name="number_of_seats" id="number_of_seats" class="form-control"
                                placeholder="Enter number of seats" min="1" required>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="date" class="form-label fw-semibold">Booking Date</label>
                            <input type="date" name="date" id="date" class="form-control" required
                                min="<?php echo $today; ?>">
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label for="time" class="form-label fw-semibold">Booking Time</label>
                            <input type="time" name="time" id="time" class="form-control" required>
                        </div>

                        <!-- Contact -->
                        <div class="mb-3">
                            <label for="contact" class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact" id="contact" class="form-control"
                                placeholder="Enter phone number" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success rounded-pill py-2 fw-semibold">Confirm
                                Booking</button>
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