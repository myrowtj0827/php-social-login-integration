
<!--  Header  -->
<header id="">
    <div class="flex-container header-txt justify-middle-contents">
        <div class="rL-float rL-margin justify-middle-contents">
            <div class="phoneMax">
                <a href="index.php"><img class="cart-size mouseCursor" src="assets/images/home/cart.png" alt="" /></a>
            </div>
            <div class="phoneMax">
                <div class="input-filed">
                    <input class="air-input" type="text" placeholder="Search.." />
                </div>
            </div>

            <?php
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
            ?>
                <div id="logout" class="rL-float phoneMax">
                    <a class="boxShawDow signJoin justify-middle-contents" href="logout.php">Logout</a>
                </div>
            <?php
                } else {
            ?>
                <div class="rL-float phoneMax">
                    <a class="boxShawDow signJoin justify-middle-contents" href="signUp.php">Sign Up</a>
                    <a class="boxShawDow signJoin justify-middle-contents" href="logIn.php">Join Now</a>
                </div>
            <?php
                }
            ?>
        </div>
    </div>

    <div class="margin-center-width">
        <div class="rL-float rL-navbarMargin navBar-height justify-middle-contents">
<!--  mobile drop down menu  -->
            <div class="menubar-icon mouseCursor">
                <div class="dropdown">
                    <div class="dropdown-toggle"  data-toggle="dropdown">
                        <img src="assets/images/home/menu-icon.png" alt="" />
                    </div>

                    <ul class="dropdown-menu dropdown-menu-mobile">
                        <li><div class="rectangle-trans-mobile"></div></li>
                        <li><a class="dropDown-item menu-mobile header-btn" href="">All Store</a></li>
                        <li><a class="dropDown-item menu-mobile header-btn" href="store.php">XYZ</a></li>
                        <li><a class="dropDown-item menu-mobile header-btn" href="currentBalance.php">Qweqea</a></li>
                        <li><a class="dropDown-item menu-mobile header-btn" href="#">
                            <span>Some text</span>
                        </a></li>

                        <li><a class="dropDown-item menu-mobile header-btn" href="#">Help</a></li>
                    </ul>
                </div>
            </div>

            <div class="txt20 mouseCursor collections lapDesktop-hidden">
                <div class="dropdown">
                    <div class="dropdown-toggle" data-toggle="dropdown">
                        <span class="header-btn">All Store</span>
                        <img class="dropDownIcon-size" src="assets/images/home/downBtnBlack.png" alt=""/>
                    </div>

                    <ul class="dropdown-menu">
                        <li><div class="rectangle-trans"></div></li>
                        <li><a class="dropDown-item header-btn" href="index.php">Air Travel &Tours</a></li>
                        <li><a class="dropDown-item header-btn" href="">Automotive</a></li>
                        <li><a class="dropDown-item header-btn" href="#">Books & Education</a></li>
                        <li><a class="dropDown-item header-btn" href="#">Brauty</a></li>
                        <li><a class="dropDown-item header-btn" href="#">Business & IT Services</a></li>
                        <li><a class="dropDown-item header-btn" href="#">Electronics</a></li>
                        <li><a class="dropDown-item header-btn" href="">Fashion</a></li>
                    </ul>
                </div>
            </div>

            <div class="txt20 mouseCursor lapDesktop-hidden">
                <a class="header-btn" href="store.php">XYZ</a>
            </div>

            <div class="txt20 mouseCursor lapDesktop-hidden">
                <a class="header-btn" href="currentBalance.php">Qweqea</a>
            </div>

            <div class="txt20 mouseCursor lapDesktop-hidden">
                <a class="header-btn" href="#">Some text</a>
            </div>
            <div class="txt20 mouseCursor lapDesktop-hidden">
                <a class="header-btn" href="">Help</a>
            </div>
        </div>
    </div>
</header>