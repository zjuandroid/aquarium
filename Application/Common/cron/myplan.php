<?php
/**
 * Created by PhpStorm.
 * User: chwang
 * Date: 2016/6/19
 * Time: 18:33
 */
file_put_contents("hahaha.txt", date("Y-m-d H:i:s") . ' '.rand(0, 50)."\r\n", FILE_APPEND);