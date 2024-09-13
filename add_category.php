<?php
$page_title = (isset($_GET['cat_id'])) ? 'Edit Category' : 'Add Category';
include("includes/header.php");
require_once("thumbnail_images.class.php");

if (isset($_POST['submit']) and isset($_GET['add'])  && PURCHASE == '') {
    if (
        empty(validate_input($_POST['category_name'])) ||
        (!isset($_FILES['category_image']['size']) || $_FILES['category_image']['size'] == 0)
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_category.php?add=yes");
        exit;
    }

    $file_name = str_replace(" ", "-", $_FILES['category_image']['name']);

    $category_image = rand(0, 99999) . "_" . $file_name;

    $tpath1 = 'images/' . $category_image;
    $pic1 = compress_image($_FILES["category_image"]["tmp_name"], $tpath1, 80);

    $thumbpath = 'images/thumbs/' . $category_image;
    $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '200', '300');

    $data = array(
        'category_name'  =>  validate_input($_POST['category_name']),
        'category_image'  =>  validate_input($category_image)
    );

    $qry = Insert('tbl_category', $data);

    $_SESSION['msg'] = "10";

    header("Location:manage_category.php");
    exit;
}
$count = 0;
if (isset($_GET['cat_id'])) {
    $qry = "SELECT * FROM tbl_category where cid='" . $_GET['cat_id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
    $count = $result->num_rows;
}

if (isset($_POST['submit']) &&   $count > 0  && PURCHASE == '') {
    if (
        empty(validate_input($_POST['category_name'])) ||
        ((isset($_FILES['category_image']) && empty($_FILES['category_image']['name'])) && empty($row['category_image']))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_category.php?cat_id=" . $_GET['cat_id']);
        exit;
    }
    if ($_FILES['category_image']['name'] != "") {
        $file_name = str_replace(" ", "-", $_FILES['category_image']['name']);

        $category_image = rand(0, 99999) . "_" . $file_name;

        $tpath1 = 'images/' . $category_image;
        $pic1 = compress_image($_FILES["category_image"]["tmp_name"], $tpath1, 80);

        $thumbpath = 'images/thumbs/' . $category_image;
        $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '200', '300');
    } else {
        $category_image = $row['category_image'];
    }

    $data = array(
        'category_name'  =>  validate_input($_POST['category_name']),
        'category_image'  =>  validate_input($category_image)
    );

    $category_edit = Update('tbl_category', $data, "WHERE cid = '" . $_GET['cat_id'] . "'");


    $_SESSION['msg'] = "11";
    header("Location:manage_category.php");
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
                        <form class="row g-3" action="" name="addeditcategory" method="post"  enctype="multipart/form-data">
                            <br><br>
                            <input type="hidden" name="cat_id" value="<?php if ($count > 0) {  _e($_GET['cat_id']); } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Category Name</label>
                                <input type="text" class="form-control" name="category_name" id="floatingName"  value="<?php if ($count > 0) {
                                                                                                                                    _e($row['category_name']);
                                                                                                                                }  ?>" class="form-control">
                            </div>
                            <div class="col-12">
                            <label for="inputNanme4" class="form-label">Category Image</label>
                                    <input type="file" name="category_image" class="form-control"  value="fileupload" accept=".png,.PNG,.jpg, .JPG ,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload">
                                    <?php if ($count > 0) { ?>
                                                <div class="fileupload_img" id="uploadPreview"><img type="image" src="images/<?php _e($row['category_image']); ?>" alt="image" style="width: 150px;height:200px;margin-top: 10px;" /></div>
                                            <?php } else { ?>
                                                <div class="fileupload_img" id="uploadPreview"><img type="image" src="assets/img/add-image.png" alt="image" style="width: 150px;height:200px;margin-top: 10px;" /></div>
                                            <?php } ?>
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