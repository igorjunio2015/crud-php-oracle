<?php
class LoginSistema
{
    private $idEmpresa;
    private $chapa;
    private $lmover;

    public function getIdEmpresa(){
        return $this->idEmpresa;
    }

    public function setIdEmpresa($idEmpresa){
        $this->idEmpresa = $idEmpresa;
    }

    public function getChapa(){
        return $this->chapa;
    }

    public function setChapa($chapa){
        $this->chapa = $chapa;
    }

    public function getLover(){
        return $this->lmover;
    }

    public function setLmover($lmover){
        $this->lmover = $lmover;
    }
}
