<?php
$pagesToShow = 10; // Number of pages to show at a time

if ($page == 0) {
    $page = 1;
}

$prev = $page - 1;
$next = $page + 1;
$lastpage = ceil($total_pages / $limit);
$LastPagem1 = $lastpage - 1;


echo "<nav aria-label='Page navigation example'>";
echo "<ul class='pagination pagination-lg'>";

if ($page > 1) {
    echo "<li class='page-item'><a class='page-link' href='$targetpage?page=$prev&keyword=$searchInput' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
} else {
    echo "<li class='page-item disabled'><span class='page-link' aria-hidden='true'>&laquo;</span></li>";
}

$start = max(1, min($page - floor($pagesToShow / 2), $lastpage - $pagesToShow + 1));
$end = min($start + $pagesToShow - 1, $lastpage);

for ($counter = $start; $counter <= $end; $counter++) {
    if ($counter == $page) {
        echo "<li class='page-item active'><span class='page-link'>$counter</span></li>";
    } else {
        echo "<li class='page-item'><a class='page-link' href='$targetpage?page=$counter&keyword=$searchInput'>$counter</a></li>";
    }
}

if ($page < $lastpage) {
    echo "<li class='page-item'><a class='page-link' href='$targetpage?page=$next&keyword=$searchInput' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";
} else {
    echo "<li class='page-item disabled'><span class='page-link' aria-hidden='true'>&raquo;</span></li>";
}

echo "</ul>";
echo "</nav>";
?>
