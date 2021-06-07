<?php
/**
 * 耦合使用 Database 物件進行資料庫驗證 username 與 email 是否已存在於資料庫
 */
class UserVeridator {

    private $error;
    /**
     * 驗證是否已登入
     */
	public function __construct() {
        $this->error = [];
    }
    public static function isLogin($Account){
      if($Account != ''){
        return true;
      }
      else{
        return false;
      }
    }

    /**
     * 驗證shop是否已存在資料庫中
     */
    public function isShopDuplicate($Shop){
		$result = Database::get()->execute('SELECT name FROM store WHERE name = :name', array(':name' => $Shop));
        if(isset($result[0]['name']) and !empty($result[0]['name'])){
          $this->error[] = 'name provided is already in use.';
          return false;
        }
		return true;
    }

    /**
     * 驗證帳號密碼是否可正確登入
     */
    public function loginVerification($Account, $password){
      $result = Database::get()->execute('SELECT * FROM members WHERE Account = :Account', array(':Account' => $Account));

      //echo $result[0]['UID'];
      if(isset($result[0]['UID']) and !empty($result[0]['UID'])){
        if(password_verify($password, $result[0]['password'])){
          return true;
        }
      }
      $this->error[]= 'Wrong Account or password or your account has not been activated.';
      return false;
    }

    /**
     * 可取出錯誤訊息字串陣列
     */
    public function getErrorArray(){
        return $this->error;
    }

    /**
     * 驗證二次密碼輸入是否相符
     */
    public function isPasswordMatch($password, $passwrodConfirm){
		if ($password != $passwrodConfirm){
            $this->error[] = 'Passwords do not match.';
            return false;
        }
		return true;
    }

    /**
     * 驗證帳號是否已存在於資料庫中
     */
    public function isAccountDuplicate($Account){
        $result = Database::get()->execute('SELECT Account FROM members WHERE Account = :Account', array(':Account' => $Account));
        if(isset($result[0]['Account']) and !empty($result[0]['Account'])){
          $this->error[] = 'Account provided is already in use.';
          return false;
        }
		return true;
    }  
}