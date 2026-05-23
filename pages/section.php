<div class="card-containe " >
<?php


$query = "SELECT * FROM campaigns";
$result = mysqli_query($conn, $query);

// Check if there are any rows returned
if(mysqli_num_rows($result)>0){
   foreach($result as $row){
   $isBlood = ($row['camp_type'] === 'blood');
?>
<div class="col">
        <div class="card col-md-3 mb-2 mt-1 ">
  <img src="../pages/profile_pic/adm.gif" class="card-img-top" alt="Not_Found" width="90" height="90">
  <div class="card-body">
    <h5 class="card-title"><?php echo $row['camp_title']; ?>
      <?php if($isBlood && !empty($row['blood_group'])): ?>
        <br><small class="text-danger"><i class="bi bi-heart-pulse"></i> Blood Group: <?= $row['blood_group']; ?></small>
      <?php endif; ?>
    </h5>
    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">10%</div>
    
    </div>
    <div class="caption">
      Raised: <?php if($isBlood): ?><i class="bi bi-droplet"></i><?php echo $row['amt_collected']; ?> pint<?= $row['amt_collected'] != 1 ? 's' : ''; ?><?php else: ?><i class="bi bi-currency-dollar"></i><?php echo $row['amt_collected']; ?><?php endif; ?>
    </div>
    <hr class="vertical-line">
    <div class="created-by">
      Created by: Joseph
    </div>
    <div class="card-footer">
      <div class="see-all-donors">
        See all Donors
      </div>
      <button class="btn btn-primary read-more">Read more</button>
    </div>
  </div>
</div>
</div>
    <?php  
  
    }
} else {
    // No campaigns found
    echo '<div class="col-12 d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 400px;">
            <div class="text-center">
              <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
              <p class="mt-3 text-muted fs-5 mb-1">No campaigns available at the moment</p>
              <p class="text-muted small">Check back later for new campaigns</p>
            </div>
          </div>';
}

// Close the database connection
mysqli_close($conn);
?>
</div>


