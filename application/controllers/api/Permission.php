<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Permission extends REST_Controller
{

    protected $MenuId = 'Permission';

    public function __construct()
    {

        parent::__construct();

        // Load Permission_Model
        $this->load->model('Permission_Model');

    }

    /**
     * Show Permission By Condition API
     * ---------------------------------
     * @method : GET
     * @link : permission/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // Permission Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

            $User_Group = $this->input->get('User_Group');
            $User_Group_Value = $this->input->get('User_Group_Value');
            $Platform = $this->input->get('Platform');

            // Load Permission Function
            $output = $User_Group == 'User' ? $this->Permission_Model->select_user_permission($User_Group_Value, $Platform) : $this->Permission_Model->select_group_permission($User_Group_Value, $Platform);

            if (isset($output) && $output) {

                // Show Permission By Condition Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show permission by condition successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show Permission By Condition Error
                $message = [
                    'status' => false,
                    'message' => 'Permission data was not found in the database',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

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
     * Create Permission API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : permission/create
     */
    public function create_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Platform', 'Platform', 'trim|required');
        $this->form_validation->set_rules('User_Group', 'User_Group', 'trim|required');
        $this->form_validation->set_rules('User_Group_Value', 'User_Group_Value', 'trim|required');

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

            // Permission Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $permission_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $permission_permission = array_filter($permission_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($permission_permission[array_keys($permission_permission)[0]]['Created']) {

                    $permission_data['items'] = json_decode($this->input->post('Items'), true);

                    $permission_data['filter'] = [
                        'Platform' => $this->input->post('Platform'),
                        'User_Group' => $this->input->post('User_Group'),
                        'User_Group_Value' => $this->input->post('User_Group_Value'),
                        'AddBy' => $permission_token['UserName'],
                        'AddDate' => date('Y-m-d H:i:s'),
                    ];

                    // Create Permission Function
                    $permission_output = $this->input->post('User_Group') == 'User' ? $this->Permission_Model->insert_user_permission($permission_data) : $this->Permission_Model->insert_group_permission($permission_data);

                    if (isset($permission_output) && $permission_output) {

                        // Create Permission Success
                        $message = [
                            'status' => true,
                            'message' => 'Create Permission Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Create Permission Error
                        $message = [
                            'status' => false,
                            'message' => 'Create Permission Fail : [Insert Data Fail]',
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

}
