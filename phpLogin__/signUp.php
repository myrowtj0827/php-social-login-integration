<!-- Sign Up page -->
<?php
// Include config file
require_once "config.php";
 
/**
 * 
 * 
 * Customer Sign Up
 * 
 */
// Define variables and initialize with empty values
$firstName = $lastName = $email = $password = $confirm_password = $captcha = "";
$firstName_err = $lastName_err = $email_err = $password_err = $confirm_password_err = $code_err = "";

if (($_SERVER["REQUEST_METHOD"] == "POST")){
   // Validate firstName
    if(empty(trim($_POST["firstName"]))){
        $firstName_err = "Please enter a firstName.";
    } 
    
    if(empty(trim($_POST["lastName"]))){
        $lastName_err = "Please enter a lastName.";
    }

    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";
    }

    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";
    }
    
    if(!empty(trim($_POST["email"]))) {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                //echo mysqli_stmt_num_rows($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $firstName = trim($_POST["firstName"]);
                    $lastName = trim($_POST["lastName"]);
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later. 43";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    //Validate Security Code
    if(empty(trim($_POST['captcha']))){
        $code_err = "Please enter security code.";
    } else{
        $captcha = $_POST["captcha"];
    }
    
    // Check input errors before inserting in database
    if(empty($firstName_err) && empty($lastName_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($code_err) && (strcasecmp($_SESSION['captcha'], $captcha) == 0)){
                
        // Prepare an insert statement
        $sql = "INSERT INTO users (firstName, lastName, email, password) VALUES (?, ?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_firstName, $param_lastName, $param_email, $param_password);
            
            // Set parameters
            $param_firstName = $firstName;
            $param_lastName = $lastName;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page                
                header("location: emailVerification.php");
            } else{
                echo mysqli_error($link);
                echo "Something went wrong. Please try again later. 108";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
        // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
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
        let agreed = false;
    </script>
</head>
<body>

<div class="margin-center-width homeHeader">
    <?php
        require('header.php');
    ?>

    <div class="header-hr"><hr /></div>
    <section class="signUp-Bg">
        <form id="signup-form" class="signUp-card" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
               
            <div class="txt-purple38 text-align-center sign-addPadding">Sign Up</div>
            <div class="signUp-grid">

                <div class="form-group <?php echo (!empty($firstName_err)) ? 'has-error' : ''; ?>">
                    <div class="txt-black22">First Name</div>
                    <div class="signUp-input">
                        <input type="text" name="firstName" class="form-control user-bg" value="<?php echo $firstName; ?>" placeholder="Rohan" required="required"/>
                    </div>
                    <span class="help-block"><?php echo $firstName_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($lastName_err)) ? 'has-error' : ''; ?>">
                    <div class="txt-black22">Last Name</div>
                    <div class="signUp-input">
                        <input type="text" name="lastName" class="form-control user-bg" value="<?php echo $lastName; ?>" placeholder="Patel" required="required"/>
                    </div>
                    <span class="help-block"><?php echo $lastName_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <div class="txt-black22">Password</div>
                    <div class="signUp-input">
                        <input type="password" name="password" class="form-control psw-bg" value="<?php echo $password;?>" placeholder="Password" required="required"/>
                    </div>
                    <span class="help-block"><?php echo $password_err; ?></span>

                    <div class="strong-red">20% Strong</div>
                </div>

                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <div class="txt-black22">Confirm Password</div>
                    <div class="signUp-input">
                        <input type="password" name="confirm_password" class="form-control psw-bg" value="<?php echo $confirm_password; ?>" placeholder="Confirm Password" required="required"/>
                        <span class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                </div>
            </div>

            <div class="signUp-email-grid email-input-width">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <div class="txt-black22">Email</div>
                    <div class="signUp-input">
                        <input type="email" name="email" class="form-control mail-bg" value="<?php echo $email; ?>" placeholder="Rohanpatel0125@gmail.com" required="required"/>
                    </div>
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>

                <center><img src="captcha.php" /></center>                

                <div class="form-group agree-top <?php echo (!empty($code_err)) ? 'has-error' : ''; ?>" required="required">
                    <div class="txt-black22">Security Code</div>

                    <div class="signUp-input">
                        <input type="text" class="form-control psw-bg" name="captcha" placeholder="325220" required="required"/>
                    </div>
                    <span class="help-block"><?php echo $code_err; ?></span>
                </div>
            </div>

            <label class="checkboxContainer">
                <a class="txt-gray18">I agree to terms and conditions</a>
                <input type="checkbox" id="vehicle1" name="termsCheck" value="Bike" onclick="checkAddress(this)">
                <span class="checkMark"></span>
            </label>
            <div class="joinNow-btn justify-middle-contents" onclick="replaceType(this)">Join Now</div>
        </form>
    </section>

    <?php
        require('footer.php');
    ?>
</div>

<script>
    $(document).ready(function() {

    //change CAPTCHA on each click or on refreshing page
        $("#reload").click(function() {
            $("img#img").remove();
            var id = Math.random();
            $('<img id="img" src="captcha.php?id='+id+'"/>').appendTo("#imgdiv");
            id ='';
        });

        //validation function
        $('#button').click(function() {
            var name = $("#username1").val();
            var email = $("#email1").val();
            var captcha = $("#captcha1").val();

            if (name == '' || email == '' || captcha == '')
            {
                alert("Fill All Fields");
            } else { //validating CAPTCHA with user input text
                var dataString = 'captcha=' + captcha;
                
                $.ajax({
                    type: "POST",
                    url: "verify.php",
                    data: dataString,
                    success: function(html) {
                        // alert(html);
                    }
                });
            }
        });
    });

    function checkAddress(checkbox)
    {
        agreed = checkbox.checked;
    }

    function replaceType(button)
    {
        if (agreed)
        {
            document.getElementById("signup-form").submit();
        }
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