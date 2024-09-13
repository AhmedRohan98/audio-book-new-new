<?php
$page_title = "SMTP Settings";
include("includes/header.php");


if (isset($_POST['submit']) && PURCHASE == '') {
    // Validate required fields
    if (
        empty(validate_input($_POST['smtp_type'])) ||
        empty(validate_input($_POST['smtp_host'])) ||
        empty(validate_input($_POST['smtp_email'])) ||
        empty(validate_input($_POST['smtp_secure'])) ||
        empty(validate_input($_POST['port_no']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location: smtp_settings.php");
        exit;
    }

    // Determine which password to use based on SMTP type
    if ($_POST['smtp_type'] == "gmail") {
        $password = !empty($_POST['smtp_password']) ? $_POST['smtp_password'] : $row['smtp_password'];
    } else {
        $password = !empty($_POST['smtp_password']) ? $_POST['smtp_password'] : $row['smtp_gpassword'];
    }

    // Prepare data array based on SMTP type
    if ($_POST['smtp_type'] == "gmail") {
        $data = array(
            'smtp_type' => validate_input($_POST['smtp_type']),
            'smtp_host' => validate_input($_POST['smtp_host']),
            'smtp_email' => validate_input($_POST['smtp_email']),
            'smtp_password' => validate_input($password),
            'smtp_secure' => validate_input($_POST['smtp_secure']),
            'port_no' => validate_input($_POST['port_no'])
        );
    } else {
        $data = array(
            'smtp_type' => validate_input($_POST['smtp_type']),
            'smtp_ghost' => validate_input($_POST['smtp_host']),
            'smtp_gemail' => validate_input($_POST['smtp_email']),
            'smtp_gpassword' => validate_input($password),
            'smtp_gsecure' => validate_input($_POST['smtp_secure']),
            'gport_no' => validate_input($_POST['port_no'])
        );
    }

    // Check if settings already exist and perform update or insert
    $sql = "SELECT * FROM tbl_smtp_settings WHERE id='1'";
    $res = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

    if (mysqli_num_rows($res) > 0) {
        $update = Update('tbl_smtp_settings', $data, "WHERE id = '1'");
    } else {
        $insert = Insert('tbl_smtp_settings', $data);
    }

    $_SESSION['class'] = "success";
    $_SESSION['msg'] = "11";
    header("Location: smtp_settings.php");
    exit;
}

// Fetch existing SMTP settings
$qry = "SELECT * FROM tbl_smtp_settings WHERE id='1'";
$result = mysqli_query($mysqli, $qry);
$row = mysqli_fetch_assoc($result);
$count = mysqli_num_rows($result);

// Include footer file
include("includes/footer.php");
?>

<!-- HTML code begins -->
<main id="main" class="main">
    <div class="row">
        <div class="col-lg-3">
            <div class="pagetitle">
                <h1><?php echo htmlentities($page_title); ?></h1>
            </div>
        </div>
    </div><br>
    <section class="section">
        <div class="row">
            <div class="col-lg-12" style="margin-bottom: 20px">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php if ($row['smtp_type'] == "gmail") { echo 'active'; } ?>" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home">Gmail SMTP</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php if ($row['smtp_type'] == "server") { echo 'active'; } ?>" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile">Server SMTP</button>
                            </li>
                        </ul>
                        <div class="tab-content pt-2" id="myTabContent">
                            <div class="tab-pane fade <?php if ($row['smtp_type'] == "gmail") { echo 'show active'; } ?>" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <br>
                                <form class="row g-3" action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="smtp_type" value="gmail">
                                    <div class="col-12">
                                        <label for="inputName4" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" name="smtp_host" value="<?php echo $count > 0 ? htmlentities($row['smtp_host']) : ''; ?>" placeholder="mail.example.in">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputEmail4" class="form-label">Email</label>
                                        <input type="text" class="form-control" name="smtp_email" value="<?php echo $count > 0 ? htmlentities($row['smtp_email']) : ''; ?>" placeholder="info@example.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputPassword4" class="form-label">Password</label>
                                        <input type="password" class="form-control" name="smtp_password" placeholder="********">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputSMTPSecure4" class="form-label">SMTPSecure</label>
                                        <select name="smtp_secure" class="form-control">
                                            <option value="tls" <?php echo $row['smtp_secure'] == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo $row['smtp_secure'] == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputPortNo4" class="form-label">Port No.</label>
                                        <input type="text" class="form-control" name="port_no" value="<?php echo $count > 0 ? htmlentities($row['port_no']) : ''; ?>" placeholder="Enter Port No.">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="submit" class="btn btn-primary <?php echo PURCHASE; ?>">Save</button>
                                        <button type="reset" class="btn btn-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade <?php if ($row['smtp_type'] == "server") { echo 'show active'; } ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <br>
                                <form class="row g-3" action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="smtp_type" value="server">
                                    <div class="col-12">
                                        <label for="inputName4" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" name="smtp_host" value="<?php echo $count > 0 ? htmlentities($row['smtp_ghost']) : ''; ?>" placeholder="mail.example.in">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputEmail4" class="form-label">Email</label>
                                        <input type="text" class="form-control" name="smtp_email" value="<?php echo $count > 0 ? htmlentities($row['smtp_gemail']) : ''; ?>" placeholder="info@example.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputPassword4" class="form-label">Password</label>
                                        <input type="password" class="form-control" name="smtp_password" placeholder="********">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputSMTPSecure4" class="form-label">SMTPSecure</label>
                                        <select name="smtp_secure" class="form-control">
                                            <option value="tls" <?php echo $row['smtp_gsecure'] == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo $row['smtp_gsecure'] == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputPortNo4" class="form-label">Port No.</label>
                                        <input type="text" class="form-control" name="port_no" value="<?php echo $count > 0 ? htmlentities($row['gport_no']) : ''; ?>" placeholder="Enter Port No.">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="submit" class="btn btn-primary <?php echo PURCHASE; ?>">Save</button>
                                        <button type="reset" class="btn btn-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- Include footer file -->
<?php include("includes/footer.php"); ?>
