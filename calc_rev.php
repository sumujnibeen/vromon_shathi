<?php
$target_types = ['destination', 'hotel', 'guide'];

foreach ($target_types as $target_type) {

    // Calculate average rating for each Target_id
    $avg_query = "SELECT Target_id, AVG(Rating) as avg_rating
                  FROM review
                  WHERE Target_type = ?
                  GROUP BY Target_id";

    $stmt = $conn->prepare($avg_query);
    $stmt->bind_param("s", $target_type);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $target_id = $row['Target_id'];
        $avg_rating = $row['avg_rating'];

        // Update the corresponding table
        $update_query = "UPDATE $target_type SET Rating = ? WHERE " . ucfirst($target_type) . "_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("di", $avg_rating, $target_id);
        $update_stmt->execute();

        $update_stmt->close();
    }
    $stmt->close();
}
