

<div class="d-flex flex-column flex-shrink-0 p-3 d-none d-md-block text-white bg-dark" id="sidebar" style="width: 280px; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; overflow-x: hidden;">
    <a href="" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4">Sidebar</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto" style="min-height: auto;">
      <li class="nav-item">
        <a href="?page=overview" class="nav-link <?php if($page=='overview') {echo'active';}else{ echo "text-white";} ?>" aria-current="page">
          
          Overview
        </a>
      </li>
      <li>
        <a href="?page=article" class="nav-link  <?php if($page=='article' || $page=='edit_article'||$page=='add_article') {echo'active';}else{ echo "text-white";} ?>">
          Post an Article
        </a>
      </li>
      <?php if($_SESSION['department_id']==20&&($_SESSION['role']=='teacher' || $_SESSION['role']=='admin')){ ?>
      <li>
        <a href="?page=reviews" class="nav-link <?php if($page=='reviews' || $page=='in_review'||$page=='add_review') {echo'active';}else{ echo "text-white";} ?>">
          Reviews
        </a>
      </li>
      <?php } ?>
      <?php if($_SESSION['department_id']==20&& $_SESSION['role']=='admin'){ ?>
      <li>
        <a href="?page=add_admin" class="nav-link <?php if($page=='add_admin') {echo'active';}else{ echo "text-white";} ?>">
          
          Add Admin
        </a>
      </li>
      <li>
        <a href="?page=add_category" class="nav-link <?php if($page=='add_category') {echo'active';}else{ echo "text-white";} ?>">
          Add Category
        </a>
      </li>
      <li>
        <a href="?page=add_developer" class="nav-link <?php if($page=='add_developer') {echo'active';}else{ echo "text-white";} ?>">
          Add Developer
        </a>
      </li>
      <li>
        <a href="?page=notice" class="nav-link <?php if($page=='notice') {echo'active';}else{ echo "text-white";} ?>">
          Notices
        </a>
      </li>
      
       <?php } ?>
       <li>
        <a href="?page=leaderboard" class="nav-link <?php if($page=='leaderboard') {echo'active';}else{ echo "text-white";} ?>">
          Leaderboard
        </a>
      </li>
    </ul>
    <hr>

<div class="dropdown mt-auto">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
       id="dropdownUser1" data-bs-toggle="dropdown">
        <img src="<?php echo'https://dummyimage.com/400x400/000/fff&text=' . urlencode($_SESSION['name'][0]); ?>" alt="" width="32" height="32"
             class="rounded-circle me-2">
        <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow"
        aria-labelledby="dropdownUser1">
        <li><a class="dropdown-item" href="signout.php">Sign out</a></li>
    </ul>
</div>

  </div>

