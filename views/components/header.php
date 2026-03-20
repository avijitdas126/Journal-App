<?php
$mode = $_GET['mode'] ?? 'draft';
?>
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
    top: 35px;
    z-index: 120;
    margin-right: 12px;
    min-width: 180px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    border: 1px solid #e3e3e3;
    overflow: hidden;
  }

  #menu ul {
    cursor: pointer;
  }

  #menu ul {
    padding: 0;
    margin: 0;
  }

  #menu ul li {
    padding: 12px 22px;
    background-color: white;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
    font-size: 1rem;
    cursor: pointer;
    border: none;
  }

  #menu ul li:hover {
    background-color: #e9ecef;
  }

  #nav .profile:hover #menu {
    display: block;
  }
</style>
<div id="navbar">

  <div class="container-fluid bg-primary" id="nav">
    <!-- Hamburger icon for offcanvas -->
    <button class="btn btn-light d-md-none me-2 float-start" type="button" data-bs-toggle="offcanvas"
      data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-list"
        viewBox="0 0 16 16">
        <path fill-rule="evenodd"
          d="M2.5 12.5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1h-10a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1h-10a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1h-10a.5.5 0 0 1-.5-.5z" />
      </svg>
    </button>
    <!-- Offcanvas menu -->
    <style>
      .offcanvas-user {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e3e3e3;
      }

      .offcanvas-user img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
      }

      .offcanvas-user-info {
        display: flex;
        flex-direction: column;
      }

      .offcanvas-user-info .name {
        font-weight: 600;
        font-size: 1.1rem;
        color: #1976d2;
      }

      .offcanvas-user-info .role {
        font-size: 0.95rem;
        color: #555;
        opacity: 0.8;
      }

      .offcanvas-nav {
        list-style: none;
        padding: 0;
        margin: 0;
      }

      .offcanvas-nav li {
        margin-bottom: 0.5rem;
      }

      .offcanvas-nav a {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.7rem 1.1rem;
        border-radius: 8px;
        font-size: 1.05rem;
        color: #333;
        text-decoration: none;
        transition: background 0.18s;
      }

      .offcanvas-nav a.active,
      .offcanvas-nav a:hover {
        background: #e3f0ff;
        color: #1976d2;
      }

      .offcanvas-nav svg {
        font-size: 1.2rem;
        color: #1976d2;
      }
    </style>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="offcanvas-user">
          <img
            src="<?php echo isset($_SESSION['avatar_url']) ? $_SESSION['avatar_url'] : 'https://dummyimage.com/400x400/000/fff&text=' . urlencode($_SESSION['name'][0]); ?>"
            alt="avatar" />
          <div class="offcanvas-user-info">
            <span class="name"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <span class="role"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
          </div>
        </div>
        <ul class="offcanvas-nav">
          <li>
            <a href="?page=overview" class="<?php if ($page == 'overview') {
              echo 'active';
            } ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                class="bi bi-house-door" viewBox="0 0 16 16">
                <path
                  d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 2 7.5V14a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3h2v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1V7.5a.5.5 0 0 0-.146-.354l-6-6z" />
                <path d="M13 2.5V6l.5.5L14 6V2.5a.5.5 0 0 0-1 0z" />
              </svg>
              Overview
            </a>
          </li>
          <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'student' || $_SESSION['role'] == 'teacher') { ?>
          <li>
            <a href="?page=article" class="<?php if (($page == 'article' || $page=='edit_review_article' || $page == 'edit_article' || $page == 'add_article')) {
              echo 'active';
            } ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                class="bi bi-pencil-square" viewBox="0 0 16 16">
                <path
                  d="M15.502 1.94a.5.5 0 0 1 0 .706l-1 1a.5.5 0 0 1-.707 0l-1-1a.5.5 0 0 1 0-.707l1-1a.5.5 0 0 1 .707 0zm-1.75 2.456-1-1L4.939 11.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l7.813-7.813z" />
                <path fill-rule="evenodd"
                  d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 13.5 1h-11A1.5 1.5 0 0 0 1 2.5v11zm1-11A.5.5 0 0 1 2.5 2h11a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11z" />
              </svg>
              Post an Article
            </a>
          </li>
          <?php }?>
          <?php if ( $_SESSION['role'] == 'admin') { ?>
            <li>
              <a href="?page=reviews" class="<?php if ($page == 'reviews' || $page == 'in_review' || $page == 'add_review') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                  class="bi bi-journal-text" viewBox="0 0 16 16">
                  <path d="M5 8h6v1H5V8zm0 2h6v1H5v-1z" />
                  <path
                    d="M2 2.5A.5.5 0 0 1 2.5 2h11a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11zm1 0v11h10v-11H3z" />
                </svg>
                Reviews
              </a>
            </li>
          <?php } ?>
          <?php if ($_SESSION['role'] == 'admin') { ?>
            <li>
              <a href="?page=add_admin" class="<?php if ($page == 'add_admin') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                  class="bi bi-person-plus" viewBox="0 0 16 16">
                  <path
                    d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4.5 8a5.5 5.5 0 1 0-9 0h9zm3-7a.5.5 0 0 1 .5.5V12h2.5a.5.5 0 0 1 0 1H14v2.5a.5.5 0 0 1-1 0V13h-2.5a.5.5 0 0 1 0-1H13v-2.5a.5.5 0 0 1 1 0z" />
                </svg>
                Add Admin
              </a>
            </li>
            <li>
              <a href="?page=add_teacher" class="<?php if ($page == 'add_teacher') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people"
                  viewBox="0 0 16 16">
                  <path
                    d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                </svg>
                Add Teacher 
              </a>
            </li>
            <li>
              <a href="?page=add_student" class="<?php if ($page == 'add_student') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-file-person-fill" viewBox="0 0 16 16">
                  <path
                    d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2m-1 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-3 4c2.623 0 4.146.826 5 1.755V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-1.245C3.854 11.825 5.377 11 8 11" />
                </svg>
                Add Student
              </a>
            </li>
            <li>
              <a href="?page=add_category" class="<?php if ($page == 'add_category') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tags"
                  viewBox="0 0 16 16">
                  <path
                    d="M3 2a1 1 0 0 0-1 1v2.586a1 1 0 0 0 .293.707l7.586 7.586a1 1 0 0 0 1.414 0l2.586-2.586a1 1 0 0 0 0-1.414L5.707 2.293A1 1 0 0 0 5 2H3zm1 1h2.586l7.586 7.586-2.586 2.586-7.586-7.586V3zm1.5 1a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z" />
                </svg>
                Add Category
              </a>
            </li>

            <li>
              <a href="?page=add_developer" class="<?php if ($page == 'add_developer') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code"
                  viewBox="0 0 16 16">
                  <path
                    d="M5.854 4.854a.5.5 0 1 0-.708-.708l-3.5 3.5a.5.5 0 0 0 0 .708l3.5 3.5a.5.5 0 0 0 .708-.708L2.707 8zm4.292 0a.5.5 0 0 1 .708-.708l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708L13.293 8z" />
                </svg>
                Add Developer
              </a>
            </li>

            <li>
              <a href="?page=notice" class="<?php if ($page == 'notice') {
                echo 'active';
              } ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal"
                  viewBox="0 0 16 16">
                  <path
                    d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2" />
                  <path
                    d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z" />
                </svg>
                Notice
              </a>
            </li>
          <?php } ?>
          <li>
            <a href="?page=leaderboard" class="<?php if ($page == 'leaderboard') {
              echo 'active';
            } ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-bar-chart-fill" viewBox="0 0 16 16">
                <path
                  d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z" />
              </svg>

              Leaderboard
            </a>
          </li>
          <li>
            <a href="?page=update_profile" class="<?php if ($page == 'update_profile') {
              echo 'active';
            } ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person"
                viewBox="0 0 16 16">
                <path
                  d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
              </svg>
              Update Profile
            </a>
          </li>
        </ul>
      </div>
    </div>
    <?php if (($page == 'add_article' || $page == 'edit_article') && $mode == 'draft') {
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
              <input type="hidden" name="id" value=<?php echo $_SESSION['user_id']; ?>>
              <input type="hidden" name="article_id" value=<?php echo $_GET['id']; ?>>
              <input type="hidden" name="role" value="<?php echo $_SESSION['role']; ?>">
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

    <img
      src="<?php echo isset($_SESSION['avatar_url']) ? $_SESSION['avatar_url'] : 'https://dummyimage.com/400x400/000/fff&text=' . urlencode($_SESSION['name'][0]); ?>"
      alt="avatar" width="32" height="32" class="rounded-circle me-2" />
    <div id="menu">
      <ul>
        <li onclick="window.location.href='profile.php?username=<?php echo $_SESSION['username'] ?>'">Profile</li>
        <li onclick="window.location.href='?page=add_article&id=' + Date.now()">Write a Post</li>
        <li onclick="window.location.href='signout.php'">Signout</li>
      </ul>
    </div>
  </div>
</div>

</div>