<?php
$page_title = "API Settings";
include("includes/header.php");

$qry = "SELECT * FROM tbl_settings where id='1'";
$result = mysqli_query($mysqli, $qry);
$settings_row = mysqli_fetch_assoc($result);
$count = $result->num_rows;

if (isset($_POST['api_submit']) && PURCHASE == '') {

    if (empty(validate_input($_POST['api_latest_limit'])) || $_POST['api_latest_limit'] <= 0) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:api_setting.php");
        exit;
    }
    $data = array(
        'api_latest_limit'  =>  validate_input($_POST['api_latest_limit']),
        'api_cat_order_by'  =>  validate_input($_POST['api_cat_order_by']),
        'api_cat_post_order_by'  =>  validate_input($_POST['api_cat_post_order_by'])
    );
    $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");


    if ($settings_edit > 0) {

        $_SESSION['msg'] = "11";
        header("Location:api_setting.php");
        exit;
    }
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
                        <form class="row g-3" action="" name="settings_api" method="post" enctype="multipart/form-data">
                            <br><br>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Latest Limit</label>
                                <input type="text" class="form-control" name="api_latest_limit" id="floatingName" value="<?php _e($settings_row['api_latest_limit']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Category List Order By</label>
                                <select name="api_cat_order_by" id="api_cat_order_by" class="form-control label ui selection fluid dropdown" style="width: 100%; height: 100%;">
                                    <option value="cid" <?php if ($settings_row['api_cat_order_by'] == 'cid') { ?>selected<?php } ?>>ID</option>
                                    <option value="category_name" <?php if ($settings_row['api_cat_order_by'] == 'category_name') { ?>selected<?php } ?>>Name</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Category Post Order</label>
                                <select name="api_cat_post_order_by" id="api_cat_post_order_by" class="form-control label ui selection fluid dropdown" style="width: 100%; height: 100%;">
                                    <option value="ASC" <?php if ($settings_row['api_cat_post_order_by'] == 'ASC') { ?>selected<?php } ?>>ASC</option>
                                    <option value="DESC" <?php if ($settings_row['api_cat_post_order_by'] == 'DESC') { ?>selected<?php } ?>>DESC</option>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="api_submit" class="btn button btn-primary <?php _e(PURCHASE); ?>">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->
<?php include("includes/footer.php"); ?>