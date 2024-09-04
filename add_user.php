<?php
$page_title = (isset($_GET['author_id'])) ? 'Edit User' : 'Add User';
include("includes/header.php");
require_once("thumbnail_images.class.php");


if (isset($_POST['submit']) and isset($_GET['add'])  && PURCHASE == '') {

    if (
        empty(validate_input($_POST['name'])) ||
        (!isset($_FILES['user_image']['size']) || $_FILES['user_image']['size'] == 0) ||
        (empty(validate_input($_POST['email'])) || !filter_var(validate_input($_POST['email']), FILTER_VALIDATE_EMAIL)) ||
        empty(validate_input($_POST['password'])) ||
        empty(validate_input($_POST['phone']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_user.php?add=yes");
        exit;
    }

    $email = $_POST['email'];
    $query = "SELECT COUNT(*) as num  FROM tbl_users WHERE `email` LIKE '$email'";
    $total_users = mysqli_fetch_array(mysqli_query($mysqli, $query));
    $total_users = $total_users['num'];

    if($total_users > 0)
    {
        $_SESSION['msg'] = "18";
        header("location:add_user.php");
        exit;
    }

    $file_name = str_replace(" ", "-", $_FILES['user_image']['name']);

    $user_image = rand(0, 99999) . "_" . $file_name;

    //Main Image
    $tpath1 = 'images/' . $user_image;
    $pic1 = compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);

    //Thumb Image 
    $thumbpath = 'images/thumbs/' . $user_image;
    $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '250', '150');


    $data = array(
        'user_type' => 'Normal',
        'name'  =>  validate_input($_POST['name']),
        'user_image' => validate_input($user_image),
        'email'  =>  validate_input($_POST['email']),
        'phone'  =>  validate_input($_POST['phone']),
        'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
    );
    if (isset($_POST['password']) && trim($_POST['password']) != "") {
        $user_password = trim($_POST['password']);
        $has_user_password = password_hash($user_password, PASSWORD_ARGON2I);
        $data = array_merge($data, array("password" => $has_user_password));
    }

    $qry = Insert('tbl_users', $data);


    $_SESSION['msg'] = "10";
    header("location:manage_users.php");
    exit;
}
$count = 0;
if (isset($_GET['user_id'])) {

    $user_qry = "SELECT * FROM tbl_users where id='" . $_GET['user_id'] . "'";
    $user_result = mysqli_query($mysqli, $user_qry);
    $user_row = mysqli_fetch_assoc($user_result);
    $count = $user_result->num_rows;
}

if (isset($_POST['submit']) and isset($_POST['user_id']) && PURCHASE == '') {
    if (
        empty(validate_input($_POST['name'])) ||
        (empty(validate_input($_POST['email'])) || !filter_var(validate_input($_POST['email']), FILTER_VALIDATE_EMAIL)) ||
        empty(validate_input($_POST['phone']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_user.php?user_id=" . $_GET['user_id']);
        exit;
    }

    if ($_FILES['user_image']['name'] != "") {
        if ($row['user_image'] != "") {
            unlink('images/thumbs/' . $row['user_image']);
            unlink('images/' . $row['user_image']);
        }

        $file_name = str_replace(" ", "-", $_FILES['user_image']['name']);

        $user_image = rand(0, 99999) . "_" . $file_name;

        //Main Image
        $tpath1 = 'images/' . $user_image;
        $pic1 = compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);

        //Thumb Image 
        $thumbpath = 'images/thumbs/' . $user_image;
        $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '250', '150');

        $data = array(
            'name'  =>  $_POST['name'],
            'user_image' => $user_image,
            'email'  =>  $_POST['email'],
            'phone'  =>  $_POST['phone']
        );
    } else {
        $data = array(
            'name'  =>  $_POST['name'],
            'user_image' => $user_row['user_image'],
            'email'  =>  $_POST['email'],
            'phone'  =>  $_POST['phone']
        );
    }
    if (isset($_POST['password']) && trim($_POST['password']) != "") {
        $user_password = trim($_POST['password']);
        $has_user_password = password_hash($user_password, PASSWORD_ARGON2I);
        $data = array_merge($data, array("password" => $has_user_password));
    }
    $user_edit = Update('tbl_users', $data, "WHERE id = '" . $_POST['user_id'] . "'");

    if ($user_edit > 0) {

        $_SESSION['msg'] = "11";
        header("location:manage_users.php");
        exit;
    }
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
                        <form class="row g-3" action="" name="addeditcategory" method="post" enctype="multipart/form-data">
                            <br><br>
                            <input type="hidden" name="user_id" value="<?php if ($count > 0) {
                                                                            _e($_GET['user_id']);
                                                                        } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="floatingName"  value="<?php if ($count > 0) {
                                                                                                                                                    _e($user_row['name']);
                                                                                                                                                } ?>">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Select User Image</label>
                                <input type="file" name="user_image" class="form-control" value="fileupload" accept=".png,.PNG,.jpg, .JPG ,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload">
                                <?php
                                if ($count > 0) {
                                    if ($user_row['user_type'] == 'Google' || $user_row['user_type'] == 'Facebook') {
                                        if (empty($user_row['user_image'])) {
                                ?>
                                            <div class="fileupload_img" id="uploadPreview">
                                                <img src="images/add-image.png" style="width:100px;height:100px;margin-top: 10px;" />
                                            </div>
                                        <?php
                                        } else {
                                        ?>
                                            <div class="fileupload_img" id="uploadPreview">
                                                <img src="<?php _e($user_row['user_image']); ?>" style="width:100px;border-radius: 60%;margin-top: 10px;" />
                                            </div>
                                        <?php
                                        }
                                    } else {
                                        if (empty($user_row['user_image'])) {
                                        ?>
                                            <div class="fileupload_img" id="uploadPreview">
                                                <img src="images/add-image.png" style="width:100px;border-radius: 60%;margin-top: 10px;" />
                                            </div>
                                        <?php
                                        } else {
                                        ?>
                                            <div class="fileupload_img" id="uploadPreview">
                                                <img src="images/<?php _e($user_row['user_image']); ?>" style="width:100px;border-radius: 60%;margin-top: 10px;" />
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>

                                <?php } else { ?>
                                    <div class="fileupload_img" id="uploadPreview">
                                        <img type="image" src="assets/img/add-image.png" alt="image" style="width:100px;border-radius: 60%;margin-top: 10px;" />
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" id="floatingName"  value="<?php if ($count > 0) {
                                                                                                                                                    _e($user_row['email']);
                                                                                                                                                } ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Password</label>
                                <input type="text" class="form-control" name="password" id="floatingName"  class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="floatingName"  value="<?php if ($count > 0) {
                                                                                                                                                    _e($user_row['phone']);
                                                                                                                                                } ?>" class="form-control">
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
                    document.getElementById('uploadPreview').innerHTML = '<img src="' + e.target.result + '" style="width:100px;border-radius: 60%;margin-top: 10px;"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>
<?php include("includes/footer.php"); ?>