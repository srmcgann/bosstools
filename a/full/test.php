<?php
  require('../db.php');
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=intval($mul)*pow(62,$pow);
      $pow++;
    }
    return intval($res);
  }
  echo alphaToDec('2w5v') ."\n\n";

?>
