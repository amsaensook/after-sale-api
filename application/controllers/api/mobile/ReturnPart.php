<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class ReturnPart extends REST_Controller
{

    protected $MenuId = 'ReturnPartMobile';

    public function __construct()
    {

        parent::__construct();

        // Load ReturnPart_Model
        $this->load->model('mobile/ReturnPart_Model');

    }

    /**
     * Update ReturnPart API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : return_part/update
     */
    public function update_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Items', 'Items', 'trim|required');

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

            // ReturnPart Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $return_part_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $return_part_permission = array_filter($return_part_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($return_part_permission[array_keys($return_part_permission)[0]]['Created']) {

                    $data['items'] = json_decode($this->input->post('Items'), true);

                    $data['user'] = [
                        'Create_By' => $return_part_token['UserName'],
                        'Create_Date' => date('Y-m-d H:i:s'),
                    ];

                    // Update ReturnPart Function
                    $return_part_output = $this->ReturnPart_Model->update_return_part($data);

                    if (isset($return_part_output) && $return_part_output) {

                        // Update ReturnPart Success
                        $message = [
                            'status' => true,
                            'message' => 'Update Return Part Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update ReturnPart Error
                        $message = [
                            'status' => false,
                            'message' => 'Update Return Part Fail : [Update Data Fail]',
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

}
