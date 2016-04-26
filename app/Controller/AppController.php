<?php
session_start();
/* MN */

App::uses('Controller', 'Controller');
App::uses('ExceptionRenderer', 'Error');
App::uses('Sanitize', 'Utility');

/**
 * Application Controller
 *
 */
class AppController extends Controller
{

    public $components = array(
        'Session',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'users', 'action' => 'userRedirect'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login')

        ),
        'RequestHandler',

        'Cookie'
    );


    public $uses = array(
        'User',
        'Role',
        'Sitesetting'

    );

    public $userRoles = array();
    public $loggedUser = array();


    function beforeFilter()
    {

        $this->basicSetup();
        //$this->Session->renew();

        if ($this->params['prefix'] === 'admin') {

            $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => true);
        }
        if ($this->params['prefix'] === 'store') {

            $this->Auth->loginAction = array('controller' => 'users', 'action' => 'storeLogin', 'store' => true);
        }
        if ($this->params['prefix'] === 'customer') {

            $this->Auth->loginAction = array('controller' => 'users', 'action' => 'customerlogin', 'customer' => true);
        }

        $this->Auth->authorize = array('Controller');
        $this->beforeSave();
        parent::beforeFilter();

    }

	public function beforeSave($options = array()) {
		$this->data = Sanitize::clean($this->data,array(
            'remove_html' => 1
        ));
        
		return true;
	}


    ////////////////////////////////////////////////////////////////////

    public function basicSetup()
    {

        $this->siteUrl = 'https://' . $_SERVER['HTTP_HOST'];
        $this->set('siteUrl', $this->siteUrl);

        $this->siteName = 'https://' . $_SERVER['HTTP_HOST'];
        $this->set('siteName', $this->siteName);

        $this->siteSetting = $siteDetails = $this->Sitesetting->find('first');


        $status = $siteDetails['Sitesetting']['offline_status'];

        if ($status == 'Yes' && $this->params['prefix'] != 'admin') {

            $this->render('/Errors/construction');
        }


        $this->siteCurrency = $siteCurrency = $this->siteSetting['Country']['currency_symbol'];

        $loggedUser = $this->Auth->user();

        if (!empty($loggedUser)) {

            $this->loggedUser = $loggedUser;
        }

        $this->set('siteSetting', $this->siteSetting);

        Configure::write('Stripe.TestSecret', $siteDetails['Sitesetting']['stripe_secretkeyTest']);
        Configure::write('Stripe.LiveSecret', $siteDetails['Sitesetting']['stripe_secretkey']);
        Configure::write('Stripe.mode', $siteDetails['Sitesetting']['stripe_mode']);
        Configure::write('Stripe.currency', $siteDetails['Country']['currency_code']);
        Configure::write('Twilio.AccountSid', $siteDetails['Sitesetting']['sms_id']);
        Configure::write('Twilio.AuthToken', $siteDetails['Sitesetting']['sms_token']);
        Configure::write('Twilio.from', $siteDetails['Sitesetting']['sms_source_number']);

        Configure::write('Hybridauth', array(
                    "Google" => array("enabled" => true,
                        "keys" => array("id" => $siteDetails['Sitesetting']['google_api_id'],
                                        "secret" => $siteDetails['Sitesetting']['google_secret_key'])),
                    "Facebook" => array("enabled" => true,
                        "keys" => array("id" => $siteDetails['Sitesetting']['facebook_api_id'],
                                        "secret" => $siteDetails['Sitesetting']['facebook_secret_key']))));

        $publishKey = ($siteDetails['Sitesetting']['stripe_mode'] != 'Live') ?
                            $siteDetails['Sitesetting']['stripe_publishkeyTest'] :
                            $siteDetails['Sitesetting']['stripe_publishkey'];
        $this->mailChimpKey 	= $siteDetails['Sitesetting']['mailchimp_key'];
        $this->mailChimpListId 	= $siteDetails['Sitesetting']['mailchimp_list_id'];

        //Bucket
        $this->siteBucket = $siteBucket = Configure::read('CakeS3.bucket');
        $this->cdn = $cdn = Configure::read('CakeS3.cdn');

        date_default_timezone_set($this->siteSetting['Sitesetting']['site_timezone']);
        $language = ($this->siteSetting['Sitesetting']['default_language'] == 1) ? 'eng' : 'deu';
        Configure::write('Config.language', $language);

        $metaTitle = $siteDetails['Sitesetting']['meta_title'];
        $metakeywords = $siteDetails['Sitesetting']['meta_keywords'];
        $metaDescriptions = $siteDetails['Sitesetting']['meta_description'];

        $this->set(compact('siteCurrency', 'metaTitle', 'metakeywords', 'metaDescriptions', 'publishKey', 'siteBucket', 'cdn'));

        $this->set('loggedUser', $this->loggedUser);

        //If call ajax request assign AJAX layout
        if ($this->RequestHandler->isAjax()) {
            $this->layout = 'ajax';
        }

        /*if($this->Session->check('Auth.User')) {
            $sessionUser = $this->Session->read('Auth.User');
            // exit();

            $loggedAdminDetail = $this->User->find('all',array('conditions' => array('User.username' => $sessionUser['username'],'User.status' => 1)));

            if(!empty($loggedAdminDetail)) {
                $this->loggedAdmin = $loggedAdminDetail[0]['User'];
                        $this->User->id = $this->loggedAdmin['id'];
            } else {
                $this->Session->setFlash(__('Invalid Access', true), 'default', array('class' => 'error'));
                $this->redirect($this->Auth->logout());
            }
        }*/
        $this->set('loggedAdmin', $this->loggedAdmin);
        $this->set('loggedCheck', $loggedUser);
    }

    public function isAuthorized($user)
    {

        if (($this->params['prefix'] === 'admin') && ($user['role_id'] != 1)) {
            echo '<h3 class="form-title"> You are not authorized to access this Page </h3>
                    <a href="' . $this->siteUrl . '/users/logout/admin"> Click here to Logout  </a>';
            exit();
        }

        if (($this->params['prefix'] === 'store') && ($user['role_id'] != 2 && $user['role_id'] != 3)) {
            echo '<h3 class="form-title"> You are not authorized to access this Page </h3>
                    <a href="' . $this->siteUrl . '/users/logout/store"> Click here to Logout  </a>';
            exit();
        }

        if (($this->params['prefix'] === 'customer') && ($user['role_id'] != 4)) {
            echo '<h3 class="form-title"> You are not authorized to access this Page </h3>
                    <a href="' . $this->siteUrl . '/users/logout/customer"> Click here to Logout  </a>';
            exit();
        }

        return true;
    }
}
