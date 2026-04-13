<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP App - K8S Test</title>
</head>
<body>
  <h1>Test App</h1>
  <p>Version: <?php echo htmlspecialchars(getenv('APP_VERSION') ?: '0.2.2', ENT_QUOTES, 'UTF-8'); ?></p>
</body>
</html>
