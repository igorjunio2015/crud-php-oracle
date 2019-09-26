<?php  

require_once ('../LoginSoftExpert.php');
require_once ('../LoginSoftExpertDAO.php');
require_once ('../Database.php');

$db      = new Database();
$dao     = new LoginSoftExpertDAO($db);

$LoginSoftExpert = new LoginSoftExpert();
$LoginSoftExpert->setIdEmpresa($idEmpresa);
$LoginSoftExpert->setChapa($chapa);
$LoginSoftExpert->setLmover($lmover);

$dao->add($produto); // aqui grava o resultado enviado do form

redirect('Location:index.php');

