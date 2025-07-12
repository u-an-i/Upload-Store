<?php
    require_once 'private/code/config.php';
    
    $hostReceiverName = get_config('hostReceiverName', Type::TEXT);
    
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Inquire to upload to <?= $hostReceiverName ?></title>
        <link href="styling.css" rel="stylesheet">
        <style>
            label {
                display: block;
                text-align: center;
            }
            button[type=submit] {
                visibility: hidden;
            }
            #emailinput:valid ~ button[type=submit] {
                visibility: visible;
            }
            ::placeholder {
                font-family: Tahoma, sans-serif;
                color: rgb(0, 0, 0, .5);
                font-weight: 300;
            }
            @media (min-width: 461px) {
                ::placeholder {
                    font-size: 15px;
                }
            }
            @media (max-width: 460px) {
                ::placeholder {
                    font-size: 10px;
                }
            }
        </style>
    </head>
    <body>
        <form action="inquire.php" method="POST">
            <label for="emailinput">Request from <?= $hostReceiverName ?> an email containing a link to a page on which you can let upload file(s) to <?= $hostReceiverName ?>:</label>
            <input type="email" required id="emailinput" name="e" placeholder="Enter the email address to which that email shall be sent" size="60">
            <?php require_once 'private/code/greencard.php'; ?>
            <button type="submit">Request email</button>
        </form>
        <script>
            var cacheTest = "<?= rand(); ?>";
            onpageshow = function () {
                if(sessionStorage.getItem("cacheTest") === cacheTest) {
                    location.reload();
                } else {
                    sessionStorage.setItem("cacheTest", cacheTest);
                    document.getElementById("emailinput").focus();
                }
            };
        </script>
    </body>
</html>
<?php
    function config_error($reason) {
        echo $reason;
    }