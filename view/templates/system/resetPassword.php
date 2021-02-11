<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <form action="/resetPw/<?php echo $userId;?>" method="POST">
    <input type="password" name="pw1" id="" placeholder="New Password">
    <input type="password" name="pw2" id="" placeholder="Confirm New Password">
    <input type="text" name="v" value="<?php echo $vLink;?>">
    <button type="submit"></button>
    </form>
</body>
</html>