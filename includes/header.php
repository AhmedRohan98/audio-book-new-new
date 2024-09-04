<?php
include("includes/connection.php");
include("includes/session_check.php");
require_once('language/language.php');
require("includes/function.php");
require_once('includes/lib/HTMLPurifier/HTMLPurifier.standalone.php');

$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
$currentFile = $parts[count($parts) - 1];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php _e(APP_NAME); ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/<?php echo APP_LOGO ?>" rel="icon">
   <link href="assets/img/<?php echo APP_LOGO ?>" rel="icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/sweetalert/sweetalert.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/vendor/select/css/semantic.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="assets/ckeditor/ckeditor.js"></script>
  <script src="assets/vendor/select/js/semantic.min.js"></script>
  <style>
    .loader-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
  </style>
</head>

<body>
  <div class="loader-container">
    <div class="spinner-grow text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-secondary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-success" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-warning" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-info" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-light" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-dark" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>`
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center">
        <img src="assets/img/<?php _e(APP_LOGO); ?>" alt="">
        <span class="d-none d-lg-block"><?php _e(APP_NAME); ?></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li>

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="images/<?php _e(PROFILE_IMG); ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php _e(USERNAME); ?></span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php _e(USERNAME); ?></h6>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="app_settings.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "home.php") ? '' : 'collapsed'; ?>" href="home.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link  <?php echo ($currentFile == "manage_category.php" || $currentFile == "add_category.php") ? '' : 'collapsed'; ?>" href="manage_category.php">
          <i class="bi-diagram-3-fill"></i>
          <span>Category </span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_author.php" || $currentFile == "add_author.php") ? '' : 'collapsed'; ?>" href="manage_author.php">
          <i class="bi-person-square"></i>
          <span>Author </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_books.php" || $currentFile == "add_book.php" || $currentFile == "edit_book.php") ? '' : 'collapsed'; ?>" href="manage_books.php">
          <i class="bi bi-book-half"></i>
          <span>Books </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_mp3.php" || $currentFile == "add_mp3.php") ? '' : 'collapsed'; ?>" href="manage_mp3.php">
          <i class="bi bi-cassette-fill"></i>
          <span>Chapter</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_playlist.php" || $currentFile == "add_playlist.php") ? '' : 'collapsed'; ?>" href="manage_playlist.php">
          <i class="bi bi-list-ul"></i>
          <span>Playlist</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_banners.php" || $currentFile == "add_banner.php") ? '' : 'collapsed'; ?>" href="manage_banners.php">
          <i class="bi bi-sliders"></i>
          <span>Home Banner</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "home_section.php" || $currentFile == "add_section.php") ? '' : 'collapsed'; ?>" href="home_section.php">
          <i class="bi bi-house-heart-fill"></i>
          <span>Home Section</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "home_tab_section.php" || $currentFile == "add_tab_section.php") ? '' : 'collapsed'; ?>" href="home_tab_section.php">
          <i class="bi bi-house-add-fill"></i>
          <span>Home Tab Section</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_users.php" || $currentFile == "add_user.php") ? '' : 'collapsed'; ?>" href="manage_users.php">
          <i class="bi bi-people-fill"></i>
          <span>Users </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_suggestion.php" || $currentFile == "") ? '' : 'collapsed'; ?>" href="manage_suggestion.php">
          <i class="bi bi-chat-quote-fill"></i>
          <span>Suggestion </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "manage_reports.php" || $currentFile == "") ? '' : 'collapsed'; ?>" href="manage_reports.php">
          <i class="bi bi-bug-fill"></i>
          <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "send_notification.php") ? '' : 'collapsed'; ?>" href="send_notification.php">
          <i class="bi bi-bell-fill"></i>
          <span>Notification </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "smtp_settings.php") ? '' : 'collapsed'; ?>" href="smtp_settings.php">
          <i class="bi bi-envelope-fill"></i>
          <span>SMTP Settings </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "admob_setting.php" || $currentFile == "app_settings.php" || $currentFile == "app_song_download_settings.php" || $currentFile == "app_privacy_policy.php" || $currentFile == "api_setting.php") ? '' : 'collapsed'; ?>" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear-wide-connected"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="sidebar-nav nav-content collapse <?php echo (strpos($_SERVER['REQUEST_URI'], 'admob_setting.php') !== false || strpos($_SERVER['REQUEST_URI'], 'app_settings.php') !== false || strpos($_SERVER['REQUEST_URI'], 'app_song_download_settings.php') !== false || strpos($_SERVER['REQUEST_URI'], 'api_setting.php') !== false || strpos($_SERVER['REQUEST_URI'], 'app_privacy_policy.php') !== false) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentFile == "admob_setting.php") ? '' : 'collapsed'; ?>" href="admob_setting.php">
              <i class="bi bi-award-fill"></i><span>Admob settings</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentFile == "app_settings.php") ? '' : 'collapsed'; ?>" href="app_settings.php">
              <i class="bi bi-award-fill"></i><span>App settings</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentFile == "app_song_download_settings.php") ? '' : 'collapsed'; ?>" href="app_song_download_settings.php">
              <i class="bi bi-award-fill"></i><span>Chapter Download settings</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentFile == "api_setting.php") ? '' : 'collapsed'; ?>" href="api_setting.php">
              <i class="bi bi-award-fill"></i><span>API Setting</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentFile == "app_privacy_policy.php") ? '' : 'collapsed'; ?>" href="app_privacy_policy.php">
              <i class="bi bi-award-fill"></i><span>App Privacy Policy</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo ($currentFile == "api_urls.php") ? '' : 'collapsed'; ?>" href="api_urls.php">
          <i class="bi bi-sliders"></i>
          <span>API URLS</span>
        </a>
      </li>
    </ul>
  </aside><!-- End Sidebar-->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var loaderContainer = document.querySelector('.loader-container');
      if (loaderContainer) {
        loaderContainer.style.display = 'none';
      }
    });
  </script>