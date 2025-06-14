<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ditso Habari - User Login & Signup</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #FFD700;
        font-family: 'Poppins', sans-serif;
      }
      .login_wrapper {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        position: relative;
      }
      .login_form, .registration_form {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        position: absolute;
        width: 100%;
        transition: all 0.5s ease;
      }
      .login_content h1 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 30px;
        color: #333;
      }
      .form-control {
        height: 45px;
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px 15px;
        margin-bottom: 15px;
        font-size: 14px;
      }
      .form-control:focus {
        border-color: #FFD700;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
      }
      .btn-default {
        background: #FFD700;
        color: #333;
        border: none;
        height: 45px;
        border-radius: 8px;
        font-weight: 500;
        width: 100%;
        margin-bottom: 15px;
        transition: all 0.3s ease;
      }
      .btn-default:hover {
        background: #FFC800;
        transform: translateY(-2px);
      }
      .reset_pass, .to_register {
        color: #666;
        font-size: 14px;
        text-decoration: none;
      }
      .reset_pass:hover, .to_register:hover {
        color: #333;
        text-decoration: none;
      }
      .separator {
        margin-top: 30px;
        text-align: center;
      }
      .separator h1 {
        font-size: 20px;
        margin-bottom: 10px;
      }
      .separator p {
        font-size: 12px;
        color: #666;
      }
      .image img {
        border-radius: 50%;
        margin-right: 10px;
      }
      .alert {
        margin-bottom: 20px;
        border-radius: 8px;
      }
      .registration_form {
        opacity: 0;
        visibility: hidden;
        transform: translateX(100%);
      }
      .login_form {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
      }
      .form-active {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
      }
      .form-inactive {
        opacity: 0;
        visibility: hidden;
        transform: translateX(-100%);
      }
    </style>
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="login_form">
          <section class="login_content">
            <?php
            include 'php/config.php';
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <form action="php/register.php" method="post">
              <h1>Login Form</h1>
              <div>
                <input name="uname" type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input name="pwd" type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1">
                <label class="form-check-label" for="is_admin">Login as Admin</label>
              </div>
              <div>
                <button name="login" class="btn btn-default submit">Login</button>
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <a href="javascript:void(0)" onclick="switchForm('register')" class="to_register"> Create Account </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="image"><img src="./img/ditso.jpg" alt="image" height="30px" width="30px"></i> Ditso Habari</h1>
                  <p>©2024 All Rights Reserved. Ditso Habari. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div class="registration_form">
          <section class="login_content">
            <form action="php/register.php" method="post">
              <h1>Create Account</h1>
              <div>
                <input name="uname" type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input name="email" type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input name="pwd" type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <button class="btn btn-default submit" name="submit">Submit</button>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member?
                  <a href="javascript:void(0)" onclick="switchForm('login')" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />
                <div>
                  <h1><i class="image"><img src="./img/ditso.jpg" alt="image" height="30px" width="30px"></i> Ditso Habari</h1>
                  <p>©2024 All Rights Reserved. Ditso Habari. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    <script>
      function switchForm(formType) {
        const loginForm = document.querySelector('.login_form');
        const registerForm = document.querySelector('.registration_form');
        
        if (formType === 'register') {
          loginForm.classList.remove('form-active');
          loginForm.classList.add('form-inactive');
          registerForm.classList.remove('form-inactive');
          registerForm.classList.add('form-active');
        } else {
          registerForm.classList.remove('form-active');
          registerForm.classList.add('form-inactive');
          loginForm.classList.remove('form-inactive');
          loginForm.classList.add('form-active');
        }
      }

      // Check if URL has #signup hash
      if (window.location.hash === '#signup') {
        switchForm('register');
      }
    </script>
  </body>
</html>
