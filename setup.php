<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Setup Configuration of Upload Store</title>
    </head>
    <body>
        <?php
            function configure() {
                if(!array_key_exists('a', $_POST) || $_POST['a'] !== '1') {
                    $default_config = '{
                        "base": "",
                        "salt": "",
                        "serverFromEmail": "",
                        "hostReceiverName": "",
                        "whitelist": [],
                        "enableManualApproval": false,
                        "manualApprovallEmail": "",
                        "notifyEmail": "",
                        "serviceEnabled": false,
                        "setupGuardEmail": ""
                    }';
                    if(!file_exists('private/config.json')) {
                        if(!file_put_contents('private/config.json', $default_config)) {
                            echo 'error creating configuration on server.';
                            return;
                        }
                    }
                    $config = json_decode(file_get_contents('private/config.json'), true);
                    if($config !== false) {
                        $config = array_merge($config, array_diff_key(json_decode($default_config, true), $config));
                        if(!is_string($config['salt'])) {
                            $config['salt'] = '';
                        }
                        if(strlen($config['salt']) > 0) {
                            $time = time();
                            if(!array_key_exists('p', $_GET) || !array_key_exists('r', $_GET) || intval($_GET['r']) < intval($time % 600) && $_GET['p'] !== md5($config['setupGuardEmail'] . $config['salt'] . intval(floor($time / 600))) || intval($_GET['r']) >= intval($time % 600) && $_GET['p'] !== md5($config['setupGuardEmail'] . $config['salt'] . (intval(floor($time / 600)) - 1))) {
                                if(!is_string($config['setupGuardEmail'])) {
                                    $config['setupGuardEmail'] = '';
                                }
                                if(strlen($config['setupGuardEmail']) > 0 && filter_var($config['setupGuardEmail'], FILTER_VALIDATE_EMAIL)) {
                                    require_once 'private/code/utilities.php';
                                    if(mail(
                                        $config['setupGuardEmail'],
                                        'link to change configuration of Upload Store, link valid for 10 minutes from time of its creation on',
                                        'https://' . normalise_domain_path($config['base']) . '/setup.php?p=' . md5($config['setupGuardEmail'] . $config['salt'] . intval(floor($time / 600))) . '&r=' . intval($time % 600),
                                        'From:' . $config['serverFromEmail']
                                    )) {
                                        echo 'E-mail containing link to change configuration sent. Link valid for 10 minutes from time of its creation on.';
                                    } else {
                                        echo 'Failed to send e-mail containing link to change configuration.';
                                    }
                                } else {
                                    echo 'E-mail address to send to link to access this setup to change existing configuration is invalid. Configuration not editable with this setup.';
                                }
                                return;
                            }
                        }
                        ?>
                            <link href="styling.css" rel="stylesheet">
                            <style>
                                form {
                                    margin: 45px 0;
                                    max-height: calc(100vh - 90px);
                                    outline: 1px solid rgba(0, 0, 0, .25);
                                    box-shadow: 0 0 calc(max(min(768px, 100vw) / 2, (100vh - 90px) / 2) * (70px / (min(768px, 100vw) / 2 + 100vw / 2))) 0 rgba(0, 0, 0, .25);
                                }
                                form > div {
                                    display: flex;
                                    gap: 15px;
                                    align-items: flex-start;
                                }
                                label {
                                    flex: 1 1 auto;
                                }
                                form > div:has(>[required]:not([disabled])) > label::after {
                                    content: " [required]";
                                }
                                form > div > * {
                                    flex: 0 0 calc(50% - 7.5px);
                                    min-width: 0;
                                }
                                input {
                                    box-sizing: border-box;
                                    padding: 5px;
                                    margin: 0;
                                }
                                input[type=checkbox] {
                                    cursor: pointer;
                                    width: 33px;
                                    height: 33px;
                                }
                                #whitelist {
                                    display: flex;
                                    flex-direction: column;
                                    gap: 10px;
                                    justify-content: center;
                                    margin: 0;
                                    padding: 0;
                                }
                                .subform {
                                    display: flex;
                                    flex-direction: column;
                                    gap: 5px;
                                    text-align: left;
                                    padding: 0;
                                }
                                .subform > div {
                                    display: flex;
                                    gap: 5px;
                                }
                                .subform input {
                                    flex: 1 1 auto;
                                    min-width: 0;
                                }
                                button {
                                    cursor: pointer;
                                    display: block;
                                }
                                div:has(> #whitelist) {
                                    display: flex;
                                    flex-direction: column;
                                }
                                .add {
                                    height: 33px;
                                    align-self: center;
                                }
                                #whitelist:has(.subform) + .add {
                                    margin-top: 20px;
                                }
                                [required]:not([disabled]) {
                                    outline: 2px solid red;
                                }
                                .subform [required]:not([disabled]) {
                                    outline: 2px solid orange;
                                }
                                .template {
                                    display: none;
                                }
                                button[type=submit] {
                                    align-self: center;
                                    padding: 5px 20px;
                                }
                            </style>
                            <form action="" method="POST">
                                <div>
                                    <label for="serverFromEmail">E-mail address to appear as FROM email address in an e-mail containing an upload link sent to someone having inquired such one (e-mail address domain might have to be the domain this tool is deployed on (due to possible server policy))</label>
                                    <input type="email" id="serverFromEmail" name="e" value="<?= is_string($config['serverFromEmail']) ? $config['serverFromEmail'] : ''; ?>" required title="required">
                                </div>
                                <div>
                                    <label for="hostReceiverName">Name to be shown in texts of e-mails and pages of this tool as the recipient of a (potential) upload</label>
                                    <input type="text" id="hostReceiverName" name="h" value="<?= is_string($config['hostReceiverName']) ? $config['hostReceiverName'] : ''; ?>" required title="required">
                                </div>
                                <div>
                                    <label for="whitelist">List of e-mail addresses of people who get sent an e-mail containing an upload link immediately upon inquiry without manual approval</label>
                                    <?php
                                        if(is_array($config['whitelist'])) {
                                            ?>
                                                <div>
                                                    <ul id="whitelist">
                                                    <?php
                                                        $index = -1;
                                                        foreach($config['whitelist'] as $email) {
                                                            if(is_string($email)) {
                                                                ++$index;
                                                                ?>
                                                                    <li class="subform">
                                                                        <label for="w<?= $index ?>">E-mail address <span class="number"><?= $index + 1 ?></span></label>
                                                                        <div><input type="email" id="w<?= $index ?>" name="w<?= $index ?>" value="<?= $email ?>" required title="required unless deleted"><button type="button" class="delete" onclick="del(this)">⛔</button></div>
                                                                    </li>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                    </ul>
                                                    <button type="button" class="add" onclick="add()">➕</button>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div>
                                    <label for="enableManualApproval">Get offered to manually approve (or deny) an inquiry to upload from someone whose e-mail address is not on the list above</label>
                                    <input type="checkbox" id="enableManualApproval" name="m" value="1" <?= is_bool($config['enableManualApproval']) && $config['enableManualApproval'] ? 'checked' : ''; ?> onclick="switchState(this)">
                                </div>
                                <div>
                                    <label for="manualApprovallEmail">E-mail address to be sent to the offers to manual approve (or deny)</label>
                                    <input type="email" id="manualApprovallEmail" name="o" value="<?= is_string($config['manualApprovallEmail']) ? $config['manualApprovallEmail'] : ''; ?>" <?= is_bool($config['enableManualApproval']) && $config['enableManualApproval'] ? '' : 'disabled'; ?> required title="required">
                                </div>
                                <div>
                                    <label for="notifyEmail">E-mail address to get sent to a notification about a successfull upload</label>
                                    <input type="email" id="notifyEmail" name="n" value="<?= is_string($config['notifyEmail']) ? $config['notifyEmail'] : ''; ?>">
                                </div>
                                <div>
                                    <label for="serviceEnabled">Service by this tool Enabled</label>
                                    <input type="checkbox" id="serviceEnabled" name="s" value="1" <?= is_bool($config['serviceEnabled']) ? $config['serviceEnabled'] ? 'checked' : '' : ''; ?>>
                                </div>
                                <div>
                                    <label for="g">E-mail address to send to link to change configuration after saving this 1st setup</label>
                                    <input type="email" id="g" name="g" value="<?= is_string($config['setupGuardEmail']) ? $config['setupGuardEmail'] : ''; ?>" required title="required">
                                </div>
                                <input type="hidden" name="b">
                                <input type="hidden" name="a" value="1">
                                <button type="submit">Save</button>
                            </form>
                            <div id="whitelistItemTemplate" class="subform template">
                                <label>E-mail address <span class="number"></span></label>
                                <div><input type="hidden" required title="required unless deleted"><button class="delete" onclick="del(this)">⛔</button></div>
                            </div>
                            <script>
                                function del(src) {
                                    var subform = src.closest(".subform");
                                    var index = parseInt(subform.querySelector(".number").textContent, 10) - 1;
                                    var nextSubform = subform.nextElementSibling;
                                    subform.remove();
                                    while(nextSubform !== null) {
                                        nextSubform.querySelector("label").setAttribute("for", "w" + index);
                                        var input = nextSubform.querySelector("input");
                                        input.id = "w" + index;
                                        input.name = "w" + index;
                                        nextSubform.querySelector(".number").textContent = "" + ++index;
                                        nextSubform = nextSubform.nextElementSibling;
                                    }
                                    
                                }
                                var whitelist = document.getElementById("whitelist");
                                var whitelistItem = document.getElementById("whitelistItemTemplate").cloneNode(true);
                                whitelistItem.removeAttribute("id");
                                whitelistItem.classList.remove("template");
                                whitelistItem.querySelector("input").type = "email";
                                function add() {
                                    var lastSubform = whitelist.lastElementChild;
                                    if(lastSubform) {
                                        var index = parseInt(lastSubform.querySelector(".number").textContent, 10);
                                    } else {
                                        var index = 0;
                                    }
                                    whitelistItemClone = whitelistItem.cloneNode(true);
                                    whitelistItemClone.querySelector("label").setAttribute("for", "w" + index);
                                    var input = whitelistItemClone.querySelector("input");
                                    input.id = "w" + index;
                                    input.name = "w" + index;
                                    whitelistItemClone.querySelector(".number").textContent = "" + (index + 1);
                                    whitelist.appendChild(whitelistItemClone);
                                }
                                var manualApprovallEmail = document.getElementById("manualApprovallEmail");
                                function switchState(src) {
                                    src.checked ? manualApprovallEmail.removeAttribute("disabled") : manualApprovallEmail.setAttribute("disabled", "");
                                }
                                document.querySelector("input[type=hidden][name=b]").value = location.hostname + location.pathname.replace("setup.php", "");
                                var maxShadow = 70;
                                var form = document.querySelector("form");
                                addEventListener("mousemove", event => {
                                    var refShadow = maxShadow / (form.clientWidth / 2 + innerWidth / 2);
                                    var x = event.clientX - innerWidth / 2;
                                    var y = event.clientY - innerHeight / 2;
                                    form.style.boxShadow = (-x * refShadow) + "px " + (-y * refShadow) + "px " + (Math.max(form.clientWidth / 2, form.clientHeight / 2) * refShadow) + "px " + 0 + "px rgba(0, 0, 0, .25)";
                                });
                            </script>
                        <?php
                    } else {
                        echo 'error reading configuration on server.';
                    }
                } else {
                    function is_email($val) {
                        return strlen($val) > 0 && filter_var($val, FILTER_VALIDATE_EMAIL);
                    }
                    
                    function is_text($val) {
                        return strlen($val) > 0;
                    }
                    
                    function is_boolean($val) {
                        return $val === '1';
                    }
                    
                    function opted_out($val) {
                        return strlen($val) === 0;
                    }
                    
                    $check = [
                        'b' => function($val) {
                            return is_text($val);
                        },
                        'g' => function($val) {
                            return is_email($val);
                        },
                        'e' => function($val) {
                            return is_email($val);
                        },
                        'h' => function($val) {
                            return is_text($val);
                        }
                    ];
                    
                    foreach($check as $key => $func) {
                        if(!array_key_exists($key, $_POST) || !$func($_POST[$key])) {
                            return;
                        }
                    }
                    
                    $index = -1;
                    while(array_key_exists('w' . ++$index, $_POST)) {
                        if(!is_email($_POST['w' . $index])) {
                            return;
                        }
                    }
                    
                    if(array_key_exists('m', $_POST)) {
                        if(!is_boolean($_POST['m'])) {
                            return;
                        }
                        if(!array_key_exists('o', $_POST) || !is_email($_POST['o'])) {
                            return;
                        }
                    }
                    
                    if(array_key_exists('n', $_POST)) {
                        if(!opted_out($_POST['n']) && !is_email($_POST['n'])) {
                            return;
                        }
                    } else {
                        return;
                    }
                    
                    if(array_key_exists('s', $_POST)) {
                        if(!is_boolean($_POST['s'])) {
                            return;
                        }
                    }
                    
                    
                    $config = json_decode(file_get_contents('private/config.json'), true);
                    if($config !== false) {
                        if(!is_string($config['salt'])) {
                            echo 'configuration tampered with. Nothing saved.';
                            return;
                        }
                        
                        if(strlen($config['salt']) === 0) {
                            $salt = '';
                            $counter = 17;
                            while(--$counter) {
                                $salt .= chr(32 + rand(0, 32)) . chr(65 + rand(0, 61));
                            }
                            $config['salt'] = $salt;
                        }
                        
                        $config['base'] = $_POST['b'];
                        $config['setupGuardEmail'] = $_POST['g'];
                        $config['serverFromEmail'] = $_POST['e'];
                        $config['hostReceiverName'] = $_POST['h'];
                        
                        $w = [];
                        $index = -1;
                        while(array_key_exists('w' . ++$index, $_POST)) {
                            $w[] = $_POST['w' . $index];
                        }
                        $config['whitelist'] = $w;
                        
                        $m = array_key_exists('m', $_POST);
                        $config['enableManualApproval'] = $m;
                        if($m) {
                            $config['manualApprovallEmail'] = $_POST['o'];
                        }
                        
                        $config['notifyEmail'] = $_POST['n'];
                        
                        $config['serviceEnabled'] = array_key_exists('s', $_POST);
                        
                        $json = json_encode($config, JSON_PRETTY_PRINT);
                        if($json !== false && file_put_contents('private/config.json', $json)) {
                            echo 'Saved.';
                            return;
                        }
                    }
                    
                    echo 'error reading configuration on server.';
                }
            }
            configure();
        ?>
    </body>
</html>