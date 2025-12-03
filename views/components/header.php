<style>
  #navbar {
    min-width: calc(100vw - 280px);
    position: relative;
  }

  #nav {
    padding: 10px;
    display: flex;
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