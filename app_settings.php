<?php
$page_title = "App Settings";
include("includes/header.php");

$qry = "SELECT * FROM tbl_settings where id='1'";
$result = mysqli_query($mysqli, $qry);
$settings_row = mysqli_fetch_assoc($result);
$count = $result->num_rows;

if (isset($_POST['submit']) && PURCHASE == '') {

    if (
        empty(validate_input($_POST['app_name'])) ||
        empty(validate_input($_POST['app_description'], true)) ||
        empty(validate_input($_POST['app_version'])) ||
        empty(validate_input($_POST['app_author'])) ||
        empty(validate_input($_POST['app_contact'])) ||
        (empty(validate_input($_POST['app_email'])) || !filter_var(validate_input($_POST['app_email']), FILTER_VALIDATE_EMAIL)) ||
        empty(validate_input($_POST['app_website']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:app_settings.php");
        exit;
    }

    $img_res = mysqli_query($mysqli, "SELECT * FROM tbl_settings WHERE id='1'");
    $img_row = mysqli_fetch_assoc($img_res);


    if ($_FILES['app_logo']['name'] != "") {

        unlink('assets/img/' . $img_row['app_logo']);

        $app_logo = $_FILES['app_logo']['name'];
        $pic1 = $_FILES['app_logo']['tmp_name'];

        $tpath1 = 'assets/img/' . $app_logo;
        copy($pic1, $tpath1);


        $data = array(

            'app_name'  =>  validate_input($_POST['app_name']),
            'app_logo'  =>  validate_input($app_logo),
            'app_description'  => validate_input($_POST['app_description'], true),
            'app_version'  =>  validate_input($_POST['app_version']),
            'app_author'  =>  validate_input($_POST['app_author']),
            'app_contact'  =>  validate_input($_POST['app_contact']),
            'app_email'  =>  validate_input($_POST['app_email']),
            'app_website'  => validate_input($_POST['app_website'])

        );
    } else {

        $data = array(

            'app_name'  =>  validate_input($_POST['app_name']),
            'app_logo'  =>  validate_input($settings_row['app_logo']),
            'app_description'  => validate_input($_POST['app_description']),
            'app_version'  =>  validate_input($_POST['app_version']),
            'app_author'  =>  validate_input($_POST['app_author']),
            'app_contact'  =>  validate_input($_POST['app_contact']),
            'app_email'  =>  validate_input($_POST['app_email']),
            'app_website'  =>  validate_input($_POST['app_website'])

        );
    }

    $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");

    if ($settings_edit > 0) {

        $_SESSION['msg'] = "11";
        header("Location:app_settings.php");
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
                        <form class="row g-3" action="" name="settings_from" method="post" class="form form-horizontal" enctype="multipart/form-data">
                            <br><br>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">App Name</label>
                                <input type="text" class="form-control" name="app_name" id="floatingName" value="<?php _e($settings_row['app_name']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">App Logo</label>
                                <input type="file" class="form-control" name="app_logo" value="fileupload" accept=".png,.PNG,.jpg, .JPG ,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload">
                                <?php if ($count > 0 && $settings_row['app_logo'] != "") { ?>
                                    <div class="fileupload_img" id="uploadPreview">
                                        <img type="image" src="assets/img/<?php _e($settings_row['app_logo']); ?>" alt="image" style="width: 100px;;height: 100px;margin-top: 10px;" />
                                    </div>
                                <?php } else { ?>
                                    <div class="fileupload_img" id="uploadPreview">
                                        <img type="image" src="assets/img/add-image.png" alt="image" style="width: 100px;height: 100px; margin-top: 10px;" />
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea name="app_description" id="author_description" class="form-control">
                                            <?php _e($settings_row['app_description'], true); ?>
                                             </textarea>
                                    <script>
                                        CKEDITOR.replace('app_description');
                                    </script>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">App Version</label>
                                <input type="text" class="form-control" name="app_version" id="floatingName" value="<?php _e($settings_row['app_version']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Author</label>
                                <input type="text" class="form-control" name="app_author" id="floatingName" value="<?php _e($settings_row['app_author']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Contact</label>
                                <input type="text" class="form-control" name="app_contact" id="floatingName" value="<?php _e($settings_row['app_contact']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Email</label>
                                <input type="text" class="form-control" name="app_email" id="floatingName" value="<?php _e($settings_row['app_email']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Website</label>
                                <input type="text" class="form-control" name="app_website" id="floatingName" value="<?php _e($settings_row['app_website']); ?>" class="form-control">
                            </div>
                            <div class="text-center">
                                <button type="submit" name="submit" class="btn button btn-primary <?php _e(PURCHASE); ?>">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->
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
                    document.getElementById('uploadPreview').innerHTML = '<img src="' + e.target.result + '" style="width:150px;height:200px;margin-top: 10px;"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>
<?php include("includes/footer.php"); ?>