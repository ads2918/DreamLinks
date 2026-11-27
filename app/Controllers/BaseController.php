<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LinkModel; 
use App\Models\UserModel; 
use Psr\Log\LoggerInterface;
use CodeIgniter\Email\Email;
/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
	

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
	 
    protected $request;
	protected $current_user;
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];
	/** Where contact messages and reports go. */
	public const ADMIN_EMAIL = 'dreamlinks1111@gmail.com';
	protected $admin_email;
	protected $upload_path;

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);   
		// Preload any models, libraries, etc, here.
		$this->admin_email = self::ADMIN_EMAIL;
        $this->session = service('session');
		
		$session = \Config\Services::session();
		$user_id = $session->user_id;
		$model = model(LinkModel::class);
		$links = $model->user_pending_links($user_id);
		$user_model = model(UserModel::class);
		$current_user = $user_model->get_record_by_id($session->user_id);
		$this->upload_path = env('uploads.upload_path');
		$this->current_user = $current_user;
		$globalData = [
            'pending_links' => $links,
			'current_user' => $current_user,
        ];
		$this->view = \Config\Services::renderer();
        $this->view->setData($globalData);

    }
	
	public function logged_in(){
		if(isset($this->session->user_id) && is_numeric($this->session->user_id)){
			return true;
		}else{
			return false;
		}
	}
	
	public function send_email($to,$subject,$message,$debug = 0){	
		$email = service('email'); // Load the email service
		$email->setFrom($this->admin_email, 'dreamlinks.com'); //

        $email->setTo($to); // Recipient's email
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send(false)) {
            return 1;
        } else {
			// to the log, never the page: the SMTP chatter is not for visitors
			if($debug){
				log_message('error', 'Email "{subject}" failed: {debug}', ['subject' => $subject, 'debug' => $email->printDebugger(['headers'])]);
			}
			return false;
        }
	}
 
}
