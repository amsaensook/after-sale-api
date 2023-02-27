<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller
{

    protected $MenuId = 'User';

    public function __construct()
    {

        parent::__construct();

        // Load User_Model
        $this->load->model('User_Model');

    }

    /**
     * Show User All API
     * ---------------------------------
     * @method : GET
     * @link : user/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // User Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load User Function
            $output = $this->User_Model->select_user();

            if (isset($output) && $output) {

                // Show User All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show user all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show User All Error
                $message = [
                    'status' => false,
                    'message' => 'User data was not found in the database',
                ];

                $this->response($message, REST_Controller::HTTP_NOT_FOUND);

            }

        } else {
            // Validate Error
            $message = [
                'status' => false,
                'message' => $is_valid_token['message'],
            ];

            $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
        }

    }

    /**
     * Create User API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : user/create
     */
    public function create_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Id', 'Id', 'trim|required');
        $this->form_validation->set_rules('UserName', 'UserName', 'trim|required');
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        $this->form_validation->set_rules('IsUse', 'IsUse', 'trim|required');

        if ($this->form_validation->run() == false) {
            // Form Validation Error
            $message = [
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors(),
            ];

            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        } else {

            // Load Authorization Token Library
            $this->load->library('Authorization_Token');

            // User Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $user_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $user_permission = array_filter($user_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($user_permission[array_keys($user_permission)[0]]['Created']) {

                    $user_data['data'] = [
                        'Id' => $this->input->post('Id'),
                        'UserName' => $this->input->post('UserName'),
                        'InitialPassword' => md5($this->input->post('Password')),
                        'CurrentPassword' => md5($this->input->post('Password')),
                        'Title' => $this->input->post('Title'),
                        'FirstName' => $this->input->post('FirstName'),
                        'LastName' => $this->input->post('LastName'),
                        'Email' => $this->input->post('Email'),
                        'IsUse' => intval($this->input->post('IsUse')),
                        'AddBy' => $user_token['UserName'],
                        'AddDate' => date('Y-m-d H:i:s'),
                        'UpdateBy' => null,
                        'UpdateDate' => null,
                        'CancelBy' => null,
                        'CancelDate' => null,
                        'Group_Index' => $this->input->post('Group_Index') ? intval($this->input->post('Group_Index')) : null,
                    ];

                    // Create User Function
                    $user_output = $this->User_Model->insert_user($user_data);

                    if (isset($user_output) && $user_output) {

                        // Create User Success
                        $message = [
                            'status' => true,
                            'message' => 'Create User Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Create User Error
                        $message = [
                            'status' => false,
                            'message' => 'Create User Fail : [Insert Data Fail]',
                        ];

                        $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {
                    // Permission Error
                    $message = [
                        'status' => false,
                        'message' => 'You don’t currently have permission to Create',
                    ];

                    $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
                }

            } else {
                // Validate Error
                $message = [
                    'status' => false,
                    'message' => $is_valid_token['message'],
                ];

                $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
            }

        }

    }

    /**
     * Update User API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : user/update
     */
    public function update_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Id', 'Id', 'trim|required');
        $this->form_validation->set_rules('UserName', 'UserName', 'trim|required');
        //$this->form_validation->set_rules('Password', 'Password', 'trim|required');
        $this->form_validation->set_rules('IsUse', 'IsUse', 'trim|required');

        if ($this->form_validation->run() == false) {
            // Form Validation Error
            $message = [
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors(),
            ];

            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        } else {

            // Load Authorization Token Library
            $this->load->library('Authorization_Token');

            // User Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $user_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $user_permission = array_filter($user_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($user_permission[array_keys($user_permission)[0]]['Updated']) {

                    $user_data['index'] = $this->input->post('User_Index');

                    $user_data['data'] = [
                        'Id' => $this->input->post('Id'),
                        'UserName' => $this->input->post('UserName'),
                        //'CurrentPassword' => md5($this->input->post('Password')),
                        'Title' => $this->input->post('Title'),
                        'FirstName' => $this->input->post('FirstName'),
                        'LastName' => $this->input->post('LastName'),
                        'Email' => $this->input->post('Email'),
                        'IsUse' => intval($this->input->post('IsUse')),
                        'UpdateBy' => $user_token['UserName'],
                        'UpdateDate' => date('Y-m-d H:i:s'),
                        'Group_Index' => $this->input->post('Group_Index') ? intval($this->input->post('Group_Index')) : null,
                    ];

                    if($this->input->post('Password'))
                    {
                        $user_data['data']['CurrentPassword'] =  md5($this->input->post('Password'));
                    }

                    // Update User Function
                    $user_output = $this->User_Model->update_user($user_data);

                    if (isset($user_output) && $user_output) {

                        // Update User Success
                        $message = [
                            'status' => true,
                            'message' => 'Update User Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update User Error
                        $message = [
                            'status' => false,
                            'message' => 'Update User Fail : [Update Data Fail]',
                        ];

                        $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {
                    // Permission Error
                    $message = [
                        'status' => false,
                        'message' => 'You don’t currently have permission to Update',
                    ];

                    $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
                }

            } else {
                // Validate Error
                $message = [
                    'status' => false,
                    'message' => $is_valid_token['message'],
                ];

                $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
            }

        }

    }

    /**
     * Delete User API
     * ---------------------------------
     * @param: User_Index
     * ---------------------------------
     * @method : POST
     * @link : user/delete
     */
    public function delete_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('User_Index', 'User_Index', 'trim|required');

        if ($this->form_validation->run() == false) {
            // Form Validation Error
            $message = [
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors(),
            ];

            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        } else {

            // Load Authorization Token Library
            $this->load->library('Authorization_Token');

            // User Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $user_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $user_permission = array_filter($user_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($user_permission[array_keys($user_permission)[0]]['Deleted']) {

                    $user_data['index'] = $this->input->post('User_Index');

                    // Delete User Function
                    $user_output = $this->User_Model->delete_user($user_data);

                    if (isset($user_output) && $user_output) {

                        // Delete User Success
                        $message = [
                            'status' => true,
                            'message' => 'Delete User Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Delete User Error
                        $message = [
                            'status' => false,
                            'message' => 'Delete User Fail : [Delete Data Fail]',
                        ];

                        $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

                    }

                } else {
                    // Permission Error
                    $message = [
                        'status' => false,
                        'message' => 'You don’t currently have permission to Delete',
                    ];

                    $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
                }

            } else {
                // Validate Error
                $message = [
                    'status' => false,
                    'message' => $is_valid_token['message'],
                ];

                $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
            }

        }

    }

}
