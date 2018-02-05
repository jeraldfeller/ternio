<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Register</title>
	
	<link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/x-icon" href="" />
    <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
    <style>
        .margin-top-24{
            margin-top: 24px;
        }
        .margin-top-12{
            margin-top: 12px;
        }
        .margin-bottom-12{
            margin-bottom: 12px;
        }
        .margin-bottom-24{
            margin-bottom: 24px;
        }
        .display-none{
            display: none;
        }

    </style>

</head>
<body>

<div class="container-fluid formfield-wrap">
    <div class="row align-items-center formfield-margin">
        <div class="col-md-12">
            <div class="col-md-6 col-md-offset-3 panel panel-default">
	            <div class="formlogo"></div>
                <h4 class="text-center formtitletag">REGISTER</h4>
                <div class="registerprobmessage">If you do not receive an email within 30 minutes, please first check your spam folder and then email support@ternio.io</div>
                <div><h5 class="warning-box"></h5></div>
                <div class="register-container">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" class="form-control" id="firstName">
                    </div>
                    <div class="form-group">
                        <label for="firstName">Last Name *</label>
                        <input type="text" class="form-control" id="lastName">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" class="form-control" id="password" placeholder="Password">
                    </div>
                    <div class="col-md-12 text-center margin-bottom-12">
                        <button class="btn btn-default submit-btn">SUBMIT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="assets/ajax/app.js"></script>

<script>
    $(document).ready(function(){
        $('.submit-btn').on('click', function(){
            $firstName = $('#firstName').val();
            $lastName = $('#lastName').val();
            $email = $('#email').val();
            $password = $('#password').val();
            if($firstName != '' && $lastName != '' && $email != '' && $password != ''){
                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                $data = {
                    firstName: $firstName,
                    lastName: $lastName,
                    email: $email,
                    password: $password
                };
                $.ajax({
                    url: 'Controller/user.php?action=register',
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if(data.success == true){
                            $('.warning-box').html('');
                            $('.register-container').addClass('text-center');
                            $('.register-container').html(data.response.message);
                        }else{
                            console.log(data);
                            $('.warning-box').html(data.response.message);
                            $('.submit-btn').html('SUBMIT');
                        }
                    },
                    data: {param: JSON.stringify($data)}
                });
            }else{
                alert('All fields are required!');
            }
        });
    });
</script>
</body>
</html>
