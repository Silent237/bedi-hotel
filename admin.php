<?php
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
page_header();
?>
<div id="container">
<?php
navigation(1);
?>
</div>
<?php
page_footer();
?>

