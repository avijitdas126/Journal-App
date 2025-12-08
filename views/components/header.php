<style>
  #navbar {
    width: 100%;
    position: relative;
  }

  #nav {
    padding: 10px;
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: flex-end;
  }

  #nav img {
    cursor: pointer;
  }

  #menu {
    display: none;
    position: absolute;
    right: 0;
    top: 45px;
    z-index: 12px;
    margin-right: 12px;
  }

  #menu ul {
    cursor: pointer;
  }

  #menu ul li {
    padding: 10px 20px;
    background-color: white;
    border: 1px solid #ddd;
  }

  #menu ul li:hover {
    background-color: #f8f9fa;
  }

  #nav .profile:hover #menu {
    display: block;
  }
</style>
<div id="navbar">
  <div class="container-fluid bg-primary" id="nav">
    <?php if ($page == 'add_article' || $page == 'edit_article') {
      ?>
      <!-- Button trigger modal -->
      <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <svg width="25px" height="25px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4.99997 5.50005H20M7.5 14L12.5 9.00003L17.5 14" stroke="#ffffffff" stroke-width="1.2" />
          <path d="M12.5 9.00003V20" stroke="#ffffffff" stroke-width="1.2" />
        </svg>
        Submit</button>

      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Make this article submit?</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to submit this article? Once submitted, it will be reviewed and published
              accordingly.
              </div>
              <form action="<?php baseurl("components/api/updateStatus.php") ?>" method="post">
              <input type="hidden" name="id" value="<?php echo $_SESSION['user_id'];  ?>">
              <input type="hidden" name="article_id" value="<?php echo $_GET['id'];  ?>">
              <input type="hidden" name="status" value="submitted">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- <button type="button" class="btn btn-dark">Submit</button> -->
      <?php
    } ?>

    <div class="profile">

      <img src="https://github.com/mdo.png" alt="avatar" width="32" height="32" class="rounded-circle me-2" />
      <div id="menu">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">An item</li>
          <li class="list-group-item">A second item</li>
          <li class="list-group-item">A third item</li>
          <li class="list-group-item">A fourth item</li>
          <li class="list-group-item">And a fifth one</li>
        </ul>
      </div>
    </div>
  </div>

</div>