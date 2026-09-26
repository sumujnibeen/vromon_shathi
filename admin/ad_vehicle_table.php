<?php
include 'db.php';
$conn=connect();
?>
<?php
 $sql = "SELECT * FROM vehicle";
  $result = mysqli_query($conn, $sql);
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ad_vehicle_booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
<table class="table table-success table-dark table-striped">
  <thead>
    <tr>
      <th scope="col">vehicle_id</th>
      <th scope="col">vehicle_name</th>
      <th scope="col">vehicle_type</th>
      <th scope="col">vehicle_price</th>
      <th scope="col">vehicle_availability_number</th>
      <th scope="col">vehicle_description</th>
      <th scope="col">vehicle_location</th>
      <th scope="col">vehicle_route</th>
      <th scope="col">created_at</th>
    </tr>
  </thead>
  <tbody>
   <?php
 if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      echo "<tr>
              <td>".$row['vehicle_id']."</td>
              <td>".$row['vehicle_name']."</td>
              <td>".$row['vehicle_type']."</td>
              <td>".$row['vehicle_price']."</td>
              <td>".$row['vehicle_availability_number']."</td>
              <td>".$row['vehicle_description']."</td>
              <td>".$row['vehicle_location']."</td>
              <td>".$row['vehicle_route']."</td>
              <td>".$row['created_at']."</td>
            </tr>";
    }
  } else {
    echo "<tr><td colspan='10'>No data found</td></tr>";
  }
  ?>
    
  </tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
 