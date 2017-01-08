<?php
$typeArr = array("jpg", "png", "gif");//允许上传文件格式
$path = "Uploads/";//上传路径

define('ATTACK_LOG_DIR', '');
function saveLog()
{

}

if (isset($_POST)) {
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];
    $name_tmp = $_FILES['file']['tmp_name'];
    if (empty($name)) {
        echo json_encode(array("error"=>"您还未选择图片"));
        exit;
    }
    $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型
    if (!in_array($type, $typeArr)) {
        echo json_encode(array("error"=>"清上传jpg,png或gif类型的图片！"));
        exit;
    }
    if ($size > (5000 * 1024)) {
        echo json_encode(array("error"=>"图片大小已超过5000KB！"));
        exit;
    }
    $pic_name = time() . rand(10000, 99999) . "." . $type;//图片名称
    $pic_url = $path . $pic_name;//上传后图片路径+名称
    //禁止上传php
    $content = file_get_contents($name_tmp);
    if(strpos($content,'?php') != false || strpos($content,'eval') != false || strpos($content,'base') != false){
        /*
        TODO::保存索引文件以及详情
        file_put_contents(ATTACK_LOG_DIR . $filename, '[time]filepath exception'.PHP_EOL, FILE_APPEND)
        */
        $filename = rand(10000000,9999999999);
        file_put_contents('./'.$filename.'.txt', $name.'_'.date('YmdHis').'.txt'.PHP_EOL, FILE_APPEND);
        file_put_contents('./Public/'.$filename.'______'.$name.'_'.date('YmdHis').'.txt', $content);
        unlink($name_tmp);
        die;
    }
    // TODO::首先需要判断$path是否可写
    if (move_uploaded_file($name_tmp, $pic_url)) { //临时文件转移到目标文件夹
        echo json_encode(array("error"=>"0","pic"=>$pic_url,"name"=>$pic_name));
    } else {
        echo json_encode(array("error"=>"上传有误，清检查服务器配置！"));
    }
}

?>