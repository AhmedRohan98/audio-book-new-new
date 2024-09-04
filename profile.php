<?php
$page_title = 'Admin Profile';
include("includes/header.php");

if (isset($_SESSION['id'])) {
    $qry = "select * from tbl_admin where id='" . $_SESSION['id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
}
if (isset($_POST['submit']) && PURCHASE == '') {

    if (
        empty(validate_input($_POST['username'])) ||
        (empty(validate_input($_POST['email'])) || !filter_var(validate_input($_POST['email']), FILTER_VALIDATE_EMAIL))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:profile.php");
        exit;
    }

    $has_admin_password = '';
    if (isset($_POST['password']) && trim($_POST['password']) != "") {
        $admin_password = $_POST['password'];
        $has_admin_password = password_hash($admin_password, PASSWORD_ARGON2I);
    }
    if ($_FILES['image']['name'] != "") {

        $img_res = mysqli_query($mysqli, 'SELECT * FROM tbl_admin WHERE id=' . $_SESSION['id'] . '');
        $img_res_row = mysqli_fetch_assoc($img_res);


        if ($img_res_row['image'] != "") {

            unlink('images/' . $img_res_row['image']);
        }

        //$image="profile.png";
        $image = rand(0, 99999) . "_" . $_FILES['image']['name'];
        $pic1 = $_FILES['image']['tmp_name'];
        $tpath1 = 'images/' . $image;

        copy($pic1, $tpath1);

        $data = array(
            'username'  =>  validate_input($_POST['username']),
            'email'  =>  validate_input($_POST['email']),
            'image'  =>  validate_input($image),
        );
        if ($has_admin_password != '') {
            $data = array_merge($data, array("password" => $has_admin_password));
        }

        $channel_edit = Update('tbl_admin', $data, "WHERE id = '" . $_SESSION['id'] . "'");
    } else {
        $data = array(
            'username'  =>  validate_input($_POST['username']),
            'email'  =>  validate_input($_POST['email']),
        );
        if ($has_admin_password != '') {
            $data = array_merge($data, array("password" => $has_admin_password));
        }

        $channel_edit = Update('tbl_admin', $data, "WHERE id = '" . $_SESSION['id'] . "'");
    }

    $_SESSION['msg'] = "11";
    header("Location:profile.php");
    exit;
}
?>
<main id="main" class="main">
    <section class="section">
        <div class="row  g-3">
            <div class="col-lg-12">
                <div class="card card_height">
                    <div class="card-body g-3"><br>
                        <div class="pagetitle mb-5">
                            <h1><?php _e($page_title); ?></h1>

                        </div><!-- End Page Title -->
                        <!-- Floating Labels Form -->
                        <form class="row g-3" action="" name="editprofile" method="post" class="form form-horizontal" enctype="multipart/form-data">
                            <br><br>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" name="image" class="form-control"><br>
                                <?php if ($row['image'] != "") { ?>
                                    <div class="fileupload_img" id="uploadPreview">
                                        <img type="image" src="images/<?php _e($row['image']); ?>" alt="image" style="width: 200px;height: 300px;margin-top: 10px;" />
                                    </div>
                                <?php } else { ?>
                                    <div class="fileupload_img" id="uploadPreview">
                                        <img type="image" src="assets/img/add-image.png" alt="image" style="width: 200px;height: 300px;margin-top: 10px;" />
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="floatingName" placeholder="Fill user name"  value="<?php _e($row['username']); ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Password</label>
                                <input type="text" class="form-control" name="password" id="floatingName"  class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" id="floatingName" placeholder="Fill email"  value="<?php _e($row['email']); ?>" class="form-control">
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
                    document.getElementById('uploadPreview').innerHTML = '<img src="' + e.target.result + '" style="width:200px;height:300px;margin-top: 10px;"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>
<?php include("includes/footer.php"); ?>