<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		extract($_POST);
	
		// Check in the 'users' table
		$qry = $this->conn->query("SELECT * FROM users WHERE username = '$username' AND password = MD5('$password')");
		if($qry->num_rows > 0){
			
			$user = $qry->fetch_assoc();
			$this->handleLogin($user);
			return json_encode(array('status'=>'success'));
		} else {
			$qry_user1 = $this->conn->query("SELECT * FROM users1 WHERE username = '$username' AND password = MD5('$password')");
			if($qry_user1->num_rows > 0){
				
				$user = $qry_user1->fetch_assoc();
				$this->handleLogin($user);
				return json_encode(array('status'=>'success'));
			} else {
				return json_encode(array('status'=>'incorrect','last_qry'=>"Both users tables checked."));
			}
		}
	}
	
	private function handleLogin($user){
		foreach($user as $k => $v){
			if(!is_numeric($k) && $k != 'password'){
				$this->settings->set_userdata($k,$v);
			}
		}
		$this->settings->set_userdata('login_type', 1);
	}
	
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	default:
		echo $auth->index();
		break;
}

