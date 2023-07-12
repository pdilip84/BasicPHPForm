<?php
echo '<pre>';
// print_r($_SERVER);

/* Declare database variables */
$dbhostname = 'localhost';
$dbusername = 'root';
$dbdatabase = 'githubform';
$dbpass = '';

/*Connect with database using PDO class */
$dsn = "mysql:hostname=" . $dbhostname . ";dbname=" . $dbdatabase;
$pdo = new pdo($dsn, $dbusername, $dbpass);

/* Define some global variables to display messages on screen */
$successMsg = '';
$errorAry = array();

/* If request for edit user */
if (isset($_REQUEST['edituser']) && !empty($_REQUEST['edituser'])) {
    $edituser =  $_REQUEST['edituser'];
    // echo $edituser;
    $userData = editUser($edituser, $pdo);
    $edituid = $userData[0];
    $edituname = $userData[1];
    $edituemail = $userData[2];
    // echo $edituid;
    // echo $edituname;
    // echo $edituemail;
}

/* If request for delete user */
if (isset($_REQUEST['deleteuser']) && !empty($_REQUEST['deleteuser'])) {
    $deleteuser =  $_REQUEST['deleteuser'];
    // echo $edituser;
    $successMsg = deleteUser($deleteuser, $pdo);
}

/* We are creating function here to get all data because we need to run this function on page load as well as when we submit the new data or delete the data. At that time we wil call it again to see quick data*/
function showAllData($pdo)
{
    return $resultall = $pdo->query("SELECT * FROM users");
}
$resultall = showAllData($pdo);
// var_dump($result);
// echo $resultall->rowCount();
$resultall->setFetchMode(PDO::FETCH_ASSOC);

function editUser($id, $pdo)
{
    return $userData = selectSingleUser($id, $pdo);
}

function selectSingleUser($id, $pdo)
{
    // SELECT * FROM `users` WHERE `id` = 50
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    foreach ($result = $stmt->fetchall(PDO::FETCH_ASSOC) as $key => $value) {
        // print_r($value);
        $editUid = $value['id'];
        $editUname = $value['username'];
        $editUemail = $value['useremail'];
        $editUpassword = $value['userpass'];
        return array($editUid, $editUname, $editUemail, $editUpassword);
    }
}

// function updateform($editUid, $editUemail, $editUpassword)
// {
//     $updateForm = 1;
//     // echo $editUemail;
//     // echo $editUid;
//     // echo $editUpassword;
// }
function deleteUser($id, $pdo)
{

    // "DELETE FROM users WHERE `users`.`id` = 27"
    $stmt = $pdo->prepare("DELETE FROM users WHERE `users`.`id` = :id");
    // $pdo->bindValue(':id', $id);
    $stmt->bindValue(':id', $id);
    $result = $stmt->execute();
    if (isset($result) && $result > 0) {
        return 'Data Deleted successfully';
    }
}

/* Insert new user */
function insertNewUser($pdo, $username, $useremail, $userpassword)
{
    // $stmt = $pdo->query("INSERT INTO `users` (`id`, `username`, `useremail`, `userpass`, `inserdata`) VALUES (NULL, 'pdilip84', 'demo@demo.com', 'superman', '2023-07-07 17:02:27')");

    /* prepare, bind & execute OR you can direcly do $pdo->query  but that is not safe */

    $stmt = $pdo->prepare("INSERT INTO `users` (`id`, `username`, `useremail`, `userpass`, `inserdata`) VALUES (NULL, :username, :useremail, :userpass, now())");
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':useremail', $useremail);
    $stmt->bindValue(':userpass', $userpassword);

    $result = $stmt->execute();
    if (isset($result)) {
        $successMsg = 'Data inserted successfully';
    } else {
        $successMsg = 'Data is not inserted, Try again!';
    }
    return $successMsg;
}

function updateUser($pdo, $userid, $username, $useremail, $userpassword)
{
    // echo 'updating user now' . $userid;
    // UPDATE `users` SET `username` = 'DilipDone11', `useremail` = 'demo@demo.in11', `userpass` = '$2y$10$Z2r3QL3vpllTz1uQI90KZuUrEVAdv.EsYc5OHuw1QWPajbVagxCLS22211' WHERE `users`.`id` = 70;
    $stmt = $pdo->prepare("UPDATE `users` SET `id` = :id, `username` = :username, `useremail` = :useremail, `userpass` = :userpass, `inserdata` = now() WHERE `users`.`id` = :id;");
    $stmt->bindValue(':id', $userid);
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':useremail', $useremail);
    $stmt->bindValue(':userpass', $userpassword);
    $editresult = $stmt->execute();
    if (isset($editresult)) {
        $successMsg = 'Data Updated successfully';
        return $successMsg;
    }
}

/* When form is submitting */
if (isset($_POST['submitformbtn'])) {
    /* Define error array as empty array */

    // print_r($_POST);

    /* Check if someone skip front end js validation and reach to server, just check empty values and throw back the errors in array */
    if (isset($_POST['username']) && empty($_POST['username'])) {
        array_push($errorAry, 'Name must be provided');
        $errorUsername = 'Name must be provided';
    }
    if (isset($_POST['useremail']) && empty($_POST['useremail'])) {
        array_push($errorAry, 'Email must be provided');
    }
    if (isset($_POST['userpassword']) && empty($_POST['userpassword'])) {
        array_push($errorAry, 'Password must be provided');
    }
    if ($_POST['userpassword'] !== $_POST['confirmpassword']) {
        array_push($errorAry, 'Password does not match');
    }

    // print_r($errorAry);
    // echo count($errorAry);

    /* If error array has values, means server side validation fail */
    if (count($errorAry) > 0) {
        /* Convert array to string to display in front form */
        $errorStr = '';
        foreach ($errorAry as $key => $value) {
            $errorStr = $errorStr . $value . "<br>";
        }
        // echo $errorStr;
    }
    /* Server side valiation passes */ else {
        /* sanatize variable */
        $username = filter_var(htmlspecialchars(trim($_POST['username'])), FILTER_SANITIZE_SPECIAL_CHARS);

        $useremail = filter_var(htmlspecialchars(trim($_POST['useremail'])), FILTER_SANITIZE_EMAIL);

        $userpassword = filter_var(htmlspecialchars(trim($_POST['userpassword'])), FILTER_SANITIZE_SPECIAL_CHARS);

        // echo $username;
        // echo $useremail;

        /* Do password Hashing */
        $userpassword = password_hash($userpassword, PASSWORD_DEFAULT);

        // echo $userpassword;
        // echo password_verify($userpassword, '$2y$10$J9YPmAqYp.7yqfGIEhhEtuKDEEEyH01ARWoB0yScLEM6eAmc11OhW');

        if (isset($_REQUEST['edituser'])) {
            $successMsg = updateUser($pdo, $_REQUEST['edituser'], $username, $useremail, $userpassword);
        } else {
            $successMsg = insertNewUser($pdo, $username, $useremail, $userpassword);
        }
    }
    /* After adding new data , we need to refresh the data table */
    $resultall = showAllData($pdo);
}

echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form-dilip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <div class="container border">
        <div class="row">
            <div class="col">
                <h1><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Registration form</a></h1>
                <h3 class="text-danger">
                    <?php

                    if (isset($successMsg) && !empty($successMsg)) {
                        echo $successMsg;
                    }
                    if (isset($errorStr) && strlen($errorStr) > 0) {
                        echo $errorStr;
                    }
                    ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"
                    onsubmit="return CheckFormValidation()" name="registrationform" enctype="">
                    <div class="form-group">
                        <label for="">User Name</label>
                        <input type="text" name="username" id="username" value="<?php if (isset($edituname)) {
                                                                                    echo $edituname;
                                                                                } ?>" class="form-control"
                            placeholder="Your name please" aria-describedby="helpId" required>
                        <small id="helpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <label for="useremail">Email id</label>
                        <input type="email" name="useremail" id="useremail" value="<?php if (isset($edituemail)) {
                                                                                        echo $edituemail;
                                                                                    } ?>" class="form-control"
                            placeholder="Your valid email address" aria-describedby="helpId" required>
                        <small id="helpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <label for="userpassword">Password</label>
                        <input type="password" name="userpassword" id="userpassword" class="form-control"
                            placeholder="Must be minimal 6 Character long & max 10" aria-describedby="helpId"
                            maxlength="10" minlength="6" required>
                        <small id="helpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Confirm Password</label>
                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control"
                            placeholder="Confirm your password" aria-describedby="confirmPasswordhelpId" maxlength="10"
                            minlength="6" required>
                        <small id="confirmPasswordhelpId" class="text-muted">Help text</small>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="submitformbtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <?php if (isset($resultall) && ($resultall->rowCount()) > 0) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($resultall->fetchAll() as $key => $value) {
                            echo "<tr>";
                            echo "<td scope='row'>" . $value['username'] . "</td>";
                            echo "<td>" . $value['useremail'] . "</td>";
                            echo "<td>" . $value['userpass'] . "</td>";
                            echo "<td><a href='" . $_SERVER['PHP_SELF'] . "?edituser=" . $value['id'] . "'>Edit</a></td>";
                            echo "<td><a href='?deleteuser=" . $value['id'] . "'>Delete</a></td>";
                            echo "</tr>";
                        }
                        ?>
                </tbody>
            </table>
            <?php } ?>
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


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>