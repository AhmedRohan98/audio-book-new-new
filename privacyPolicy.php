<?php
    include("includes/connection.php");
    $privacy_policy_content = isset($settings_details['app_privacy_policy']) ? $settings_details['app_privacy_policy'] : "";
    if (!empty($privacy_policy_content)) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Privacy Policy</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-3">
    <?php echo html_entity_decode($privacy_policy_content); ?>
</div>

</body>
</html>

<?php
    } else {
        echo "Error: Privacy policy content not found.";
    }
?>
