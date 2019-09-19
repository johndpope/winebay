<?php

// move_all_images();
// import_all_images();
// process_products_data();
// fix_transparency();
remove_spaces();

function remove_spaces()
{
    $base = './upload/images';
    $folder = scandir($base);

    foreach ($folder as $i => $file) {
        $destFile = preg_replace('/\s+/', '', $file);
        echo "[$i] - $file -> $destFile\n";
        // copy("./imagens/$file", "./imagens_proccess/" . date('YdHi') . floor(rand() % 1000) . $destFile);
        rename("$base/$file", "$base/$destFile");
    }
}

function process_products_data()
{
    $host = "localhost";
    $user = "admin";
    $senha = "dodo1992";
    $db = "winebay_admin";
    $conexao = mysqli_connect($host, $user, $senha, $db) or die('Erro ao conectar ao MySQL');
    $sql = "SELECT * FROM product ORDER BY name ASC";
    $result = mysqli_query($conexao, $sql);
    while ($data = mysqli_fetch_assoc($result)) {
        $procName = GenerateFilename($data['name']);
        $imageSql = "SELECT id FROM image WHERE path LIKE '%$procName%';";
        $imageResult = mysqli_query($conexao, $imageSql);
        $imageData = mysqli_fetch_assoc($imageResult);
        $imgTxt = 'NO';
        if ($imageData) {
            $imgTxt = 'YES';
            mysqli_query($conexao, "UPDATE product SET id_image_thumb = {$imageData['id']} WHERE id = {$data['id']}");
        }
        echo "[{$data['id']}] - {$data['name']} -> {$procName} $imgTxt\n";
    }
}

function import_all_images()
{
    $folder = scandir('./imagens_proccess');
    foreach ($folder as $i => $file) {
        $imgSql = "INSERT INTO image (path) VALUES ('/upload/images/$file');";
        $myfile = file_put_contents('sql.txt', $imgSql . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function move_all_images()
{
    $folder = scandir('./imagens');

    foreach ($folder as $i => $file) {
        $destFile = GenerateFilename($file);
        echo "[$i] - $file -> $destFile\n";
        copy("./imagens/$file", "./imagens_proccess/" . date('YdHi') . floor(rand() % 1000) . $destFile);
    }
}

function fix_transparency()
{
    $path = '/var/www/winebay/admin/server/public/upload/images';
    $folder = scandir($path);

    foreach ($folder as $i => $file) {
        if (($file != '.') && ($file != '..')) {
            echo "[$i] - $file\n";
            $img = new Imagick("$path/$file");
            $img->paintTransparentImage($img->getImageBackgroundColor(), 0, 1500);
            $img->trimImage(20000);
            $img->setImageFormat('png');
            $replaced = str_replace(".jpg", ".png", $file);
            $img->writeImage($replaced);
            unlink("$path/$file");
        }
    }
}

function GenerateFilename($file)
{
    $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
        'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
        'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', ' ' => '_', '(' => '', ')' => '', 'º' => '', '°' => '', "'" => '');
    $newFile = strtolower(strtr($file, $unwanted_array));

    return $newFile;
}
