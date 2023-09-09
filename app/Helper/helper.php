<?php

function createStr(){
    $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZA';
    return $str[mt_rand(0,26)].$str[mt_rand(0,26)].$str[mt_rand(0,26)].$str[mt_rand(0,26)];
}



?>