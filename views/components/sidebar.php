
<div class="d-block d-md-none">
<a class="btn btn-light " data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
  <img src="<?php baseurl("assets/menu.svg") ?>" alt="menu" height="24" width="24">
</a>
</div>
<div class="d-flex flex-column flex-shrink-0 p-3 d-none d-md-block text-white bg-dark" id="sidebar" style="width: 280px;max-height: 100vh;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4">Sidebar</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto" style="min-height: calc(100vh - 165px);">
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
      <?php if($_SESSION['department_id']==20){ ?>
      <li>
        <a href="?page=reviews" class="nav-link <?php if($page=='reviews') {echo'active';}else{ echo "text-white";} ?>">
          Reviews
        </a>
      </li>
      <?php } ?>
      <li>
        <a href="#" class="nav-link text-white">
          
          Products
        </a>
      </li>
      <li>
        <a href="#" class="nav-link text-white">
          
          Customers
        </a>
      </li>
    </ul>
    <hr>

<div class="dropdown mt-auto">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
       id="dropdownUser1" data-bs-toggle="dropdown">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32"
             class="rounded-circle me-2">
        <strong>mdo</strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow"
        aria-labelledby="dropdownUser1">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Sign out</a></li>
    </ul>
</div>

  </div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
      Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.
    </div>
    <div class="dropdown mt-3">
      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        Dropdown button
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
      </ul>
    </div>
  </div>
</div>