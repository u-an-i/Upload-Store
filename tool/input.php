<?php
    require_once 'private/code/config.php';
    require_once 'private/code/borderpatrol.php';
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Upload to <?= get_config('hostReceiverName', Type::TEXT); ?></title>
        <link href="styling.css" rel="stylesheet">
        <style>
            .plural, .none, .hidden {
                display: none !important;
            }
            .multiple .plural {
                display: inline !important;
            }
            #progress {
                display: inline-block;
                width: 45px;
                text-align: right;
            }
            #progress::after {
                content: "%";
            }
            [name=files] {
                display: block;
            }
            [name=files].hidden + br {
                display: none;
            }
        </style>
    </head>
    <body>
        <form action="save.php" method="POST">
            <input type="file" multiple name="files" onchange="checkFiles(this)" title="open filepicker">
            <input type="hidden" name="c" value="<?= $_GET['c'] ?>">
            <button type="submit" onclick="upload(this);event.preventDefault()" id="filesSubmit" class="none">Upload file<span class="plural">s</span></button>
            <div id="status" class="hidden">Upload <span id="progress"></span> complete<span id="completed" class="hidden">d and so did saving on the server</span>.</div>
        </form>
        <script>
            var filesInput;
        
            function checkFiles(input) {
                if(input.files.length) {
                    filesInput = input;
                    var uploadButton = document.getElementById("filesSubmit");
                    uploadButton.classList.remove("none");
                    if(input.files.length > 1) {
                        uploadButton.classList.add("multiple");
                    } else {
                        uploadButton.classList.remove("multiple");
                    }
                } else {
                    document.getElementById("filesSubmit").classList.add("none");
                }
            }
        
            function upload(button) {
                button.disabled = "disabled";
                
                const formData = new FormData();
            
                formData.append("c", "<?= $_GET['c'] ?>");
                var index = -1;
                Array.prototype.forEach.call(filesInput.files, file => {
                    formData.append("f" +  ++index, file);
                });
                formData.append("a", "" + filesInput.files.length);
            
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "save.php");
                
                var progress = document.getElementById("progress");
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        progress.textContent = Math.floor((event.loaded / event.total) * 100);
                    }
                };
            
                xhr.onerror = () => document.getElementById("status").textContent = "Error.";
                xhr.onload = () => {
                    if (xhr.responseText === "success") {
                        progress.classList.add("hidden");
                        document.getElementById("completed").classList.remove("hidden");
                    } else if (xhr.status >= 500 && xhr.status < 600) {
                        document.getElementById("status").textContent = "Saving on the server failed.";
                    } else {
                        document.getElementById("status").textContent = "Error.";
                    }
                };
    
                document.getElementById("status").classList.remove("hidden");
                xhr.send(formData);
                filesInput.classList.add("hidden");
            }
        </script>
    </body>
</html>
<?php
    function config_error($reason) {
        echo $reason;
    }
    
    function entry_denied($reason) {
        require_once 'private/code/utilities.php';
        header('Location: https://' . normalise_domain_path(get_config('base', Type::TEXT)));
    }