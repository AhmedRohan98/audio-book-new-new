<?php
$page_title = "Send Notification";
include("includes/header.php");

$_GET = validate_input($_GET);
function get_cat_name($cat_id)
{
    global $mysqli;

    $cat_qry = "SELECT * FROM tbl_category WHERE cid='" . $cat_id . "'";
    $cat_result = mysqli_query($mysqli, $cat_qry);
    $cat_row = mysqli_fetch_assoc($cat_result);

    return $cat_row['category_name'];
}

$cat_qry = "SELECT * FROM tbl_category ORDER BY category_name";
$cat_result = mysqli_query($mysqli, $cat_qry);

if (isset($_POST['submit_notification']) && PURCHASE == '') {
    
    if (
        empty(validate_input($_POST['notification_title'])) ||
        empty(validate_input($_POST['notification_msg']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:send_notification.php?add");
        exit;
    }
    
    $notification_title = validate_input($_POST['notification_title']);
    $notification_msg = validate_input($_POST['notification_msg']);
    $external_link = trim($_POST['external_link']);

    $content = array("en" => $notification_msg);

    // Use the segment "Total Subscriptions"
    $segment = "Active Users";

    $fields = array(
        'app_id' => $settings_details['onesignal_app_id'],
        'included_segments' => array($segment),
        'headings' => array("en" => $notification_title),
        'contents' => $content,
    );

    if ($_FILES['big_picture']['name'] != "") {
        $big_picture = rand(0, 99999) . "_" . $_FILES['big_picture']['name'];
        $tpath2 = 'images/' . $big_picture;
        move_uploaded_file($_FILES["big_picture"]["tmp_name"], $tpath2);

        $file_path = getBaseUrl() . 'images/' . $big_picture;
        $fields['big_picture'] = $file_path;
        $fields['ios_attachments'] = array('id' => $file_path);
    }
    
    if (!empty($external_link)) {
        $fields['url'] = $external_link;
    }

    $fields = json_encode($fields);

    $onesignal_rest_key_f = 'Authorization: Basic ' . $settings_details['onesignal_rest_key'];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', $onesignal_rest_key_f));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    $_SESSION['class'] = "success";
    $_SESSION['msg'] = "19";
    header('Location: send_notification.php');
    exit;
} elseif (isset($_POST['notification_submit']) && PURCHASE == '') {
        if (
            empty(validate_input($_POST['onesignal_rest_key'])) ||
            empty(validate_input($_POST['onesignal_app_id']))
        ) {
            $_SESSION['msg'] = "15";
            $_SESSION['class'] = 'error';
            header("Location:send_notification.php?add");
            exit;
        }
            $data = array(
                'onesignal_rest_key' => $_POST['onesignal_rest_key'],
                'onesignal_app_id' => $_POST['onesignal_app_id'],
            );

    $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");

    $_SESSION['class'] = "success";
    $_SESSION['msg'] = "11";
    header("Location:send_notification.php");
    exit;
}
?>
<style>
    span.select2.select2-container.select2-container--default {
        display: none;
    }
</style>
<main id="main" class="main">
    <div class="row">
        <div class="col-lg-3">
            <div class="pagetitle">
                <h1><?php _e($page_title); ?></h1>
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
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Notification Settings</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Send Notification</button>
                            </li>
                        </ul>
                        <div class="tab-content pt-2" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <br>
                                <form class="row g-3" action="" name="settings_api" method="post" enctype="multipart/form-data" id="api_form">
                                    <br><br>
                                    <div class="col-12">
                                        <label for="inputNanme4" class="form-label">OneSignal App ID</label>
                                        <input type="text" class="form-control" name="onesignal_app_id" id="floatingName" value="<?php _e($settings_details['onesignal_app_id']); ?>" placeholder="Fill book title" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputNanme4" class="form-label">OneSignal Rest Key</label>
                                        <input type="text" class="form-control" name="onesignal_rest_key" id="floatingName" value="<?php _e($settings_details['onesignal_rest_key']); ?>" placeholder="Fill book title" class="form-control">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="notification_submit" class="btn btn-primary <?php _e(PURCHASE); ?>">Save</button>
                                        <button type="reset" class="btn btn-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <br>
                                <form class="row g-3" action="" name="" method="post" enctype="multipart/form-data">
                                    <br><br>
                                    <div class="col-12">
                                        <label for="inputNanme4" class="form-label">Title</label>
                                        <input type="text" class="form-control" name="notification_title" id="floatingName" placeholder="Fill book title" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputNanme4" class="form-label">Message</label>
                                        <input type="text" class="form-control" name="notification_msg" id="floatingName" placeholder="Fill book title" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputNanme4" class="form-label">Image <br />(Optional)</label>
                                        <input class="form-control" type="file" name="big_picture" value="fileupload" accept=".png,.PNG,.jpg, .JPG ,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload">
                                        <div class="fileupload_img" id="uploadPreview">
                                            <img type="image" src="assets/img/add-image.png" alt="image" style="width: 200px;height: 300px;margin-top: 10px;" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputNanme4" class="form-label">External Link <br />(Optional)</label>
                                        <input type="text" class="form-control" name="external_link" id="floatingName" placeholder="Fill book title" class="form-control">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="submit_notification" class="btn btn-primary <?php _e(PURCHASE); ?>">Save</button>
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
<script type="text/javascript">
    function fileValidation() {
        var fileInput = document.getElementById('fileupload');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.png|.PNG|.jpg|.JPG|.jpeg|.JPEG)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Please upload file having extension .png, .PNG, .JPG , .jpg,.jpeg,.JPEG only.');
            fileInput.value = '';
            return false;
        } else {
            //image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('uploadPreview').innerHTML = '<img src="' + e.target.result + '" style="width:200px;height:300px;margin-top: 10px;"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>
<?php include("includes/footer.php"); ?>