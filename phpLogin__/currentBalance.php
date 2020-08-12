<?php
    require_once "config.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Current Balence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <script src="./assets/js/fontawesomeKitConfiga076d05399.js"></script>
    <link rel="stylesheet" href="assets/css/font-awesome4.7.0.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="./assets/js/jquery-3.5.1.js"></script>
    <script src="./assets/js/jquery3.5.1.min.js"></script>

    <script src="./assets/js/p50.5.7.min.js"></script>
    <script src="./assets/js/owl.carousel2.3.4.min.js"></script>
    <link rel="stylesheet" href="assets/css/owl.carousel2.3.4.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default2.3.4.min.css">

    <script src="./assets/js/bootstrap4.5.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap4.5.0.min.css">

    <script src="./assets/js/main.js"></script>
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>

<div class="margin-center-width homeHeader">
    <?php
        require('header.php');
    ?>

    <div class="header-hr"><hr /></div>

    <section class="rL-margin point-coverter">
        <div class="txt-black38">Point Coverter</div>

        <div class="balence-card">
            <div class="logIn-lineHeight txt-purple32"> Current Balance : 253553 Hala Points</div>

            <hr />

            <div class="rL-float-table card-RL justify-middle-contents">
                <div class="txt-black32">Rewards Program</div>

                <div id="custom-select">
                    <select>
                        <option selected disabled>Program search</option>
                        <option><a class="txtHover">1</a></option>
                        <option><a class="txtHover">2</a></option>
                        <option><a class="txtHover">3</a></option>
                        <option><a class="txtHover">4</a></option>
                    </select>
                </div>
            </div>

            <div class="card-RL">
                <div class="rewards-program">
                    <div class="rL-float table-thBg justify-middle-contents">
                        <div class="txt-white26">Rewards Program</div>
                        <div class="row txt-white26 right-txt-width">
                            <div class="col-6 logIn-lineHeight">Points</div>
                            <div class="col-6 logIn-lineHeight">Miles</div>
                        </div>
                    </div>

                    <div class="rL-float justify-middle-contents">
                        <div class="txt-gray26">Emirate Airways</div>
                        <div class="row txt-gray26 right-txt-width">
                            <div class="col-6 table-vertical-line">5464</div>
                            <div class="col-6 table-vertical-line">100</div>
                        </div>
                    </div>

                    <div class="rL-float justify-middle-contents">
                        <div class="txt-gray26">British Airways</div>
                        <div class="row txt-gray26 right-txt-width">
                            <div class="col-6 table-vertical-line">4654</div>
                            <div class="col-6 table-vertical-line">250</div>
                        </div>
                    </div>

                    <div class="table-hr"><hr /></div>

                    <div class="rL-float justify-middle-contents">
                        <div></div>
                        <div class="row right-txt-tfWidth">
                            <div class="col-4 txt-purple26 table-txt-center">Total</div>
                            <div class="col-4 txt-black26 table-txt-center">10118</div>
                            <div class="col-4 txt-black26 table-txt-center">350</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rL-margin gift-card-bottom">
        <div class="balence-card">
            <div class="rL-float card-RL justify-middle-contents">
                <div class="txt-black32">Gift Card</div>
            </div>

            <div class="card-RL">
                <div class="gift-card">
                    <div class="rL-float txt-gray26 justify-middle-contents">
                        <div>Option1</div>
                        <div>220</div>
                    </div>

                    <div class="rL-float txt-gray26 justify-middle-contents">
                        <div>Option2</div>
                        <div>235</div>
                    </div>

                    <div class="rL-float txt-gray26 justify-middle-contents">
                        <div>Option3</div>
                        <div>250</div>
                    </div>
                </div>
            </div>

            <form class="card-RL rL-float-table justify-middle-contents logIn-lineHeight" action="index.php">
                <label class="checkboxContainer checkboxContainerGray txt-top">
                    <a class="txt-gray18 txt-bold">I agree to terms and conditions</a>
                    <input type="checkbox"  id="vehicle1" name="vehicle1" value="Bike">
                    <span class="checkMark"></span>
                </label>
                <input type="submit" class="submit-btn justify-middle-contents" value="Submit">
            </form>
        </div>
    </section>

    <?php
        require('footer.php');
    ?>
</div>

<script>
$(document).ready(function() {
    $('#remembered').click(function () {
        localStorage.setItem("rememberMe_login", true);
    });

    $('#logout').click(function () {
        localStorage.setItem("rememberMe_login", false);
    });
});

    /**
     * Arrow replacing
     */
    $('.collections img').mouseenter(function () {
        $(this).attr('src', 'assets/images/home/downBtnRed.png')
    });

    $('.collections img').mouseleave(function () {
        $(this).attr('src', 'assets/images/home/downBtnBlack.png')
    });

    $('.collections span').mouseenter(function () {
        $('.collections img').attr('src', 'assets/images/home/downBtnRed.png')
    });

    $('.collections span').mouseleave(function () {
        $('.collections img').attr('src', 'assets/images/home/downBtnBlack.png')
    });
</script>
</body>
</html>