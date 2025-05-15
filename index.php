<?php
$conn = new mysqli("localhost", "root", "", "calculators");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$resultText = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $num1 = floatval($_POST["num1"]);
  $num2 = floatval($_POST["num2"]);
  $operation = $_POST["operation"];

  switch ($operation) {
    case "add":
      $res = $num1 + $num2;
      $symbol = "+";
      break;
    case "subtract":
      $res = $num1 - $num2;
      $symbol = "-";
      break;
    case "multiply":
      $res = $num1 * $num2;
      $symbol = "×";
      break;
    case "divide":
      if ($num2 == 0) {
        $resultText = "Cannot divide by zero.";
        $res = null;
        break;
      }
      $res = $num1 / $num2;
      $symbol = "÷";
      break;
    default:
      $res = null;
      $resultText = "Invalid operation.";
  }

  if ($res !== null) {
    $resultText = "$num1 $symbol $num2 = $res";
    $stmt = $conn->prepare("INSERT INTO result (results) VALUES (?)");
    $stmt->bind_param("s", $resultText);
    $stmt->execute();
    $stmt->close();
  }
}

$history = $conn->query("SELECT * FROM result ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bootstrap Calculator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
  <div class="container">
    <h2 class="text-center mb-4">Simple Calculator with History</h2>

    <form method="POST" class="card p-4 shadow-sm mb-4">
      <div class="mb-3">
        <input type="number" name="num1" class="form-control" placeholder="Enter first number" required>
      </div>
      <div class="mb-3">
        <input type="number" name="num2" class="form-control" placeholder="Enter second number" required>
      </div>
      <div class="mb-3">
        <select name="operation" class="form-select" required>
          <option value="add">Add (+)</option>
          <option value="subtract">Subtract (-)</option>
          <option value="multiply">Multiply (×)</option>
          <option value="divide">Divide (÷)</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-100">Calculate</button>
    </form>

    <?php if ($resultText): ?>
      <div class="alert alert-success text-center">
        <?= htmlspecialchars($resultText) ?>
      </div>
    <?php endif; ?>

    <div class="card p-3 shadow-sm">
      <h5 class="mb-3">Calculation History</h5>
      <ul class="list-group">
        <?php while($row = $history->fetch_assoc()): ?>
          <li class="list-group-item d-flex justify-content-between">
            <?= htmlspecialchars($row['results']) ?>
            <div class="btn-group" role="group">
         <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
             <form method="POST" action="delete.php" style="margin: 0;">
             <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button class="btn btn-sm btn-danger">Delete</button>
          </form>
</div>

          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</body>
</html>

<?php $conn->close(); ?>
