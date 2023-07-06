<?php

include "js/repositorio.php";



 $sql = "SELECT * FROM dbo.tabelaTeste";
 
 $reposit = new reposit();
 $result = $reposit->RunQuery($sql);

    foreach($result as $row){
        $descricao = $row['descricao'];
        echo $descricao. " "; 
    }
    echo (".");
    //só pra testar, criei um banco chamado TESTE
    


?>