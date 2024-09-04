<footer id="footer" class="footer">
  <div class="copyright">
    &copy; Copyright <strong><a href="https://vocsyinfotech.com" target="_blank" style="color: #4154f1 !important;"><span>Vocsy Infotech</span></a></strong>. © <?php echo date("Y"); ?> All Rights Reserved
  </div>
  </div>
</footer>
<script>
  $('.label.ui.dropdown')
    .dropdown();

  $('.no.label.ui.dropdown')
    .dropdown({
      useLabels: false
    });

  $('.ui.button').on('click', function() {
    $('.ui.dropdown')
      .dropdown('restore defaults')
  })
</script>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script type="text/javascript" src="assets/js/vendor.js"></script>
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/global/global.min.js"></script>
<script src="assets/vendor/quill/quill.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/js/notify.min.js"></script>
<script src="assets/sweetalert/sweetalert.min.js"></script>
<script src="assets/js/main.js"></script>
<?php if (isset($_SESSION['msg'])) {  ?>

  <script type="text/javascript">
    $('.notifyjs-corner').empty();
    $.notify(
      '<?php _e($client_lang[$_SESSION["msg"]]); ?>', {
        position: "top center",
        className: '<?= isset($_SESSION["class"]) ? $_SESSION["class"] : "success" ?>'
      }
    );
  </script>

<?php
  unset($_SESSION['msg']);
  unset($_SESSION['class']);
}
?>
</body>

</html>