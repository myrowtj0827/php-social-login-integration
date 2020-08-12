<?php
// Initialize the session
//session_start();

$login_button = "";

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Include config file
require_once "config.php";

/**
 * 
 * 
 * Customer Log In 
 * 
 */

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    if(isset($_POST['remember'])) {
        $_SESSION['remember'] = $_POST['remember'];
    }

    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, email, password FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;

                            //$_SESSION['remember_me'] = true;
                            //echo '<script> localStorage.setItem("rememberMe_login", true); </script>';

                            // Redirect user to welcome page
                            header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if email doesn't exist
                    $email_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }    
    // Close connection
    mysqli_close($link);
}

/**
 * Google Login Link
 */
    $_SESSION['from'] = 'login';

    $google_client->setRedirectUri('http://localhost/phpLogin/index.php');
    $login_button = '<a style="text-decoration: none !important;" href="'.$google_client->createAuthUrl().'"><div class="google-btn signUp-red-txt txt-bold justify-middle-contents"><img class="google-facebook" src="assets/images/home/google.png" alt="" /> Continue with Google</div></a>';

/**
 * Facebook Login Link
 */
    $facebook_permissions = ['email']; // Optional permissions        
    $facebook_login_button = $facebook_helper->getLoginUrl('http://localhost/phpLogin/', $facebook_permissions);
    $facebook_login_button ='<a style="text-decoration: none !important;" href="'.$facebook_login_button.'"><div class="facebook-btn signUp-green-txt txt-bold justify-middle-contents"><img class="google-facebook" src="assets/images/home/facebook-sign.png" alt="" />Continue with Facebook</div></a>';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log In</title>
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

    <section id="logInModal" class="logIn-Bg">
        <div class="logIn-card">
            <div class="txt-rLPadding">
                <div class="txt-purple38 logIn-lineHeight">Login</div>

                <div class="txt-black24 logIn-lineHeight">XX Hala bonus when you sign up today!</div>

                <form id="logIn" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?> sign-addPadding">
                        <div class="txt-black22">Email</div>
                        <div class="signUp-input">
                            <input type="email" name="email" class="form-control mail-bg" placeholder="Rohanpatel0125@gmail.com" />
                        </div>

                        <span class="help-block"><?php echo $email_err; ?></span>
                    </div>
                    

                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?> agree-top">
                        <div class="txt-black22">Password</div>

                        <div class="signUp-input">
                            <input type="password" class="form-control psw-bg" name="password" placeholder="Password" />
                        </div>
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>


                    <div class="logIn-lineHeight">
                        <label class="checkboxContainer txt-top">
                            <a class="txt-gray18 txt-bold">Remember me</a>
                            <input type="checkbox" name="remember" onclick="checkbox(this)">
                            <span class="checkMark"></span>
                        </label>

                        <div class="joinNow-btn justify-middle-contents remember-login" onclick="login()">Login</div>
                    </div>
                </form>

                <div class="rL-float hr-padding">
                    <div class="or-left"><hr /></div>

                    <div class="signUp-gray-txt txt-bold">OR</div>

                    <div class="or-right"><hr /></div>
                </div>

                <div class="login-btn-margin">

                    <?php
                        echo '<div align="center">'.$login_button . '</div>';
                        echo '<div align="center">'.$facebook_login_button . '</div>';
                    ?>                   
                    
                </div>
            </div>

            <div class="terms-hr"><hr /></div>

            <div class="txt-gray18 txt-bold logIn-lineHeight">All right reserved ...</div>
            <div class="txt-blue18 logIn-lineHeight">Terms and Privacy</div>
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

    function login() {
        document.getElementById("logIn").submit();
    }
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