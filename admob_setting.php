<?php
$page_title = "Admob Settings";
include("includes/header.php");

$qry = "SELECT * FROM tbl_settings where id='1'";
$result = mysqli_query($mysqli, $qry);
$settings_row = mysqli_fetch_assoc($result);

if (isset($_POST['admob_submit']) && PURCHASE == '') {
    if (
        empty(validate_input($_POST['publisher_id']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:admob_setting.php");
        exit;
    }
    $data = array(
        'publisher_id'  =>  validate_input($_POST['publisher_id']),
        'interstital_ad_click'  =>  validate_input($_POST['interstital_ad_click']),
        'banner_ad_id'  =>  $_POST['banner_ad_id'],
        'interstital_ad_id'  =>  $_POST['interstital_ad_id'],
        'app_open_ad_id'  =>  $_POST['app_open_ad_id'],
        'ios_banner_ad_id'  =>  $_POST['ios_banner_ad_id'],
        'ios_interstital_ad_id' => $_POST['ios_interstital_ad_id'],
        'ios_app_open_ad_id' => $_POST['ios_app_open_ad_id']
    );

    $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");


    if ($settings_edit > 0) {

        $_SESSION['msg'] = "11";
        header("Location:admob_setting.php");
        exit;
    }
}
?>
<main id="main" class="main">
    <div class="row">
        <div class="col-lg-3">
            <div class="pagetitle">
                <h1><?php _e($page_title); ?></h1>
            </div>
        </div>
    </div><br>
    <section class="section">
        <div class="card"><br />
            <div class="row">
                <form class="row" action="" name="addeditcategory" method="post" enctype="multipart/form-data">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Publisher ID</label>
                        <input type="text" class="form-control mb-2" name="publisher_id" value="<?php _e($settings_row['publisher_id']); ?>">
                    </div><br>
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Interstital Click</label>
                        <input type="number" class="form-control mb-2" name="interstital_ad_click" value="<?php _e($settings_row['interstital_ad_click']); ?>">
                    </div>
                    <div class="col-lg-6">


                        <div class="card-body">
                            <h5 class="card-title">Android</h5>

                            <!-- Default Accordion -->
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Banner Ads
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">Banner AD ID</label>
                                                <input type="text" class="form-control mb-2" name="banner_ad_id" value="<?php _e($settings_row['banner_ad_id']); ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">Status</label>
                                                <?php if ($settings_row['banner_ad_id_status'] == "1") { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="active" data-column="banner_ad_id_status">
                                                        <img src="assets/img/btn_enabled.png" style="width:50px" />
                                                    </a>
                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="deactive" data-column="banner_ad_id_status">
                                                        <img src="assets/img/btn_disabled.png" style="width:50px" />
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Interstital Ads
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">Interstital AD ID</label>
                                                <input type="text" class="form-control mb-2" name="interstital_ad_id" value="<?php _e($settings_row['interstital_ad_id']); ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">Status</label>
                                                <?php if ($settings_row['interstital_ad_id_status'] == "1") { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="active" data-column="interstital_ad_id_status">
                                                        <img src="assets/img/btn_enabled.png" style="width:50px" />
                                                    </a>
                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="deactive" data-column="interstital_ad_id_status">
                                                        <img src="assets/img/btn_disabled.png" style="width:50px" />
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            App Open Ad ID
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">App Open Ad ID</label>
                                                <input type="text" class="form-control mb-2" name="app_open_ad_id" value="<?php _e($settings_row['app_open_ad_id']); ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">status</label>
                                                <?php if ($settings_row['app_open_ad_id_status'] == "1") { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="active" data-column="app_open_ad_id_status">
                                                        <img src="assets/img/btn_enabled.png" style="width:50px" />
                                                    </a>
                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="deactive" data-column="app_open_ad_id_status">
                                                        <img src="assets/img/btn_disabled.png" style="width:50px" />
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Default Accordion Example -->
                        </div>

                    </div>
                    <div class="col-lg-6">

                        <div class="card-body">
                            <h5 class="card-title">IOS</h5>

                            <!-- Default Accordion -->
                            <div class="accordion" id="accordionExample2">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne2">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne2" aria-expanded="true" aria-controls="collapseOne2">
                                            IOS Banner Ads
                                        </button>
                                    </h2>
                                    <div id="collapseOne2" class="accordion-collapse collapse show" aria-labelledby="headingOne2" data-bs-parent="#accordionExample2">
                                        <div class="accordion-body">
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">Banner ID</label>
                                                <input type="text" class="form-control mb-2" name="ios_banner_ad_id" value="<?php _e($settings_row['ios_banner_ad_id']); ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">status</label>
                                                <?php if ($settings_row['ios_banner_ad_id_status'] == "1") { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="active" data-column="ios_banner_ad_id_status">
                                                        <img src="assets/img/btn_enabled.png" style="width:50px" />
                                                    </a>
                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="deactive" data-column="ios_banner_ad_id_status">
                                                        <img src="assets/img/btn_disabled.png" style="width:50px" />
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2">
                                            IOS Interstital Ads
                                        </button>
                                    </h2>
                                    <div id="collapseTwo2" class="accordion-collapse collapse" aria-labelledby="headingTwo2" data-bs-parent="#accordionExample2">
                                        <div class="accordion-body">
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">Interstital ID</label>
                                                <input type="text" class="form-control mb-2" name="ios_interstital_ad_id" value="<?php _e($settings_row['ios_interstital_ad_id']); ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">status</label>
                                                <?php if ($settings_row['ios_interstital_ad_id_status'] == "1") { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="active" data-column="ios_interstital_ad_id_status">
                                                        <img src="assets/img/btn_enabled.png" style="width:50px" />
                                                    </a>
                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="deactive" data-column="ios_interstital_ad_id_status">
                                                        <img src="assets/img/btn_disabled.png" style="width:50px" />
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree2" aria-expanded="false" aria-controls="collapseThree2">
                                            IOS App Open Ad ID
                                        </button>
                                    </h2>
                                    <div id="collapseThree2" class="accordion-collapse collapse" aria-labelledby="headingThree2" data-bs-parent="#accordionExample2">
                                        <div class="accordion-body">
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">IOS App Open Ad ID</label>
                                                <input type="text" class="form-control mb-2" name="ios_app_open_ad_id" value="<?php _e($settings_row['ios_app_open_ad_id']); ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputNanme4" class="form-label">status</label>
                                                <?php if ($settings_row['ios_app_open_ad_id_status'] == "1") { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="active" data-column="ios_app_open_ad_id_status">
                                                        <img src="assets/img/btn_enabled.png" style="width:50px" />
                                                    </a>
                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a form-control" href="javascript:void(0)" data-id="<?php _e($settings_row['id']); ?>" data-action="deactive" data-column="ios_app_open_ad_id_status">
                                                        <img src="assets/img/btn_disabled.png" style="width:50px" />
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="text-center">
                        <button type="submit" name="admob_submit" class="btn btn-primary <?php _e(PURCHASE); ?>">Save</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div><br>
        </div>
    </section>
    <script type="text/javascript">
        $(".toggle_btn_a").on("click", function(e) {
            e.preventDefault();

            var _for = $(this).data("action");
            var _id = $(this).data("id");
            var _column = $(this).data("column");
            var _table = 'tbl_settings';
            $.ajax({
                type: 'post',
                url: 'processdata.php',
                dataType: 'json',
                data: {
                    id: _id,
                    for_action: _for,
                    column: _column,
                    table: _table,
                    'action': 'admob_add',
                    'tbl_id': 'id'
                },
                success: function(res) {
                    console.log(res);
                    if (res.status == '1') {
                        location.reload();
                    }
                }
            });

        });
    </script>
</main>
<?php include("includes/footer.php"); ?>