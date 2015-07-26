<?php
namespace modes;

function process_upload() {
	$new_name = get_new_file_name();
	$path = FILES_DIR . '/' . $new_name;
	// if (!validate_upload($_FILES['tmp_name'])) {

	// }
    $file = (object) $_FILES['file'];
    $md5 = md5_file($file->tmp_name);

    $existing = get_file_by_hash($md5);

    fb(123123);

    if ($existing) {
        error_page(500, 'File already exists', FALSE);

        return;
    }

    $img_info = getimagesize($file->tmp_name);

    if (!$img_info) {
        fb(htmlspecialchars(file_get_contents($file->tmp_name)));

        error_page(500, 'Unsupported file type', FALSE);

        return;
    }

    $new_name = get_new_file_name();

    if (move_uploaded_file($file->tmp_name, $uploadfile)) {

        $sql = 'INSERT INTO file (md5, type, id) VALUES (?, ?, ?)';

        $query = $this->dbh->prepare($sql);

        $query->execute(array($md5, 'image'));

        echo 'File uploaded';
    } else {
        error_page(500, 'Upload failed', FALSE);
    }
}

function save_file() {

}



function xsave_file($temp_name, $url = null, $referrer = null) {
    $img_info = @getimagesize($temp_name);
    $max_dimension = 500;
    if (!$img_info) {
        echo 'Not an image.<br /><br />';
		$debug_data = htmlspecialchars(file_get_contents($temp_name));
		echo "<textarea cols='100' rows='10'>$debug_data</textarea>";
        return;
    }
    list($width, $height, $ext) = $img_info;
    $type = $img_info['mime'];
    $ext = image_type_to_extension($ext, false);
    $ext_o = $ext;
    if ($ext_o == 'jpeg') $ext_o = 'jpg';
    if (in_array($ext, array_keys($this->_extensions))) {
        $ext = $this->_extensions[$ext];
    }
    $url_comp = parse_url($url);

    $md5 = md5_file($temp_name);
    $name = $this->getNewId();
    $existing = $this->getFileByHash($md5);
    if ($existing) {
        if (file_exists($this->_path . $existing['file'])) {
            return $existing['file'];
        }
        $file_name = $existing['file'];
    } else {
        $file_name = "$name.$ext";
    }
    if ($url && isset($url_comp['host']) && $url_comp['host'] == 'cheezpictureisunrelated.files.wordpress.com') {
        $im = new Imagick($temp_name);
        $im->cropImage($width, $height - 10, 0, 0);
        $im->writeImage($temp_name);
    }
    if ($url && isset($url_comp['host']) && $url_comp['host'] == 'cheezcomixed.files.wordpress.com') {
        $im = new Imagick($temp_name);
        $im->cropImage($width, $height - 10, 0, 0);
        $im->writeImage($temp_name);
    }
    $size = filesize($temp_name);


    $size_o = $width_o = $height_o = $md5_o = $file_name_o = null;

    if ($ext != 'swf') {

        if ($width > $max_dimension) {
            $size_o = $size;
            $width_o = $width;
            $height_o = $height;
            $md5_o = $md5;
            $file_name_o = "{$name}_o.$ext_o";
            if (!file_exists($this->_path . $file_name_o)) {
                var_dump($temp_name);
                var_dump(stat($temp_name));
                var_dump($this->_path . $file_name_o);
                copy($temp_name, $this->_path . $file_name_o);
            }
            $image = new Imagick($temp_name);
            $o = $image->getImageOrientation();
            switch($o) {
                case 1:
                    $rotate = 0;
                    $flip = false;
                    break;
                case 2:
                    $rotate = 0;
                    $flip = true;
                    break;
                case 3:
                    $rotate = 180;
                    $flip = false;
                    break;
                case 4:
                    $rotate = 180;
                    $flip = true;
                    break;
                case 5:
                    $rotate = 90;
                    $flip = true;
                    break;
                case 6:
                    $rotate = 90;
                    $flip = false;
                    break;
                case 7:
                    $rotate = 270;
                    $flip = true;
                    break;
                case 8:
                    $rotate = 270;
                    $flip = false;
                    break;
                default:
                    $rotate = 0;
                    $flip = false;
            }
            if ($rotate) {
                $image->rotateImage(new ImagickPixel(), $rotate);
                list($width, $height) = array($height, $width);
            }
            if ($flip) {
                $image->flipImage();
            }
            if ($width > $height) {
                $width = $max_dimension;
                $height = 0;
            } else {
//                $height = $max_dimension;
                $width = 400;
                $height = 0;
            }
            $image->thumbnailImage($width, $height);
            if (!$width)
                $width = $image->getImageWidth();
            else
                $height = $image->getImageHeight();
            $image->writeImage($this->_path . $file_name);
            $md5 = md5_file($this->_path . $file_name);
            $size = filesize($this->_path . $file_name);
        } elseif (!file_exists($this->_path . $file_name)) {
            copy($temp_name, $this->_path . $file_name);
        }
    } elseif (!file_exists($this->_path . $file_name)) {
        copy($temp_name, $this->_path . $file_name);
    }
    if (!$existing) {
        $animated = is_animated_gif($this->_path . $file_name) ? 1 : 0;
//            var_dump($animated);
//            var_dump($this->_path . $file_name);exit;
        $sql = 'INSERT INTO img (md5, file, source, '
            .'width, height, type, size, created_by, referrer, width_o, height_o, md5_o, file_o, size_o, animated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($md5, $file_name, $url, $width, $height, $type,
            $size, $_SESSION['user'], $referrer, $width_o, $height_o, $md5_o, $file_name_o, $size_o, $animated);

    } else {
        $sql = 'UPDATE img SET deleted = NULL, restored = NOW(), restored_by = ? WHERE md5 = ?';
        $params = array($_SESSION['user'], $md5);
    }
    $query = $this->dbh->prepare($sql);
    $query->execute($params);

    if ($ext != 'swf') {
        $to_width = 150;
        $qqq = $this->_path . $name . "_$to_width.jpg";
        if ($width > $to_width || $ext != 'jpg') {
            $image = new Imagick($this->_path . $file_name);
            $image->thumbnailImage($to_width, 0);
            $image->writeImage($qqq);
        } else {
            copy($this->_path . $file_name, $qqq);
        }
    }
    return $file_name;
}