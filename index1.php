<?php
echo '<pre>';
// print_r($_SERVER);
print_r($_POST);
echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <div class="container border">
        <div class="row">
            <div class="col">
                <h1>This is a Registration form</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return CheckFormValidation()" name="registrationform" enctype="">
                    <div class="form-group">
                        <label for="">User Name</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Your name please" aria-describedby="helpId" required>
                        <small id="helpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <label for="useremail">Email id</label>
                        <input type="email" name="useremail" id="useremail" class="form-control" placeholder="Your valid email address" aria-describedby="helpId" required>
                        <small id="helpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <label for="userpassword">Password</label>
                        <input type="password" name="userpassword" id="userpassword" class="form-control" placeholder="Must be minimal 6 Character long & max 10" aria-describedby="helpId" maxlength="10" minlength="6" required>
                        <small id="helpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Confirm Password</label>
                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" placeholder="Confirm your password" aria-describedby="confirmPasswordhelpId" required>
                        <small id="confirmPasswordhelpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="submitform">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        console.log('Hello world');
        let CheckFormValidation = function() {
            console.log('form is Submitted');
            let passValue = document.getElementById('userpassword').value;
            console.log(passValue);
            let confirmPassValue = document.getElementById('confirmpassword').value;
            console.log(confirmPassValue);
            if (passValue !== confirmPassValue) {
                console.log('Password Not match!');
                let errorPass = document.getElementById('confirmPasswordhelpId');
                errorPass.innerHTML = 'Password not matched!';
                errorPass.classList.remove('text-muted');
                errorPass.classList.add('text-danger');
                return false;
            }
            return true;
        }
    </script>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>