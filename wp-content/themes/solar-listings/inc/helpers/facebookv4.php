<?php
session_start();
// define('FACEBOOK_SDK_V4_SRC_DIR', '/facebook-php-sdk-v4/src/Facebook/');
// require __DIR__ . '/facebook-php-sdk-v4/autoload.php';
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/facebook-php-sdk-v4/src/');

require_once 'Facebook/FacebookSession.php';
require_once 'Facebook/FacebookRequest.php';
require_once 'Facebook/FacebookSDKException.php';
require_once 'Facebook/FacebookRequestException.php';
require_once 'Facebook/FacebookAuthorizationException.php';
require_once 'Facebook/Entities/AccessToken.php';
require_once 'Facebook/HttpClients/FacebookHttpable.php';
require_once 'Facebook/HttpClients/FacebookStreamHttpClient.php';
require_once 'Facebook/HttpClients/FacebookStream.php';
require_once 'Facebook/HttpClients/FacebookCurlHttpClient.php';
require_once 'Facebook/HttpClients/FacebookCurl.php';
require_once 'Facebook/GraphObject.php';
require_once 'Facebook/GraphUser.php';
require_once 'Facebook/FacebookResponse.php';
require_once 'Facebook/FacebookServerException.php';
require_once 'Facebook/FacebookRedirectLoginHelper.php';


use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;

class Facebook
{
    
    private $appId = '492232084268237';
    private $appSecret = '385fc549fe09bc9def0e34d5d8837df6';
    private $required_scope = 'public_profile, publish_actions, email, user_birthday'; //Permissions required
    private $redirect_url = 'https://www.australiansolarquotes.com.au/';
    private $login_url;
    private $user_profile;

    public function __construct()
    {
        FacebookSession::setDefaultApplication($this->appId , $this->appSecret);
    }

    public function authenticate()
    {
        $helper = new FacebookRedirectLoginHelper($this->redirect_url);

        try {
            $session = $helper->getSessionFromRedirect();
        } catch(FacebookRequestException $ex) {
            die(" Error : " . $ex->getMessage());
        } catch(\Exception $ex) {
            die(" Error : " . $ex->getMessage());
        }
        
        if($session)
        {
            $user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
            $user_profile = $user_profile->asArray();
            if(isset($user_profile['email'])) {
           		//Insert user information from facebook
           		$this->user_profile = $user_profile;
            	return true;
            } else {
                return false;
            } 
        } else {
            $this->login_url = $helper->getLoginUrl( array( 'scope' => $this->required_scope ) );
            return false;
        }

        return true;
    }
    
    public function getLoginUrl()
    {
        return $this->login_url;
    }

    public function setRedirectUrl($redirect_url)
    {
    	$this->redirect_url = $redirect_url;
    }

    public function getUserProfile()
    {
    	return $this->user_profile;
    }
}