<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--Bootstrap 5 CSS CDN-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!--Website Logo-->
    <link rel="icon" href="/assets/img/ccs-logo.png">
    <!--Boxicons-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <title>CCS: E-LOG | Login</title>
    <!-- Custom styles for this template -->
    <link href="/assets/css/index.css" rel="stylesheet">
</head>
<style>
    body{
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
    }
</style>
<body>

<div class="container-fluid d-flex justify-content-center align-items-center vh-100" style="background: rgb(236,161,107);
    background: radial-gradient(circle, rgba(236,161,107,1) 0%, rgba(249,106,3,1) 50%, rgba(236,161,107,1) 100%);">
        <div class="shadow-sm p-3 bg-body rounded w-100 w-sm-100 w-md-75 w-lg-50">
            <main class="form-signin">
                <form autocomplete="off" action="./login.php" method="post">
                    <img class="w-100" src="/assets/img/ccs-header-textonly.png" style="margin-bottom: -50px;">
                    <img class="w-100" src="/assets/img/ccs-elog-logo.png" style="margin-bottom: -50px;">
            
                        <div class="alert alert-secondary mt-4 pb-1" style="background-color: #ff964b;">
                            <h6 class="text-center">
                                <b class="text-light">WELCOME</b>
                            </h6>
                        </div>
        
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Username">
                            <label for="floatingInput">Username</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
                            <label for="floatingPassword">Password</label>
                        </div>

                        <div class="float-start mb-3">
                            <a href="./forgot.php" style="text-decoration: none; color: #f96b06;">Forgot password?</a>
                        </div>
            
                        <button class="w-100 btn text-light rounded-pill fw-bold" type="submit" name="login" style="background-color: #F96A03;">LOGIN</button>
                        <p class="mt-5  text-muted text-center">&copy; 2023 College of Computing Studies: Electronic Logbook System</p>
                </form>
        </main>
    </div>
</div>
    
</body>
</html>