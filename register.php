<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/functions.php");

if (isset($_GET['reset'])) {
    reset_();
}

if ($_SESSION['auth_code_valid']) {
    $type = 'register';
    $title = 'Register';
    $button_text = 'Submit';
    $type = 'register';
    $content = ['<div class="input-field"><label for="username">Username</label><input type="text" name="user_name" class="text validate" id="username" autocomplete="off" autofocus></div><div class="input-field"><label for="password">Password</label><input type="password" name="user_password" class="text validate" id="password" autocomplete="off"></div>'];
} else {
    $type = 'invite_code';
    $title = 'Invite Code';
    $button_text = 'Check Invite Code';
    $content = ['<div class="input-field"><label for="auth_code">Invite Code</label><input type="text" name="auth_code" class="text validate" id="auth_code" autocomplete="off" value="' . clean_data($_GET['auth_code']) . ' autofocus></div>'];
}

?>


<!DOCTYPE html>

<html lang="en">

<?php head($title); ?>

<body>
<div class="wrapper">
    <form class="login">
        <input type="hidden" name="CSRFtoken" value="<?= csrf_gen(); ?>"/>
        <input type="hidden" name="type" value="<?= $type ?>"/>
        <p class="title"><?= $title ?></p>
        <?php foreach ($content as $row) {echo $row;} ?>
        <button id="submit"><i class="spinner"></i> <span class="state"><?= $button_text ?></span></button>
    </form>
</div>
<?php footer('register'); ?>
</body>

</html>
