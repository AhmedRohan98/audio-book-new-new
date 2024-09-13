<?php
$page_title = "App Privacy Policy";
include("includes/header.php");
$qry = "SELECT * FROM tbl_settings where id='1'";
$result = mysqli_query($mysqli, $qry);
$settings_row = mysqli_fetch_assoc($result);
$count = $result->num_rows;

if (isset($_POST['app_pri_poly']) && PURCHASE == '') {
    if (
        empty($_POST['app_privacy_policy'])
     ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:app_privacy_policy.php");
        exit;
     }

    $data = array(
        'app_privacy_policy'  =>  $_POST['app_privacy_policy']
    );
    $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");
    $_SESSION['msg'] = "11";
    header("Location:app_privacy_policy.php");
    exit;
}
?>
<main id="main" class="main">
    <section class="section">
        <div class="row  g-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body g-3"><br>
                        <div class="pagetitle mb-5">
                            <h1><?php _e($page_title); ?></h1>

                        </div><!-- End Page Title -->
                        <!-- Floating Labels Form -->
                        <form class="row g-3" action="" name="api_privacy_policy" method="post" enctype="multipart/form-data">
                            <br><br>
                            <div class="col-md-12">
                                 <label for="inputNanme4" class="form-label">Privacy Policy</label>
                                <input type="text" class="form-control" disabled value="<?php echo getBaseUrl(). "privacyPolicy.php" ?>" id="floatingName" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea name="app_privacy_policy" id="app_privacy_policy"  class="form-control">
                                    <?php _e($settings_row['app_privacy_policy'],true); ?>
                                             </textarea>
                                    <script>
                                        CKEDITOR.replace('app_privacy_policy');
                                    </script>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="app_pri_poly" class="btn button btn-primary <?php _e(PURCHASE); ?>">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>
<?php include("includes/footer.php"); ?>