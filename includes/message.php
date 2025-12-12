<?php
if(isset($_SESSION['message'])) :?>
<div class="alert alert-warning alert-dismissible fade show mt-1 text-center" role="alert" style="width:500px;margin-left:370px;">
<strong> <?= $_SESSION['message'];?></strong>
<button type="button" class="btn-close" data-bs-dismiss="alert" ></button>
</div>
<?php
unset($_SESSION['message']);
endif;
?>