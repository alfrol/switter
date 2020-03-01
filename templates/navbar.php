<nav class="navbar navbar-expand-lg sticky-top" style="background-color: #dcac7b;">
    <a href="<?php echo PATH_PREFIX . 'home'; ?>" class="navbar-brand text-dark">
        Switter <img src="<?php echo PATH_PREFIX; ?>Switter.png" alt="Switter logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigationBar"
            aria-controls="navigationBar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between ml-lg-3" id="navigationBar">
        <div class="navbar-nav">
            <a href="<?php echo PATH_PREFIX . 'home'; ?>"
               class="nav-item nav-link text-dark <?php if (strpos($_SERVER['REQUEST_URI'], 'home')) echo 'active'; ?>">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="<?php echo PATH_PREFIX . 'profile?id=' . $_SESSION['id']; ?>"
               class="nav-item nav-link text-dark <?php if (strpos($_SERVER['REQUEST_URI'], 'profile')) echo 'active'; ?>">
                <i class="fas fa-user"></i> Profile
            </a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle text-dark" id="notificationMenuDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <div class="dropdown-menu" aria-labelledby="notificationMenuDropdown">
                    <p class="dropdown-item">You have 0 notifications</p>
                </div>
            </div>
        </div>

        <form class="form-inline ml-lg-5 pl-lg-5"
              action="<?php echo htmlspecialchars(PATH_PREFIX . 'search'); ?>"
              method="POST" id="search_form">
            <div class="input-group">
                <input type="search" class="form-control border-dark" placeholder="Search" name="search_value" id="search_value"
                       aria-label="Search" aria-describedby="searchBarIcon" value="<?php if (isset($_POST['search_value'])) echo $_POST['search_value']; ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-dark" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="navbar-nav">
            <a href="<?php echo PATH_PREFIX . 'new-switt'; ?>"
               class="nav-item nav-link text-dark <?php if (strpos($_SERVER['REQUEST_URI'],'new_switt')) echo 'active'; ?>">
                <i class="fas fa-feather-alt"></i> New Switt
            </a>
        </div>

        <form class="form-inline" action="<?php echo htmlspecialchars(PATH_PREFIX . '?logout=true') ?>" method="POST">
            <div class="navbar-nav">
                <button class="btn btn-link nav-item nav-link text-dark" type="submit">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </button>
            </div>
        </form>
    </div>
</nav>
