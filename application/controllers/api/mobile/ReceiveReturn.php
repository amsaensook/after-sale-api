<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class ReceiveReturn extends REST_Controller
{

    protected $MenuId = 'ReceiveReturnMobile';

    public function __construct()
    {

        parent::__construct();

        // Load ReceiveReturn_Model
        $this->load->model('mobile/ReceiveReturn_Model');

    }

    /**
     * Show ReceiveReturn All API
     * ---------------------------------
     * @method : GET
     * @link : receive_return/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // ReceiveReturn Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load ReceiveReturn Function
            $output = $this->ReceiveReturn_Model->select_receive_return();

            if (isset($output) && $output) {

                // Show ReceiveReturn All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show receive return all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show ReceiveReturn All Error
                $message = [
                    'status' => false,
                    'message' => 'Receive Return data was not found in the database',
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
     * Update ReceiveReturn API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : receive_return/update
     */
    public function update_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Rec_ID', 'Rec_ID', 'trim|required');

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

            // ReceiveReturn Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $receive_return_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $receive_return_permission = array_filter($receive_return_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($receive_return_permission[array_keys($receive_return_permission)[0]]['Updated']) {

                    $receive_return_data['where'] = [
                        'Rec_ID' =>  $this->input->post('Rec_ID')
                    ];

                    $receive_return_data['data'] = [
                        'status' => 9,
                        'Update_By' => $receive_return_token['UserName'],
                        'Update_Date' => date('Y-m-d H:i:s'),
                    ];

                    // Update ReceiveReturn Function
                    $receive_return_output = $this->ReceiveReturn_Model->update_receive_return($receive_return_data);

                    if (isset($receive_return_output) && $receive_return_output) {

                        // Update ReceiveReturn Success
                        $message = [
                            'status' => true,
                            'message' => 'Update Receive Return Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update ReceiveReturn Error
                        $message = [
                            'status' => false,
                            'message' => 'Update Receive Return Fail : [Update Data Fail]',
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
     * Show ReceiveReturn Item API
     * ---------------------------------
     * @method : GET
     * @link : receive_return/item
     */
    public function item_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // ReceiveReturn Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load ReceiveReturn Function
            $output = $this->ReceiveReturn_Model->select_receive_return_item($this->input->get('Rec_ID'));

            if (isset($output) && $output) {

                // Show ReceiveReturn All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show receive return item successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show ReceiveReturn All Error
                $message = [
                    'status' => false,
                    'message' => 'Receive Return Item data was not found in the database',
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
     * Exec ReceiveReturn Transaction API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : receive_return/exec_transaction
     */
    public function exec_transaction_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('Rec_ID', 'Rec_ID', 'trim|required');
        $this->form_validation->set_rules('QR_NO', 'QR_NO', 'trim|required');
        $this->form_validation->set_rules('Tag_ID', 'Tag_ID', 'trim|required');

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

            // ReceiveReturn Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $receive_return_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $receive_return_permission = array_filter($receive_return_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($receive_return_permission[array_keys($receive_return_permission)[0]]['Created']) {

                    $tag_data = [
                        'Rec_ID' => intval($this->input->post('Rec_ID')),
                        'QR_NO' => $this->input->post('QR_NO'),
                        'Tag_ID' => intval($this->input->post('Tag_ID')),
                        'Username' => $receive_return_token['UserName'],
                    ];

                    // Exec ReceiveReturn Transaction Function
                    $receive_return_output = $this->ReceiveReturn_Model->exec_receive_return_transaction($tag_data);

                    if (isset($receive_return_output) && $receive_return_output) {

                        if(boolval($receive_return_output[0]['Result_status']) === true)
                        {
                        
                            // Exec ReceiveReturn Transaction Success
                            $message = [
                                'status' => true,
                                'message' => $receive_return_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_OK);
                        }
                        else
                        {
                             // Exec ReceiveReturn Transaction Error Condition
                             $message = [
                                'status' => false,
                                'message' => $receive_return_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }

                    } else {

                        // Exec ReceiveReturn Transaction Error
                        $message = [
                            'status' => false,
                            'message' => 'Exec Transaction Fail : [Exec Data Fail]',
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
