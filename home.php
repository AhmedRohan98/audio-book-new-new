<?php
include("includes/header.php");

$qry_cat = "SELECT COUNT(*) as num FROM tbl_category";
$total_category = mysqli_fetch_array(mysqli_query($mysqli, $qry_cat));
$total_category = $total_category['num'];

$qry_art = "SELECT COUNT(*) as num FROM tbl_artist";
$total_artist = mysqli_fetch_array(mysqli_query($mysqli, $qry_art));
$total_artist = $total_artist['num'];

$qry_mp3 = "SELECT COUNT(*) as num FROM tbl_mp3";
$total_mp3 = mysqli_fetch_array(mysqli_query($mysqli, $qry_mp3));
$total_mp3 = $total_mp3['num'];


$qry_album = "SELECT COUNT(*) as num FROM tbl_album";
$total_album = mysqli_fetch_array(mysqli_query($mysqli, $qry_album));
$total_album = $total_album['num'];


$qry_playlist = "SELECT COUNT(*) as num FROM tbl_playlist";
$total_playlist = mysqli_fetch_array(mysqli_query($mysqli, $qry_playlist));
$total_playlist = $total_playlist['num'];

$qry_users = "SELECT COUNT(*) as num FROM tbl_users";
$total_users = mysqli_fetch_array(mysqli_query($mysqli, $qry_users));
$total_users = $total_users['num'];

$qry_banner = "SELECT COUNT(*) as num FROM tbl_banner";
$total_banner = mysqli_fetch_array(mysqli_query($mysqli, $qry_banner));
$total_banner = $total_banner['num'];

$qry_song_suggest = "SELECT COUNT(*) as num FROM tbl_song_suggest";
$total_song_suggest = mysqli_fetch_array(mysqli_query($mysqli, $qry_song_suggest));
$total_song_suggest = $total_song_suggest['num'];

$qry_reports = "SELECT COUNT(*) as num FROM tbl_reports";
$total_reports = mysqli_fetch_array(mysqli_query($mysqli, $qry_reports));
$total_reports = $total_reports['num'];

$author_qry = "SELECT * FROM tbl_artist ORDER BY tbl_artist.id DESC LIMIT 5";
$author_result = mysqli_query($mysqli, $author_qry);

$category_qry = "SELECT * FROM tbl_category ORDER BY tbl_category.cid  DESC LIMIT 5";
$category_result = mysqli_query($mysqli, $category_qry);

$books_qry = "SELECT * FROM tbl_album
LEFT JOIN tbl_category ON tbl_album.cat_ids= tbl_category.cid
LEFT JOIN tbl_artist ON tbl_album.aid= tbl_artist.id
ORDER BY tbl_album.aid DESC LIMIT 5";
$books_result = mysqli_query($mysqli, $books_qry);


$books_view = "SELECT * FROM tbl_album
LEFT JOIN tbl_category ON tbl_album.cat_ids = tbl_category.cid
LEFT JOIN tbl_artist ON tbl_album.aid= tbl_artist.id
ORDER BY tbl_album.total_views DESC LIMIT 5";
$books_view_result = mysqli_query($mysqli, $books_view);

$cat_qry = "SELECT * FROM tbl_category";
$cat_result = mysqli_query($mysqli, $cat_qry);
if (!$cat_result) {
	die("Error in SQL query: " . mysqli_error($mysqli));
}
$categories = array();
while ($row = mysqli_fetch_array($cat_result)) {
	$categories[$row['cid']] = $row['category_name'];
}

$author_qry = "SELECT * FROM tbl_artist";
$author_result2 = mysqli_query($mysqli, $author_qry);
if (!$author_result2) {
	die("Error in SQL query: " . mysqli_error($mysqli));
}
$authors_name_get = array();
while ($row = mysqli_fetch_array($author_result2)) {
	$authors_name_get[$row['id']] = $row['artist_name'];
}
?>
<main id="main" class="main">

	<div class="pagetitle">
		<h1>Dashboard</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item active">Dashboard</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->
	<?php
		if (PURCHASE == "disabled") {
		?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="alert alert-danger alert-dismissible  in" role="alert">
					<h4 id="oh-snap!-you-got-an-error!">
						<i class="fa fa-hand-o-right"></i> This is only demo.
					</h4>
					<p style="margin-bottom: 10px">You have no right operate to <strong>ADD,</strong> <strong>EDIT</strong> and <strong>DELETE</strong> function.</p>
				</div>
			</div>

		<?php
		}
		?>
	<section class="section dashboard">
		<div class="row">

			<!-- Left side columns -->
			<div class="col-lg-12">
				<div class="row">

					<!-- Sales Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card sales-card">
							<a href="manage_category.php">
								<div class="card-body">
									<h5 class="card-title">Categories</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi-diagram-3-fill"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_category)); ?></h6>
										</div>
									</div>
								</div>
							</a>
						</div>
					</div><!-- End Sales Card -->

					<!-- Revenue Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card revenue-card">
							<a href="manage_artist.php">
								<div class="card-body">
									<h5 class="card-title">Author</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi-person-square"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_artist)); ?></h6>
										</div>
									</div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card customers-card">
							<a href="manage_album.php">
								<div class="card-body">
									<h5 class="card-title">Books</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi-book-fill"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_album)); ?></h6>
										</div>
									</div>
								</div>
							</a>
						</div>

					</div><!-- End Customers Card -->
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card chapter-card">
							<a href="manage_mp3.php">
								<div class="card-body">
									<h5 class="card-title">Chapter</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-cassette-fill"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_mp3)); ?></h6>
										</div>
									</div>
								</div>

							</a>
						</div>

					</div>
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card Playlist-card">
							<a href="manage_playlist.php">
								<div class="card-body">
									<h5 class="card-title">Playlist</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-list-ul"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_playlist)); ?></h6>
										</div>
									</div>

								</div>
							</a>
						</div>

					</div>
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card users-card">
							<a href="manage_users.php">
								<div class="card-body">
									<h5 class="card-title">Users</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-people-fill"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_users)); ?></h6>
										</div>
									</div>

								</div>
							</a>
						</div>

					</div>
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card banners-card">
							<a href="manage_banners.php">
								<div class="card-body">
									<h5 class="card-title">Banners</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-sliders"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_banner)); ?></h6>
										</div>
									</div>
								</div>
							</a>
						</div>

					</div>
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card suggestion-card">
							<a href="manage_suggestion.php">
								<div class="card-body">
									<h5 class="card-title">Suggestion</h5>
									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-chat-quote-fill"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_song_suggest)); ?></h6>
										</div>
									</div>
								</div>
							</a>
						</div>

					</div>
					<div class="col-xxl-3 col-xl-12">

						<div class="card info-card reports-card">
							<a href="manage_reports.php">
								<div class="card-body">
									<h5 class="card-title">Reports</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-bug-fill"></i>
										</div>
										<div class="ps-3">
											<h6><?php _e(thousandsNumberFormat($total_reports)); ?></h6>
										</div>
									</div>

								</div>
							</a>
						</div>

					</div>

					<!-- Top Selling -->

				</div>

			</div>
			<?php
			$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
			$query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as user_count FROM tbl_users WHERE YEAR(created_at) = ? GROUP BY DATE_FORMAT(created_at, '%Y-%m')";

			$stmt = mysqli_prepare($mysqli, $query);
			mysqli_stmt_bind_param($stmt, 's', $selectedYear);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			$response = array('labels' => array(), 'data' => array());

			while ($row = mysqli_fetch_assoc($result)) {
				$response['labels'][] = $row['month'];
				$response['data'][] = $row['user_count'];
			}

			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
				header('Content-Type: application/json');
				echo json_encode($response);
			} else {
			?>
				<div class="col-lg-9">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title">Users Chart</h5>

							<label for="yearSelect">Select Year:</label>
							<select id="yearSelect">
								<?php
								$currentYear = date('Y');
								$years = range($currentYear - 2, $currentYear);
								foreach ($years as $year) {
									$selected = ($year == $selectedYear) ? 'selected' : '';
									echo "<option value='$year' $selected>$year</option>";
								}
								?>
							</select>

							<canvas id="barChart" style="max-height: 400px;"></canvas>
							<script>
								document.addEventListener("DOMContentLoaded", () => {
									let chart;

									function initializeChart(labels, data) {
										chart = new Chart(document.querySelector('#barChart'), {
											type: 'bar',
											data: {
												labels: labels,
												datasets: [{
													label: 'Bar Chart',
													data: data,
													backgroundColor: 'rgba(54, 162, 235, 0.2)',
													borderColor: 'rgb(54, 162, 235)',
													borderWidth: 1
												}]
											},
											options: {
												scales: {
													y: {
														beginAtZero: true
													}
												}
											}
										});
									}

									initializeChart(<?php echo json_encode($response['labels']); ?>, <?php echo json_encode($response['data']); ?>);

									document.getElementById('yearSelect').addEventListener('change', function() {
										let selectedYear = this.value;
										window.location.href = `home.php?year=${selectedYear}`;
									});
								});
							</script>
						</div>
					</div>
				</div>
			<?php
			}
			?>
			<div class="col-lg-3">
				<!-- News & Updates Traffic -->
				<div class="card">
					<div class="card-body pb-0">
						<h5 class="card-title">Recent &amp; Authors</h5>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th scope="col">Author</th>
										<th scope="col">Image</th>
									</tr>
								</thead>
								<tbody>
									<?php
									while ($authors = mysqli_fetch_array($author_result)) {
									?>
										<tr>
											<td><?php _e($authors['artist_name']); ?></td>
											<th scope="row"><img src="images/<?php _e($authors['artist_image']); ?>" alt="" style="width: 80px;border-radius: 5px;height: 70px;"></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<!-- News & Updates Traffic -->
				<div class="card">
					<div class="card-body pb-0">
						<h5 class="card-title">Recent &amp; Categorys</h5>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th scope="col">Category</th>
										<th scope="col">Image</th>
									</tr>
								</thead>
								<tbody>
									<?php
									while ($category = mysqli_fetch_array($category_result)) {
									?>
										<tr>
											<td><?php _e($category['category_name']); ?></td>
											<th scope="row"><img src="images/<?php _e($category['category_image']); ?>" alt="" style="width: 80px;border-radius: 5px;height: 70px;"></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div><!-- End News & Updates -->
			</div>

			<div class="col-lg-9">
				<div class="card">
					<div class="card-body pb-0">
						<h5 class="card-title">Recent & Books</h5>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th scope="col">Category</th>
										<th scope="col">Author</th>
										<th scope="col">Title</th>
										<th scope="col">Image</th>
									</tr>
								</thead>
								<tbody>
									<?php
									while ($books_row = mysqli_fetch_array($books_result)) {
									?>
										<tr>
											<td scope="row">
												<?php
												$cat_ids = explode(',', $books_row['cat_ids']);
												foreach ($cat_ids as $cat_id) {
													if (isset($categories[$cat_id])) {
														_e($categories[$cat_id] . ",");
													}
												}
												?>
											</td>
											<td scope="row">
												<?php
												$cat_ids = explode(',', $books_row['cat_ids']);
												foreach ($cat_ids as $cat_id) {
													if (isset($categories[$cat_id])) {
														_e($categories[$cat_id] . ",");
													}
												}
												?>
											</td>
											<td><?php _e($books_row['album_name']); ?></td>
											<th scope="row"><img src="images/<?php _e($books_row['album_image']); ?>" alt="" style="width: 80px;border-radius: 5px;height: 70px;"></th>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div><!-- End Right side columns -->

		</div>
	</section>

</main><!-- End #main -->
<?php include("includes/footer.php"); ?>