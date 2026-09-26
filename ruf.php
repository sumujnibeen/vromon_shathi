<?php
include('db.php');
$conn = connect();
include 'auth.php';

// Validate input
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("<p class='text-center text-danger mt-5'>Invalid request.</p>");
}

$type = $_GET['type'];
$id = intval($_GET['id']);

//Validate type
$validTypes = ['destination', 'hotel', 'guide'];
if (!in_array($type, $validTypes)) {
    die("<p class='text-center text-danger mt-5'>Invalid type.</p>");
}

// Fetch item info
$stmt = $conn->prepare("SELECT * FROM $type WHERE {$type}_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("<p class='text-center text-danger mt-5'>Item not found.</p>");
}

$item = $result->fetch_assoc();
$stmt->close();

//  Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        echo "<p class='text-center text-danger'>Please select a rating.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO review (Type, Item_id, Rating, Comment, Created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("siis", $type, $id, $rating, $comment);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Thank you for your feedback!'); window.location.href='{$type}_details.php?id=$id';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate <?php echo ucfirst($type); ?> - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <section class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Rate <?php echo ucfirst($type); ?></h2>
            <p class="text-muted">Share your experience with us!</p>
        </div>

        <div class="card shadow mx-auto" style="max-width: 600px;">
            <div class="card-body p-4 text-center">
                <img src="<?php echo $item['Photo']; ?>" alt="<?php echo $item['Name']; ?>"
                    class="img-fluid rounded mb-3" style="max-height: 250px; object-fit: cover;">

                <h4 class="fw-bold mb-3"><?php echo $item['Name']; ?></h4>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Your Rating:</label><br>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" class="btn-check" name="rating" id="star<?php echo $i; ?>"
                                value="<?php echo $i; ?>">
                            <label class="btn btn-outline-warning fs-3" for="star<?php echo $i; ?>">&#9733;</label>
                        <?php endfor; ?>
                    </div>

                    <div class="mb-3">
                        <textarea name="comment" class="form-control rounded-3" rows="3"
                            placeholder="Leave a comment (optional)"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill">Submit Rating</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

this was review.php


and this is review table

Review_id
User_id
Target_type
Target_id
Rating
Comment
Created_at


update this accroding to this


/////////////////////

<?php
include('controller.php');
$db = connect();

// Hardcoded user_id for demo (replace with session in real app)
$user_id = 1;

// Fetch user reviews
$stmt = $db->prepare("SELECT * FROM reviews WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .review-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 p-0">
                <?php include('sidebar.php'); ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 p-4">
                <h3 class="mb-4">My Reviews</h3>

                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-card">
                            <h5 class="mb-1"><?php echo htmlspecialchars($rev['package_name']); ?></h5>
                            <small class="text-muted"><?php echo date("d M Y, H:i", strtotime($rev['created_at'])); ?></small>
                            <p class="mb-1">Rating: <?php echo htmlspecialchars($rev['rating']); ?>/5</p>
                            <p><?php echo htmlspecialchars($rev['comment']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">You have not posted any reviews yet.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

review.php