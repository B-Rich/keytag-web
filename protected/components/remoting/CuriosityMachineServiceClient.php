<?php

class CuriosityMachineServiceClient {
    const connectAction = "UserWebService.Connect";
    const loginAction = "UserWebService.Authenticate";
    const logoutAction = "UserWebService.LogOut";
    const IonGetByIdAction = "UserWebService.GetUserInfoForIONById";
    const IonGetByPersonalInfoAction = "UserWebService.GetUserInfoForIONByCriteria";
    const IonSave = "UserWebService.CreateUserFromION";
    
    private $amfClient;
    public $LoggedIn;
    
    public function __construct($endpoint){
        $this->amfClient = new Zend_Amf_Client($endpoint);
        $this->LoggedIn = false;
    }
    
    public function Connect(){
        $result = $this->amfClient->sendRequest(self::connectAction, array());
        $this->LoggedIn = $result->IsLoggedIn;
    }
    
    public function Login($username, $password){
        if (!$this->LoggedIn) {
            $result = $this->amfClient->sendRequest(self::loginAction, array($username, $password));
            $this->LoggedIn = $result->IsLoggedIn;
        }
    }
    
    public function Logout(){
        if ($this->LoggedIn) {
            $result = $this->amfClient->sendRequest(self::logoutAction, array());
            $this->LoggedIn = !$result;
        }
    }
    
    public function GetCMUserById($userId){
        $result = $this->amfClient->sendRequest(self::IonGetByIdAction, array($userId));
        return $result;
    }
    
    public function GetCMUserByInfo($firstName, $lastName, $birthDate){
        $result = $this->amfClient->sendRequest(self::IonGetByPersonalInfoAction, array($firstName, $lastName, $birthDate));
        return $result;
    }
    
    public function SaveCMUser($user){
        $result = $this->amfClient->sendRequest(self::IonSave, array($user));
        return $result;
    }
}

?>
