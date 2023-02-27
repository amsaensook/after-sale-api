<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Group extends REST_Controller
{

    protected $MenuId = 'Group';

    public function __construct()
    {

        parent::__construct();

        // Load Group_Model
        $this->load->model('Group_Model');

    }

    /**
     * Show Group All API
     * ---------------------------------
     * @method : GET
     * @link : group/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // Group Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load Group Function
            $output = $this->Group_Model->select_group();

            if (isset($output) && $output) {

                // Show Group All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show group all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show Group All Error
                $message = [
                    'status' => false,
                    'message' => 'Group data was not found in the database',
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
     * Create Group API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : group/create
     */
    public function create_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Id', 'Id', 'trim|required');
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

            // Group Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $group_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $group_permission = array_filter($group_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($group_permission[array_keys($group_permission)[0]]['Created']) {

                    $group_data['data'] = [
                        'Id' => $this->input->post('Id'),
                        'Name' => $this->input->post('Name'),
                        'Des' => $this->input->post('Des'),
                        'IsUse' => intval($this->input->post('IsUse')),
                        'AddBy' => $group_token['UserName'],
                        'AddDate' => date('Y-m-d H:i:s'),
                        'UpdateBy' => null,
                        'UpdateDate' => null,
                        'CancelBy' => null,
                        'CancelDate' => null,
                    ];

                    // Create Group Function
                    $group_output = $this->Group_Model->insert_group($group_data);

                    if (isset($group_output) && $group_output) {

                        // Create Group Success
                        $message = [
                            'status' => true,
                            'message' => 'Create Group Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Create Group Error
                        $message = [
                            'status' => false,
                            'message' => 'Create Group Fail : [Insert Data Fail]',
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
     * Update Group API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : group/update
     */
    public function update_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Id', 'Id', 'trim|required');
        $this->form_validation->set_rules('IsUse', 'IsUse', 'trim|required');

        if ($this->form_validation->run() == false) {
            // Form Validation Error
            $message = [
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors(),
            ];

            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {

            // Load Authorization Token Library
            $this->load->library('Authorization_Token');

            // Group Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $group_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $group_permission = array_filter($group_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($group_permission[array_keys($group_permission)[0]]['Updated']) {

                    $group_data['index'] = $this->input->post('Group_Index');

                    $group_data['data'] = [
                        'Id' => $this->input->post('Id'),
                        'Name' => $this->input->post('Name'),
                        'Des' => $this->input->post('Des'),
                        'IsUse' => intval($this->input->post('IsUse')),
                        'UpdateBy' => $group_token['UserName'],
                        'UpdateDate' => date('Y-m-d H:i:s'),
                    ];

                    // Update Group Function
                    $group_output = $this->Group_Model->update_group($group_data);

                    if (isset($group_output) && $group_output) {

                        // Update Group Success
                        $message = [
                            'status' => true,
                            'message' => 'Update Group Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update Group Error
                        $message = [
                            'status' => false,
                            'message' => 'Update Group Fail : [Update Data Fail]',
                        ];

                        $this->response($message, REST_Controller::HTTP_NOT_FOUND);

                    }

                } else {
                    // Permission Error
                    $message = [
                        'status' => false,
                        'message' => 'You don’t currently have permission to Update',
                    ];

                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }

            } else {
                // Validate Error
                $message = [
                    'status' => false,
                    'message' => $is_valid_token['message'],
                ];

                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }

        }

    }

    /**
     * Delete Group API
     * ---------------------------------
     * @param: Group_Index
     * ---------------------------------
     * @method : POST
     * @link : group/delete
     */
    public function delete_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Group_Index', 'Group_Index', 'trim|required');

        if ($this->form_validation->run() == false) {
            // Form Validation Error
            $message = [
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors(),
            ];

            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {

            // Load Authorization Token Library
            $this->load->library('Authorization_Token');

            // Group Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $group_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $group_permission = array_filter($group_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($group_permission[array_keys($group_permission)[0]]['Deleted']) {

                    $group_data['index'] = $this->input->post('Group_Index');

                    // Delete Group Function
                    $group_output = $this->Group_Model->delete_group($group_data);

                    if (isset($group_output) && $group_output) {

                        // Delete Group Success
                        $message = [
                            'status' => true,
                            'message' => 'Delete Group Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Delete Group Error
                        $message = [
                            'status' => false,
                            'message' => 'Delete Group Fail : [Delete Data Fail]',
                        ];

                        $this->response($message, REST_Controller::HTTP_NOT_FOUND);

                    }

                } else {
                    // Permission Error
                    $message = [
                        'status' => false,
                        'message' => 'You don’t currently have permission to Delete',
                    ];

                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }

            } else {
                // Validate Error
                $message = [
                    'status' => false,
                    'message' => $is_valid_token['message'],
                ];

                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }

        }

    }

}
