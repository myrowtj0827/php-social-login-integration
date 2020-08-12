<!-- index.php -->
<?php
    //Include Configuration File
    include('config.php');

    $signup_button = '';
    $_SESSION['access_token'] = "";

    //This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
    if(isset($_GET["code"]) && !isset($_GET["state"]))
    {

        //It will Attempt to exchange a code for an valid authentication token.
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
        
        //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
        if(!isset($token['error']))
        {
            //Set the access token used for requests
            $google_client->setAccessToken($token['access_token']);
            
            //Store "access_token" value in $_SESSION variable for future use.
            $_SESSION['access_token'] = $token['access_token'];
            
            //Create Object of Google Service OAuth 2 class
            $google_service = new Google_Service_Oauth2($google_client);
            
            //Get user profile data from google
            $data = $google_service->userinfo->get();
            
            //Below you can find Get profile data and store into $_SESSION variable
            
            if(!empty($data['given_name']))
            {
                $_SESSION['user_first_name'] = $data['given_name'];
            }

            if(!empty($data['family_name']))
            {
                $_SESSION['user_last_name'] = $data['family_name'];
            }

            if(!empty($data['email']))
            {
                $_SESSION['user_email_address'] = $data['email'];
            }

            if(!empty($data['gender']))
            {
                $_SESSION['user_gender'] = $data['gender'];
            }

            if(!empty($data['picture']))
            {
                $_SESSION['user_image'] = $data['picture'];
            }
        }

        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE firstName = ? and lastName = ? and email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_firstName, $param_lastName, $param_email);
            
            // Set parameters
            $param_firstName = $_SESSION['user_first_name'];
            $param_lastName = $_SESSION['user_last_name'];
            $param_email = $_SESSION['user_email_address'];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

            // Testing the google account registration state
                if(mysqli_stmt_num_rows($stmt) == 1){

                    if ($_SESSION['from'] === 'login') {
                        $_SESSION['from'] === 'index';
                        $_SESSION["loggedin"] = true;                       
                                               
                        header('location: index.php');
                    } else {

                        $_SESSION['registration'] = "g_exsist";
                        header("location: emailVerification.php");
                    }                      
                  
                } else{
                  
                    // Prepare an insert statement
                    $sql_add = "INSERT INTO users (firstName, lastName, email) VALUES (?, ?, ?)";

                    if($stmt_add = mysqli_prepare($link, $sql_add)){
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt_add, "sss", $param_first, $param_last, $param_email_address);
                        
                        // Set parameters
                        $param_first = $_SESSION['user_first_name'];
                        $param_last = $_SESSION['user_last_name'];
                        $param_email_address = $_SESSION['user_email_address'];
                        
                        // Attempt to execute the prepared statement
                        if(mysqli_stmt_execute($stmt_add)){
                            // Redirect to login page      
                             $_SESSION['registration'] = "g_register";          
                            header("location: emailVerification.php");
                        } else{
                            echo mysqli_error($link);
                            echo "Something went wrong. Please try again later. 108";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt_add);
                    }                  
                }
            } else{
                echo "Oops! Something went wrong. Please try again later. 43";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }       
    }
    
    /**
     * Google Sign Up link
     */
    //This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
    if(!isset($_SESSION['access_token']))
    {
    //Create a URL to obtain user authorization
        $signup_button = '<a style="text-decoration: none !important;" href="'.$google_client->createAuthUrl().'"><div class="google-btn signUp-red-txt txt-bold justify-middle-contents"><img class="google-facebook" src="assets/images/home/google.png" alt="" /> Continue with Google</div></a>';
    } else {
        $signup_button = '<a style="text-decoration: none !important;" href="'.$google_client->createAuthUrl().'"><div class="google-btn signUp-red-txt txt-bold justify-middle-contents"><img class="google-facebook" src="assets/images/home/google.png" alt="" /> Continue with Google</div></a>';
    }
    
    // Close connection
    // mysqli_close($link);


    $facebook_output = '';
    if(isset($_GET['code']) && isset($_GET["state"])) {

        if(isset($_SESSION['access_token']) && $_SESSION['access_token'] !== ''){
            $access_token = $_SESSION['access_token'];
        } else {            
            $access_token = $facebook_helper->getAccessToken();
            $_SESSION['access_token'] = $access_token;            
            $facebook->setDefaultAccessToken($_SESSION['access_token']);
        }

        $_SESSION['user_name'] = '';
        $_SESSION['user_email_address'] = '';
    
        $graph_response = $facebook->get("/me?fields=name,email", $access_token);    
        $facebook_user_info = $graph_response->getGraphUser();
        
        if(!empty($facebook_user_info['name'])){            
            $_SESSION['user_name'] = $facebook_user_info['name'];    
        }
    
        if(!empty($facebook_user_info['email'])){
            $_SESSION['user_email_address'] = $facebook_user_info['email'];        
        }

        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE firstName = ? and email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_firstName, $param_email);
            
            // Set parameters
            $param_firstName = $_SESSION['user_name'];
            $param_email = $_SESSION['user_email_address'];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

            // Testing the google account registration state
                if(mysqli_stmt_num_rows($stmt) == 1){

                    if ($_SESSION['from'] === 'login') {
                        $_SESSION['from'] === 'index';
                        $_SESSION["loggedin"] = true;
                                               
                        header('location: index.php');
                    } else {
                        $_SESSION["registration"] = "fb_exsist";
                        header("location: emailVerification.php");
                    }                 
                } else{
                  
                    // Prepare an insert statement
                    $sql_add = "INSERT INTO users (firstName, email) VALUES (?, ?)";

                    if($stmt_add = mysqli_prepare($link, $sql_add)){
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt_add, "ss", $param_first, $param_email_address);
                        
                        // Set parameters
                        $param_first = $_SESSION['user_name'];
                        $param_email_address = $_SESSION['user_email_address'];
                        
                        // Attempt to execute the prepared statement
                        if(mysqli_stmt_execute($stmt_add)){
                            // Redirect to login page
                            echo '<script>alert('. mysqli_error($link) . ')</script>';
                            $_SESSION["registration"] = "fb_register";
                            header("location: emailVerification.php");
                        } else{
                            $_SESSION["registration"] = "fb_fail";
                            echo '<script>alert('. mysqli_error($link) . ')</script>';
                            echo "Something went wrong. Please try again later. 108";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt_add);
                    }                  
                }
            } else{
                echo "Oops! Something went wrong. Please try again later. 43";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

     // Get facebook url
     $facebook_permissions = ['email']; // Optional permissions        
     $facebook_login_url = $facebook_helper->getLoginUrl('http://localhost/phpLogin/index.php', $facebook_permissions);

     // Render Facebook login button
     $facebook_login_url ='<a style="text-decoration: none !important;" href="'.$facebook_login_url.'"><div class="facebook-btn signUp-green-txt txt-bold justify-middle-contents"><img class="google-facebook" src="assets/images/home/facebook-sign.png" alt="" />Continue with Facebook</div></a>';
?>

<html>
 <head>
    <title>HomePage</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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

    <script>
        let remember_me;
    </script>

 </head>
 <body>

<div class="margin-center-width homeHeader">

    <?php
        require('header.php');
    ?>

    <section class="backStores-Bg justify-homeContents">
        <div class="homeBg-desc">
            <div class="large-txt">Get up to 40% Cash Back at over 2,500 stores</div>
            <div class="middle-txt-top">LOREM IPSUM DOLOR SIT AMET, CONSETETUR SADIPSCING ELITR,</div>
            <div class="lower-txt">sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliguyam erat, sed diam voluptua.</div>
            <div class="showNow-btn boxShawDow justify-middle-contents">SHOP NOW</div>
        </div>

       <?php
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        ?>
            <div class=""> 
            </div>
        <?php
            } else {
        ?>
            <div class="signUp-modal justify-middle-nonFlex login-success">
            <div class="signUp-black-txt txt-bold">Sign Up Today and Get Bonus a $10*</div>

            <?php          
                echo '<div id="google_login" align="center">'.$signup_button . '</div>';
            ?> 
            <?PHP
                echo '<div id="facebook_login" align="center">'.$facebook_login_url . '</div>';
            ?>

            <div class="rL-float hr-padding">
                <div class="or-left"><hr /></div>

                <div class="signUp-gray-txt txt-bold">OR</div>

                <div class="or-right"><hr /></div>
            </div>

            <form action="signUp.php">
                <input type="email" class="form-control email-btn signUp-gray-txt txt-bold justify-middle-contents signUp-text-left" placeholder="Email" />
                <input type="password" class="form-control password-btn signUp-gray-txt txt-bold justify-middle-contents signUp-text-left" placeholder="Password" />
                <input type="submit" class="joinNow-btn justify-middle-contents" value="Join Now"/>
            </form>
        </div>
        <?php
            }
        ?>
    </section>

    <section class="rL-margin img4-section">
        <div class="grid1-width">
            <div class="img-grid4">
                <div class="grid-cart mouseCursor justify-middle-contents"><img class="iconSize" src="assets/images/home/img1.png" alt="" /> </div>
                <div class="grid-cart mouseCursor justify-middle-contents"><img class="iconSize" src="assets/images/home/img2.png" alt="" /> </div>
                <div class="grid-cart mouseCursor justify-middle-contents"><img class="iconSize" src="assets/images/home/img3.png" alt="" /> </div>
                <div class="grid-cart mouseCursor justify-middle-contents"><img class="iconSize" src="assets/images/home/img4.png" alt="" /> </div>
            </div>
        </div>
    </section>

    <section class="imgSlider-section">
        <div class="rL-margin">
            <div class="img-grid6">
                <div id="owlSliderImg" class="owl-carousel owl-theme">
                    <div class="grid-slider-cart-Popular mouseCursor">
                        <div class="purple-txt">Popular</div>
                    </div>

                    <div class="grid-slider-cart-Travel mouseCursor">
                        <div class="purple-txt">Travel</div>
                    </div>

                    <div class="grid-slider-cart-Dining mouseCursor">
                        <div class="purple-txt">Dining</div>
                    </div>

                    <div class="grid-slider-cart-Fashion mouseCursor">
                        <div class="purple-txt">Fashion</div>
                    </div>

                    <div class="grid-slider-cart-Electronics mouseCursor">
                        <div class="purple-txt">Electronics</div>
                    </div>

                    <div class="grid-slider-cart-Sports mouseCursor">
                        <div class="purple-txt">Sports & Outdoors</div>
                    </div>

                    <div class="grid-slider-cart-Popular mouseCursor">
                        <div class="purple-txt">Popular</div>
                    </div>

                    <div class="grid-slider-cart-Travel mouseCursor">
                        <div class="purple-txt">Travel</div>
                    </div>

                    <div class="grid-slider-cart-Dining mouseCursor">
                        <div class="purple-txt">Dining</div>
                    </div>

                    <div class="grid-slider-cart-Fashion mouseCursor">
                        <div class="purple-txt">Fashion</div>
                    </div>

                    <div class="grid-slider-cart-Electronics mouseCursor">
                        <div class="purple-txt">Electronics</div>
                    </div>

                    <div class="grid-slider-cart-Sports mouseCursor">
                        <div class="purple-txt">Sports & Outdoors</div>
                    </div>
                </div>
            </div>

            <div class="phone-shown">
                <div class="img-grid5">
                <div class="grid-cart5 justify-middle-nonFlex borderGray-bottom borderGray-right topLeft-grid-radius">
                    <div class="img-height"><img class="img-size1" src="assets/images/home/img11.png" alt="" /> </div>
                    <div class="purple-txt1">BUDGET</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-bottom borderGray-right">
                    <div class="img-height"><img class="img-size2 " src="assets/images/home/img12.png" alt="" /> </div>
                    <div class="purple-txt1">COBONE</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-bottom borderGray-right">
                    <div class="img-height"><img class="img-size2" src="assets/images/home/img13.png" alt="" /> </div>
                    <div class="purple-txt1">TRIP.COM</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-bottom borderGray-right">
                    <div class="img-height"><img class="img-size2" src="assets/images/home/img14.png" alt="" /> </div>
                    <div class="purple-txt1">BOOKING.COM</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-bottom topRight-grid-radius">
                    <div class="img-height"><img class="img-size2" src="assets/images/home/img15.png" alt="" /> </div>
                    <div class="purple-txt1">RADISSON</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-right bottomLeft-grid-radius">
                    <div class="img-height"><img class="img-size1" src="assets/images/home/img16.png" alt="" /> </div>
                    <div class="purple-txt1">GAP</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-right">
                    <div class="img-height"><img class="img-size1" src="assets/images/home/img17.png" alt="" /> </div>
                    <div class="purple-txt1">AIRARABIA</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-right">
                    <div class="img-height"><img class="img-size1" src="assets/images/home/img18.png" alt="" /> </div>
                    <div class="purple-txt1">BATH & BODY</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex borderGray-right">
                    <div class="img-height"><img class="img-size1" src="assets/images/home/img19.png" alt="" /> </div>
                    <div class="purple-txt1">WALMART</div>
                    <div class="black-txt1">5 points/$</div>
                </div>

                <div class="grid-cart5 justify-middle-nonFlex bottomRight-grid-radius">
                    <div class="img-height"><img class="img-size1" src="assets/images/home/img20.png" alt="" /> </div>
                    <div class="purple-txt1">IPSOS</div>
                    <div class="black-txt1">5 points/$</div>
                </div>
            </div></div>

            <div class="phone-hidden">
                <div class="img-grid5">
                    <div class="grid-cart5 justify-middle-nonFlex right-bottom topLeft-grid-radius">
                        <div class="img-height"><img class="img-size1" src="assets/images/home/img11.png" alt="" /> </div>
                        <div class="purple-txt1">BUDGET</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex bottomGray topRight-grid-radius">
                        <div class="img-height"><img class="img-size2 " src="assets/images/home/img12.png" alt="" /> </div>
                        <div class="purple-txt1">COBONE</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex right-bottom">
                        <div class="img-height"><img class="img-size2" src="assets/images/home/img13.png" alt="" /> </div>
                        <div class="purple-txt1">TRIP.COM</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex bottomGray">
                        <div class="img-height"><img class="img-size2" src="assets/images/home/img14.png" alt="" /> </div>
                        <div class="purple-txt1">BOOKING.COM</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex right-bottom">
                        <div class="img-height"><img class="img-size2" src="assets/images/home/img15.png" alt="" /> </div>
                        <div class="purple-txt1">RADISSON</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex bottomGray">
                        <div class="img-height"><img class="img-size1" src="assets/images/home/img16.png" alt="" /> </div>
                        <div class="purple-txt1">GAP</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex right-bottom">
                        <div class="img-height"><img class="img-size1" src="assets/images/home/img17.png" alt="" /> </div>
                        <div class="purple-txt1">AIRARABIA</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex bottomGray">
                        <div class="img-height"><img class="img-size1" src="assets/images/home/img18.png" alt="" /> </div>
                        <div class="purple-txt1">BATH & BODY</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex borderGray-right bottomLeft-grid-radius">
                        <div class="img-height"><img class="img-size1" src="assets/images/home/img19.png" alt="" /> </div>
                        <div class="purple-txt1">WALMART</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>

                    <div class="grid-cart5 justify-middle-nonFlex bottomRight-grid-radius">
                        <div class="img-height"><img class="img-size1" src="assets/images/home/img20.png" alt="" /> </div>
                        <div class="purple-txt1">IPSOS</div>
                        <div class="black-txt1">5 points/$</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="explore-section">
        <div class="rL-margin">
            <div class="black-large-txt">Explore More Store</div>

            <div class="img-grid3">
                <div class="grid-cart3">
                    <div class="text-meddium txt-bold">New Deals</div>
                    <div class="row padding-bottom">
                        <div class="col-4">
                            <div class="img-bg justify-middle-contents"><img class="img-size3" src="assets/images/home/img31.png" alt="" /></div>
                        </div>

                        <div class="col-8">
                            <div>
                                <div class="text-small txt-bold">Double Points</div>
                                <div class="text-tiny">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy</div>
                            </div>
                        </div>
                    </div>

                    <div class="row padding-bottom">
                        <div class="col-4">
                            <div class="img-bg justify-middle-contents"><img class="img-size3" src="assets/images/home/img32.png" alt="" /></div>
                        </div>

                        <div class="col-8">
                            <div>
                                <div class="text-small txt-bold">500 Points for referal</div>
                                <div class="text-tiny">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="img-bg justify-middle-contents"><img class="img-size3" src="assets/images/home/img33.png" alt="" /></div>
                        </div>

                        <div class="col-8">
                            <div>
                                <div class="text-small txt-bold">10% off site wide</div>
                                <div class="text-tiny">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid-slider-cart3">
                    <div class="bg-slider justify-middle-contents">
                        <div class=""><img src="assets/images/home/arrow-left.png" alt="" /> </div>
                        <div class=""><img class="max100-width" src="assets/images/home/img34.png" /></div>
                        <div class=""><img src="assets/images/home/arrow-right.png" alt="" /> </div>
                    </div>

                    <div class="get-padding">
                        <div class="rL-float">
                            <div class="text-meddium txt-bold">Get 40% off Beauty Byes</div>
                            <div class="text-tiny topTxt">2 Day Ago</div>
                        </div>
                        <div class="text-tiny">5% Caseback</div>
                    </div>
                </div>

                <div class="grid-cart3">
                    <div class="text-meddium txt-bold">Trending Now</div>

                    <div class="rL-float">
                        <div class="text-small">Lorem ipsum</div>
                        <select className="country-option text-small txt-bold">
                            <option class="text-small txt-bold" selected>253%</option>
                            <option class="text-small txt-bold">255%</option>
                            <option class="text-small txt-bold">254%</option>
                            <option class="text-small txt-bold">253%</option>
                            <option class="text-small txt-bold">252%</option>
                            <option class="text-small txt-bold">251%</option>
                            <option class="text-small txt-bold">250%</option>
                        </select>
                    </div>
                    <hr />

                    <div class="rL-float">
                        <div class="text-small">Lorem ipsum</div>
                        <select className="country-option text-small txt-bold">
                            <option class="text-small txt-bold" selected>253%</option>
                            <option class="text-small txt-bold">255%</option>
                            <option class="text-small txt-bold">254%</option>
                            <option class="text-small txt-bold">253%</option>
                            <option class="text-small txt-bold">252%</option>
                            <option class="text-small txt-bold">251%</option>
                            <option class="text-small txt-bold">250%</option>
                        </select>
                    </div>
                    <hr />

                    <div class="rL-float">
                        <div class="text-small">Lorem ipsum</div>
                        <select className="country-option text-small txt-bold">
                            <option class="text-small txt-bold" selected>253%</option>
                            <option class="text-small txt-bold">255%</option>
                            <option class="text-small txt-bold">254%</option>
                            <option class="text-small txt-bold">253%</option>
                            <option class="text-small txt-bold">252%</option>
                            <option class="text-small txt-bold">251%</option>
                            <option class="text-small txt-bold">250%</option>
                        </select>
                    </div>
                    <hr />

                    <div class="rL-float">
                        <div class="text-small">Lorem ipsum</div>
                        <select className="country-option text-small txt-bold">
                            <option class="text-small txt-bold" selected>253%</option>
                            <option class="text-small txt-bold">255%</option>
                            <option class="text-small txt-bold">254%</option>
                            <option class="text-small txt-bold">253%</option>
                            <option class="text-small txt-bold">252%</option>
                            <option class="text-small txt-bold">251%</option>
                            <option class="text-small txt-bold">250%</option>
                        </select>
                    </div>
                    <hr />

                    <div class="rL-float">
                        <div class="text-small">Lorem ipsum</div>
                        <select className="country-option text-small txt-bold">
                            <option class="text-small txt-bold" selected>253%</option>
                            <option class="text-small txt-bold">255%</option>
                            <option class="text-small txt-bold">254%</option>
                            <option class="text-small txt-bold">253%</option>
                            <option class="text-small txt-bold">252%</option>
                            <option class="text-small txt-bold">251%</option>
                            <option class="text-small txt-bold">250%</option>
                        </select>
                    </div>
                    <hr />

                    <div class="rL-float">
                        <div class="text-small">Lorem ipsum</div>
                        <select className="country-option text-small txt-bold">
                            <option class="text-small txt-bold" selected>253%</option>
                            <option class="text-small txt-bold">255%</option>
                            <option class="text-small txt-bold">254%</option>
                            <option class="text-small txt-bold">253%</option>
                            <option class="text-small txt-bold">252%</option>
                            <option class="text-small txt-bold">251%</option>
                            <option class="text-small txt-bold">250%</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="deals-section">
        <div class="rL-margin">
            <div class="black-large-txt">Top Deals</div>

            <div class="img-grid-shop">
                <div class="grid-cart-shop">
                    <div class="bg-grid4 justify-middle-contents">
                        <img src="assets/images/home/img41.png" alt="" />
                    </div>
                    <div class="text-small txt-bold text-align-center">12/08/20 22:58 PM</div>
                    <div class="text-tiny text-align-center">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et</div>
                    <div class="shopNow-btn justify-middle-contents">Shop Now</div>
                </div>

                <div class="grid-cart-shop">
                    <div class="bg-grid4 justify-middle-contents">
                        <img class="shoes-size" src="assets/images/home/img42.png" alt="" />
                    </div>
                    <div class="text-small txt-bold text-align-center">12/08/20 22:58 PM</div>
                    <div class="text-tiny text-align-center">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et</div>
                    <div class="shopNow-btn justify-middle-contents">Shop Now</div>
                </div>

                <div class="grid-cart-shop">
                    <div class="bg-grid4 justify-middle-contents">
                        <img src="assets/images/home/img43.png" alt="" />
                    </div>
                    <div class="text-small txt-bold text-align-center">12/08/20 22:58 PM</div>
                    <div class="text-tiny text-align-center">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et</div>
                    <div class="shopNow-btn justify-middle-contents">Shop Now</div>
                </div>

                <div class="grid-cart-shop">
                    <div class="bg-grid4 justify-middle-contents">
                        <img src="assets/images/home/img44.png" alt="" />
                    </div>
                    <div class="text-small txt-bold text-align-center">12/08/20 22:58 PM</div>
                    <div class="text-tiny text-align-center">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et</div>
                    <div class="shopNow-btn justify-middle-contents">Shop Now</div>
                </div>
            </div>
        </div>
    </section>

<!--  Footer  -->
    <footer class="flex-container footerBg justify-middle-contents">
        <div class="footer-padding rL-float-footer">
            <div class="followUs justify-middle-nonFlex">
                <div class="text-align-left follow-bottom">Follow Us</div>
                <div class="text-align-left">
                    <img class="social-padding" src="assets/images/home/twitter.png" alt="" />
                    <img class="social-padding" src="assets/images/home/facebook.png" alt="" />
                    <img class="social-padding" src="assets/images/home/youtube.png" alt="" />
                </div>
            </div>

            <div class="txt-rLPadding txt-bold justify-middle-nonFlex">
                <div>About Us | News | Terms & Conditions | Privacy Policy | Contact Us</div>
                <div class="year-top">
                    &copy;<script>document.write(new Date().getFullYear());</script> Halamiles. All rights reserved.
                </div>
            </div>

            <div class="">
                Halamiles.com
            </div>
        </div>
    </footer>
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

        remember_me = localStorage.getItem("rememberMe_login");

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