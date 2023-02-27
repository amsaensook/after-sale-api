<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Auth extends REST_Controller
{

  public function __construct()
  {

    parent::__construct();

    // Load Auth_Model
    $this->load->model('Auth_Model');
  }

  /**
   * Login API
   * ---------------------------------
   * @param: username
   * @param: password
   * ---------------------------------
   * @method : POST
   * @link : auth/login
   */
  public function login_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('username', 'username', 'trim|required');
    $this->form_validation->set_rules('password', 'password', 'trim|required');
    $this->form_validation->set_rules('platform', 'platform', 'trim|required');

    if ($this->form_validation->run() == false) {
      // Form Validation Error
      $message = [
        'status' => false,
        'error' => $this->form_validation->error_array(),
        'message' => validation_errors(),
      ];

      $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
    } else {
      $login_data = [
        'username' => $this->input->post('username'),
        'password' => $this->input->post('password'),
        'platform' => $this->input->post('platform'), // WA = Web Application, MA = Mobile Application
      ];

      // Load Login Function
      $login_output = $this->Auth_Model->select_login($login_data);

      if (isset($login_output) && $login_output) {
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // Get Permission
        $permission_output = $this->Auth_Model->select_permission($login_data);

        // Generate Token
        $token_data = [
          'id' => $login_output[0]['User_Index'], //Recommend for Token
          'EmpCode' => $login_output[0]['Id'],
          'UserName' => $login_output[0]['UserName'],
          'FirstName' => $login_output[0]['FirstName'],
          'LastName' => $login_output[0]['LastName'],
          'GroupName' => $login_output[0]['Name'],
          'permission' => (isset($permission_output) && $permission_output) ? $permission_output : null,
          'time' => time(), //Recommend for Token
        ];

        $user_token = $this->authorization_token->generateToken($token_data);

        $return_data = [
          'id' => $login_output[0]['User_Index'],
          'EmpCode' => $login_output[0]['Id'],
          'UserName' => $login_output[0]['UserName'],
          'FirstName' => $login_output[0]['FirstName'],
          'LastName' => $login_output[0]['LastName'],
          'GroupName' => $login_output[0]['Name'],
          'permission' => (isset($permission_output) && $permission_output) ? $permission_output : null,
          'token' => $user_token,
        ];

        // Login Success
        $message = [
          'status' => true,
          'data' => $return_data,
          'message' => 'User login successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {
        // Login Error
        $message = [
          'status' => false,
          'message' => 'Invalid username or password',
        ];

        $this->response($message, REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  /**
   * Validate Token API
   * ---------------------------------
   * @method : GET
   * @link : auth/validate_token
   */
  public function validate_token_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // User Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
      // Validate User Token Success
      $message = [
        'status' => true,
        'message' => 'Validate token successful',
      ];

      $this->response($message, REST_Controller::HTTP_OK);
    } else {
      // Validate User Token Error
      $message = [
        'status' => false,
        'message' => $is_valid_token['message'],
      ];

      $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
    }
  }

  /**
   * Refresh Token API
   * ---------------------------------
   * @method : GET
   * @link : auth/refresh_token
   */
  public function refresh_token_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // User Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

      $old_data = (array)($this->authorization_token->userData());

      // Generate Token
      $token_data = [
        'id' => $old_data['id'], //Recommend for Token
        'EmpCode' => $old_data['EmpCode'],
        'UserName' => $old_data['UserName'],
        'FirstName' => $old_data['FirstName'],
        'LastName' => $old_data['LastName'],
        'permission' => $old_data['permission'],
        'time' => time(), //Recommend for Token
      ];

      $user_token = $this->authorization_token->generateToken($token_data);

      $return_data = [
        'id' => $old_data['id'],
        'EmpCode' => $old_data['EmpCode'],
        'UserName' => $old_data['UserName'],
        'FirstName' => $old_data['FirstName'],
        'LastName' => $old_data['LastName'],
        'permission' => $old_data['permission'],
        'token' => $user_token,
      ];

      // Refresh Token Success
      $message = [
        'status' => true,
        'data' => $return_data,
        'message' => 'Refresh token successful',
      ];

      $this->response($message, REST_Controller::HTTP_OK);
    } else {
      // Validate User Token Error
      $message = [
        'status' => false,
        'message' => $is_valid_token['message'],
      ];

      $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
    }
  }
}
