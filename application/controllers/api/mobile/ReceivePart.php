<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class ReceivePart extends REST_Controller
{

    protected $MenuId = 'ReceivePartMobile';

    public function __construct()
    {

        parent::__construct();

        // Load ReceivePart_Model
        $this->load->model('mobile/ReceivePart_Model');

    }

    /**
     * Show ReceivePart All API
     * ---------------------------------
     * @method : GET
     * @link : receive_part/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // ReceivePart Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load ReceivePart Function
            $output = $this->ReceivePart_Model->select_receive_part();

            if (isset($output) && $output) {

                // Show ReceivePart All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show receive part all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show ReceivePart All Error
                $message = [
                    'status' => false,
                    'message' => 'Receive Part data was not found in the database',
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
     * Update ReceivePart API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : receive_part/update
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

            // ReceivePart Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $receive_part_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $receive_part_permission = array_filter($receive_part_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($receive_part_permission[array_keys($receive_part_permission)[0]]['Updated']) {

                    $receive_part_data['where'] = [
                        'Rec_ID' =>  $this->input->post('Rec_ID')
                    ];

                    $receive_part_data['data'] = [
                        'status' => 9,
                        'Update_By' => $receive_part_token['UserName'],
                        'Update_Date' => date('Y-m-d H:i:s'),
                    ];

                    // Update ReceivePart Function
                    $receive_part_output = $this->ReceivePart_Model->update_receive_part($receive_part_data);

                    if (isset($receive_part_output) && $receive_part_output) {

                        // Update ReceivePart Success
                        $message = [
                            'status' => true,
                            'message' => 'Update Receive Part Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update ReceivePart Error
                        $message = [
                            'status' => false,
                            'message' => 'Update Receive Part Fail : [Update Data Fail]',
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
     * Show ReceivePart Item API
     * ---------------------------------
     * @method : GET
     * @link : receive_part/item
     */
    public function item_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // ReceivePart Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load ReceivePart Function
            $output = $this->ReceivePart_Model->select_receive_part_item($this->input->get('Rec_ID'));

            if (isset($output) && $output) {

                // Show ReceivePart All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show receive part item successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show ReceivePart All Error
                $message = [
                    'status' => false,
                    'message' => 'Receive Part Item data was not found in the database',
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
     * Exec ReceivePart Transaction API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : receive_part/exec_transaction
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

            // ReceivePart Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $receive_part_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $receive_part_permission = array_filter($receive_part_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($receive_part_permission[array_keys($receive_part_permission)[0]]['Created']) {

                    $tag_data = [
                        'Rec_ID' => intval($this->input->post('Rec_ID')),
                        'QR_NO' => $this->input->post('QR_NO'),
                        'Tag_ID' => intval($this->input->post('Tag_ID')),
                        'Username' => $receive_part_token['UserName'],
                    ];

                    // Exec ReceivePart Transaction Function
                    $receive_part_output = $this->ReceivePart_Model->exec_receive_part_transaction($tag_data);

                    if (isset($receive_part_output) && $receive_part_output) {

                        if(boolval($receive_part_output[0]['Result_status']) === true)
                        {
                        
                            // Exec ReceivePart Transaction Success
                            $message = [
                                'status' => true,
                                'message' => $receive_part_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_OK);
                        }
                        else
                        {
                             // Exec ReceivePart Transaction Error Condition
                             $message = [
                                'status' => false,
                                'message' => $receive_part_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }

                    } else {

                        // Exec ReceivePart Transaction Error
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
