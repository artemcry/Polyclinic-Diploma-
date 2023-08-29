<?php
require_once '../importcss.html';

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'clinic';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_errno) {
    echo 'Failed to connect to MySQL: ' . $mysqli->connect_error;
    exit;
}

$backupFileDB = 'db_'.date('Y-m-d_H-i-s').'.sql';

$command = "mysqldump --host=$host --user=$username --password=$password $database > $backupFileDB";
system($command, $output);
$msg = "";
if ($output === 0) {
    $msg .= "<br><h5 id=error-msg style=\"color: Green;\">БД збережено</h5>";
} else {
    $msg .= "<br><h5 id=error-msg style=\"color: red;\">Помилка збереження БД</h5>";
}

$mysqli->close();

//////// site 

$backupDir = $_SERVER['DOCUMENT_ROOT'];
$websiteDir = $_SERVER['DOCUMENT_ROOT'];
$backupFile = '/backup/website_' . date('Y-m-d_H-i-s') . '.zip';
$backupFileFullPath = $backupDir . $backupFile;
$zip = new ZipArchive();

if ($zip->open($backupFileFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($websiteDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($websiteDir) + 1);

            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->addFile($backupFileDB);
    $zip->close();
    unlink($backupFileDB);

    $msg .= "<br><h5 id=error-msg style=\"color: green;\">Сайт збережено</h5>";

} else {
    $msg .= "<br><h5 id=error-msg style=\"color: red;\">Помилка </h5>";

}

?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="d-flex flex-column align-items-center mb-5">
                <button onclick="window.location.href = 'main.php'" type="button" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </button>
                <h2 class="mt-3">Створення резервної копії</h2>
                <?=$msg?>        
            </div>
        </div>
    </div>
</div>
<?php

$backupFilePath = $backupFileFullPath;
$backupFileName = 'backup_'.date('Y-m-d_H-i-s').'.zip';

function downloadFile($filePath, $fileName) {
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . $fileName . '"');
  readfile($filePath);
}

downloadFile($backupFilePath, $backupFileName);
?>
