<header>
    <nav>
        <div>
            <a href="/newsfeed.php" class=logo>
                <img src="./image.jpg" alt="logo">
            </a>
        </div>
        <div>
            <span class="nav-links">
                <?php
                     if (isset($_SESSION['email'])) {

                        if ($_SESSION['email'] != "admin") {
                            echo '<a href="userinfo.php"><i class="fa-solid fa-user"></i></a>';
                        }

                        echo '<a href="../logout.php"><i class= "fa-solid fa-arrow-right-from-bracket"></i></a>';
                     }
                ?>
            </span>
        </div>
    </nav>
</header>