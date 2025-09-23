<?php
    class App{
        function __construct($array){
            
            if(isset($array["json"]["ctl"])){
                $ctl = $array["json"]["ctl"]."Controller";                
                new $ctl($array);
            }else{
                new MoveController($array);
            }
        }
    }
?>