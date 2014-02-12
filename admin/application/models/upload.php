<?php
	define('APPLICATION_ADMIN', true);
	$link = null;
	
	require_once '../../../define.php';
	require_once '../../../application/config.php';
	require_once '../../../core/views/html.php';
	require_once '../../../core/models/image.php';
	
	function db_connect() {
		global $config, $link;
		$link = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pwd']);
		mysql_select_db($config['db']['db'], $link);
		mysql_query('SET NAMES '.$config['db']['charset']); 
	}
	function db_disconnect() {
		global $link;
		mysql_close($link);
	}
	
	function v($str) {
		return addslashes($str);
	}

	if(empty($_POST['type'])) die();
	switch($_POST['type']) {
		case 'photos':
			if(!empty($_FILES['Filedata']['size']) && file_exists($_FILES['Filedata']['tmp_name'])) {
				$pid = (empty($_POST['pid'])) ? 0 : (int)$_POST['pid'];
				$mid = (empty($_POST['mid'])) ? 0 : (int)$_POST['mid'];
				if($pid) {
					db_connect();
					$ext = explode('.', $_FILES['Filedata']['name']);
					$ext = $ext[count($ext)-1];
					mysql_query('INSERT INTO photos SET 
									pid       =  '.$pid.', 
									module_id =  '.$mid.',
									extension = "'.v($ext).'",
									title     = "'.v($_FILES['Filedata']['name']).'"
								');
					$id = mysql_insert_id();
					if($id) {
						$new_path = $config['files']['images'].$id.'.'.$ext;
						if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $new_path)) {
							
							$resize_w = empty($_POST['resize-w']) ? 0 : (int)$_POST['resize-w'];
							$resize_h = empty($_POST['resize-h']) ? 0 : (int)$_POST['resize-h'];
							if(($resize_w !== 0) || ($resize_h !== 0)) {
								$gd = new image();
								$img = $gd->analyze($new_path);
								if(!empty($img)) {
									$gd->toFile($new_path, 80, $resize_w, $resize_h);
									$gd->toFile($config['files']['images'].'t'.DS.$id.'.'.$ext, 80, 145, 145);
								}
							}
							
							$html = new html();
							$photos['listing'] = array();
							$resq = mysql_query('SELECT * FROM photos WHERE pid = '.$pid.' AND module_id = '.$mid.' ORDER BY id');
							while($row = mysql_fetch_assoc($resq)) {
								$row['src'] = '/application/includes/uploadIMG/t/'.$row['id'].'.'.$row['extension'];
								$row['url'] = '/application/includes/uploadIMG/'.$row['id'].'.'.$row['extension'];
								$row['zap'] = ',';
								$photos['listing'][] = $row;
							}
							$photos['listing'][ sizeof($photos['listing'])-1 ]['zap'] = '';
							$photos['pid']       = $pid;
							$photos['block']     = 'photos';
							$photos['module_id'] = $mid;
							$tiny_image = $html->render('lists/tiny_image.html', array('listing' => $photos['listing']));
							file_put_contents(INDEX.'admin'.DS.'application'.DS.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image);
							echo $html->render('submodules/photos_list.html', $photos);
						} else {
							mysql_query('DELETE FROM photos WHERE id = '.(int)$id);
						}
					}
					db_disconnect();
				}
			}
			break;
		case 'images':
			if(!empty($_FILES['Filedata']['size']) && file_exists($_FILES['Filedata']['tmp_name'])) {
				$pid = (empty($_POST['pid'])) ? 0 : (int)$_POST['pid'];
				$mid = (empty($_POST['mid'])) ? 0 : (int)$_POST['mid'];
				if($pid) {
					db_connect();
					$sort = 1;
					$res_q = mysql_query('SELECT MAX(sort) FROM images WHERE pid = '.$pid.' AND module_id = '.$mid);
					if(mysql_num_rows($res_q)) {
						$sort = mysql_fetch_row($res_q);
						$sort = (int)$sort[0] + 1;
					}
					mysql_query('INSERT INTO images SET 
									pid       =  '.$pid.', 
									module_id =  '.$mid.',
									title     = "'.v($_FILES['Filedata']['name']).'",
									sort      = '.$sort.'
								');
					$id = mysql_insert_id();
					if($id) {
						$path_b = $config['files']['img'].'b/'.$id.'.jpg';
						$path_l = $config['files']['img'].'l/'.$id.'.jpg';
						$path_t = $config['files']['img'].'t/'.$id.'.jpg';		//Для отображения в админке
						if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $path_b)) {
							$gd = new image();
							$omg = $gd->analyze($path_b);

							if((int)$omg['b_height'] > (int)$omg['b_width']){
								$scale_b = $omg['b_height']/(int)$config['images']['height_big'];
								$width_b = (int)($omg['b_width']/$scale_b);
								$scale_l = $omg['b_height']/(int)$config['images']['height_small'];
								$width_l = (int)$config['images']['width_small'];
							
								$gd->toFile($path_b, 80, $width_b, $config['images']['height_big']);
								$gd->toFile($path_l, 80, $width_l, $config['images']['height_small']);
							}
							else {
								$gd->toFile($path_b, 80, $config['images']['width_big'], $config['images']['height_big']);	
								$gd->toFile($path_l, 80, $config['images']['width_small'], $config['images']['height_small']);
							}
							
							$gd->toFile($path_t, 80, 145, 145);					//Для отображения в админке
							$img = $gd->analyze($path_b);
							if(empty($img)) {
								$sql = 'DELETE FROM images WHERE id = '.$id;
							} else {
								$sql = 'UPDATE images SET b_width = '.(int)$img['b_width'].', b_height = '.(int)$img['b_height'].' WHERE id = '.$id;
							}
							mysql_query($sql);
							$html = new html();
							$photos['listing'] = array();
							$resq = mysql_query('SELECT * FROM images WHERE pid = '.$pid.' AND module_id = '.$mid.' ORDER BY id');
							while($row = mysql_fetch_assoc($resq)) {
								$row['src'] = '/application/includes/img/t/'.$row['id'].'.jpg';
								$photos['listing'][] = $row;
							}
							$photos['pid']       = $pid;
							$photos['block']     = 'images';
							$photos['module_id'] = $mid;
							echo $html->render('submodules/images_list.html', $photos);
						} else {
							mysql_query('DELETE FROM images WHERE id = '.(int)$id);
						}
					}
					db_disconnect();
				}
			}
			break;
		case 'files':
			if(!empty($_FILES['Filedata']['size']) && file_exists($_FILES['Filedata']['tmp_name'])) {
				$pid = (empty($_POST['pid'])) ? 0 : (int)$_POST['pid'];
				$mid = (empty($_POST['mid'])) ? 0 : (int)$_POST['mid'];
				if($pid) {
					db_connect();
					$ext = explode('.', $_FILES['Filedata']['name']);
					$filename = $ext;
					unset($filename[count($filename)-1]);
					$filename = implode('.', $filename);
					$ext  = $ext[count($ext)-1];
					$sort = 1;
					$res_q = mysql_query('SELECT MAX(sort) FROM files WHERE pid = '.$pid.' AND module_id = '.$mid);
					if(mysql_num_rows($res_q)) {
						$sort = mysql_fetch_row($res_q);
						$sort = (int)$sort[0] + 1;
					}
					mysql_query('INSERT INTO files SET 
									pid       =  '.$pid.', 
									module_id =  '.$mid.',
									filename  = "'.v($filename).'",
									filesize  = "'.$_FILES['Filedata']['size'].'",
									filetype  = "'.$_FILES['Filedata']['type'].'",
									extension = "'.v($ext).'",
									sort      = '.$sort.'
								');
					$id = mysql_insert_id();
					if($id) {
						if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $config['files']['file'].$id.'.'.$ext)) {
							$html = new html();
							$photos['listing'] = array();
							$resq = mysql_query('SELECT * FROM files WHERE pid = '.$pid.' AND module_id = '.$mid.' ORDER BY id');
							while($row = mysql_fetch_assoc($resq)) {
								$row['src'] = '/application/includes/files/'.$row['id'].'.'.$row['extension'];
								if(mb_strlen($row['filename'], 'utf-8') > 60) {
									$row['filename'] = mb_substr($row['filename'],0,58,'utf-8').'..';
								}
								$row['date'] = join('.',array_reverse(explode('-',substr($row['date'],0,10))));
								$row['eye']  = ($row['is_show']) ? 'eye_show' : 'eye_hide';
								$photos['listing'][] = $row;
							}
							$photos['pid']       = $pid;
							$photos['block']     = 'files';
							$photos['module_id'] = $mid;
							echo $html->render('submodules/files_list.html', $photos);
						} else {
							mysql_query('DELETE FROM files WHERE id = '.(int)$id);
						}
					}
					db_disconnect();
				}
			}
			break;
	}
	die();
?>