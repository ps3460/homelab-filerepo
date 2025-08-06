<?php
// PHP function to format file size into a human-readable format
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Load Ionicons for UI elements -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(var(--glow-color-rgb), 0.2), 0 0 40px rgba(var(--glow-color-rgb), 0.1);
            }
            50% {
                box-shadow: 0 0 30px rgba(var(--glow-color-rgb), 0.4), 0 0 60px rgba(var(--glow-color-rgb), 0.2);
            }
        }
        :root {
            --glow-color: #ec4899; /* Hot Pink Neon */
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b1120; /* Deeper space blue */
            color: #d1d5db; 
        }
        .main-container {
            max-width: 800px;
            margin: 4rem auto;
            padding: 1rem;
        }
        .card {
            background-color: rgba(17, 24, 39, 0.8); /* Semi-transparent dark card */
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            border: 1px solid #374151;
            padding: 2rem;
            margin-bottom: 2rem;
            animation: pulse-glow 5s infinite ease-in-out;
            transition: box-shadow 0.3s ease-in-out;
        }
        .card-title {
            color: #f9fafb;
            text-shadow: 0 0 10px var(--glow-color), 0 0 20px var(--glow-color);
        }
        .file-input-label {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 2rem; border: 2px dashed #4b5563; border-radius: 0.5rem;
            cursor: pointer; text-align: center; transition: background-color 0.2s, border-color 0.2s, box-shadow 0.2s;
        }
        .file-input-label:hover {
            background-color: #374151; border-color: var(--glow-color);
            box-shadow: inset 0 0 15px rgba(var(--glow-color-rgb), 0.5);
        }
        input[type="file"] { display: none; }
        .submit-btn {
            display: block; width: 100%; padding: 0.85rem; background-color: var(--glow-color);
            color: white; border-radius: 0.5rem; border: none; font-weight: 600;
            cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 0 15px var(--glow-color);
            text-shadow: 0 0 5px rgba(255,255,255,0.5);
        }
        .submit-btn:hover { background-color: #db2777; box-shadow: 0 0 25px var(--glow-color); }
        .submit-btn:disabled { background-color: #4b5563; color: #9ca3af; cursor: not-allowed; box-shadow: none; }
        #file-name { margin-top: 1rem; color: #9ca3af; }
        .file-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 1rem; border-radius: 0.5rem;
            transition: background-color 0.2s, box-shadow 0.2s; border-bottom: 1px solid #374151;
        }
        .file-item:last-child { border-bottom: none; }
        .file-item:hover { 
            background-color: #374151;
            box-shadow: 0 0 10px rgba(var(--glow-color-rgb), 0.3);
        }
        .file-info a { color: var(--glow-color); text-decoration: none; font-weight: 500; text-shadow: 0 0 8px rgba(var(--glow-color-rgb), 0.7); }
        .file-info a:hover { text-decoration: underline; }
        .file-size { font-size: 0.875rem; color: #9ca3af; margin-left: 1rem; }
        .action-btn {
            background-color: #4b5563; color: #d1d5db; border: none; border-radius: 0.375rem;
            padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;
            transition: background-color 0.2s; margin-left: 0.5rem;
            display: inline-flex; align-items: center;
        }
        .action-btn ion-icon { margin-right: 0.3rem; }
        .action-btn:hover { background-color: #6b7280; }
        .delete-btn { background-color: rgba(153, 27, 27, 0.5); color: #fecaca; border: 1px solid #991b1b; }
        .delete-btn:hover { background-color: #991b1b; }
        #progress-wrapper { background-color: #374151; border-radius: 9999px; }
        #progress-bar { background-color: var(--glow-color); border-radius: 9999px; }
    </style>
</head>
<body>

    <div class="main-container">
        <!-- Uploader Card -->
        <div class="card">
            <h1 class="card-title text-2xl font-bold text-center mb-6">File Hub</h1>
            <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
                <label for="fileToUpload" class="file-input-label">
                    <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-2"></ion-icon>
                    <span class="text-gray-400 font-medium">Click to select a file</span>
                </label>
                <input type="file" name="fileToUpload" id="fileToUpload">
                <p id="file-name" class="text-center">No file selected</p>
                <button type="submit" id="submitBtn" class="submit-btn mt-6" disabled>Upload File</button>
            </form>
            <div id="progress-wrapper" class="w-full h-2.5 mt-4 hidden">
                <div id="progress-bar" class="h-2.5" style="width: 0%"></div>
            </div>
            <p id="status-message" class="text-center mt-2 font-medium"></p>
        </div>

        <!-- File List Card -->
        <div class="card">
            <h2 class="card-title text-xl font-bold text-center mb-6">Available Files</h2>
            <ul class="space-y-1">
                <?php
                $dir = "uploads/";
                if (is_dir($dir)){
                    $files = scandir($dir);
                    $fileCount = 0;
                    foreach($files as $file){
                        if($file !== '.' && $file !== '..'){
                            $fileCount++;
                            $filePath = $dir . $file;
                            // FIXED: Use rtrim to remove trailing slash from directory path, preventing double slashes in URL
                            $base_path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
                            $fileUrl = "http://" . $_SERVER['HTTP_HOST'] . $base_path . '/' . $filePath;
                            $fileSize = formatSizeUnits(filesize($filePath));
                            $displayName = htmlspecialchars($file);
                            
                            echo "<li class='file-item'>
                                    <div class='flex items-center truncate'>
                                        <ion-icon name='document-outline' class='text-xl text-gray-400 mr-3'></ion-icon>
                                        <div class='file-info truncate'><a href='{$filePath}' download title='{$displayName}'>{$displayName}</a></div>
                                        <div class='file-size flex-shrink-0'>{$fileSize}</div>
                                    </div>
                                    <div class='flex items-center flex-shrink-0'>
                                        <button class='action-btn' onclick=\"copyToClipboard('{$fileUrl}')\"><ion-icon name='copy-outline'></ion-icon>Copy</button>
                                        <form action='delete.php' method='post' onsubmit='return confirmDelete();' style='display: inline;'>
                                            <input type='hidden' name='filename' value='{$displayName}'>
                                            <button type='submit' class='action-btn delete-btn'><ion-icon name='trash-outline'></ion-icon>Delete</button>
                                        </form>
                                    </div>
                                  </li>";
                        }
                    }
                    if ($fileCount == 0) {
                        echo '<li class="text-gray-500 text-center py-4">No files have been uploaded yet.</li>';
                    }
                } else {
                    echo '<li class="text-red-500 text-center py-4">Error: The \'uploads\' directory does not exist.</li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <script>
        // Set CSS variable for glow color from JS
        document.documentElement.style.setProperty('--glow-color-rgb', '236, 72, 153');

        const uploadForm = document.getElementById('uploadForm');
        const fileInput = document.getElementById('fileToUpload');
        const fileNameDisplay = document.getElementById('file-name');
        const submitBtn = document.getElementById('submitBtn');
        const progressWrapper = document.getElementById('progress-wrapper');
        const progressBar = document.getElementById('progress-bar');
        const statusMessage = document.getElementById('status-message');

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = `Selected: ${fileInput.files[0].name}`;
                submitBtn.disabled = false;
                statusMessage.textContent = '';
                progressWrapper.classList.add('hidden');
                progressBar.style.width = '0%';
            } else {
                fileNameDisplay.textContent = 'No file selected';
                submitBtn.disabled = true;
            }
        });

        uploadForm.addEventListener('submit', function(event) {
            event.preventDefault();

            if (fileInput.files.length === 0) {
                alert('Please select a file to upload.');
                return;
            }

            submitBtn.disabled = true;
            statusMessage.textContent = 'Uploading...';
            progressWrapper.classList.remove('hidden');

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', function() {
                submitBtn.disabled = false;
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        statusMessage.textContent = response.message;
                        if(response.status === 'success') {
                            statusMessage.className = 'text-center mt-2 font-medium text-green-400';
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            statusMessage.className = 'text-center mt-2 font-medium text-red-400';
                        }
                    } catch (e) {
                        statusMessage.textContent = 'Error parsing server response.';
                        statusMessage.className = 'text-center mt-2 font-medium text-red-400';
                    }
                } else {
                    statusMessage.textContent = 'An error occurred during the upload. (Server status: ' + xhr.status + ')';
                    statusMessage.className = 'text-center mt-2 font-medium text-red-400';
                }
            });

            xhr.addEventListener('error', function() {
                submitBtn.disabled = false;
                statusMessage.textContent = 'A network error occurred. Please try again.';
                statusMessage.className = 'text-center mt-2 font-medium text-red-400';
                progressWrapper.classList.add('hidden');
            });

            xhr.open('POST', 'upload.php', true);
            xhr.send(formData);
        });

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('URL copied to clipboard!');
                });
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-9999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('URL copied to clipboard!');
                } catch (err) {
                    alert('Failed to copy URL.');
                }
                document.body.removeChild(textArea);
            }
        }

        function confirmDelete() {
            return confirm('Are you sure you want to delete this file? This action cannot be undone.');
        }
    </script>

</body>
</html>