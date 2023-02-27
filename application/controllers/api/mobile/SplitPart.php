<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class SplitPart extends REST_Controller
{

    protected $MenuId = 'SplitPartMobile';

    public function __construct()
    {

        parent::__construct();

        // Load SplitPart_Model
        $this->load->model('mobile/SplitPart_Model');

    }

    /**
     * Show SplitPart All API
     * ---------------------------------
     * @method : GET
     * @link : split_part/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // SplitPart Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load SplitPart Function
            $output = $this->SplitPart_Model->select_split_part();

            if (isset($output) && $output) {

                // Show SplitPart All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show split part all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show SplitPart All Error
                $message = [
                    'status' => false,
                    'message' => 'Split Part data was not found in the database',
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
     * Update SplitPart API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : split_part/update
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

            // SplitPart Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $split_part_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $split_part_permission = array_filter($split_part_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($split_part_permission[array_keys($split_part_permission)[0]]['Updated']) {

                    $split_part_data['where'] = [
                        'Rec_ID' =>  $this->input->post('Rec_ID')
                    ];

                    $split_part_data['data'] = [
                        'status' => 9,
                        'Update_By' => $split_part_token['UserName'],
                        'Update_Date' => date('Y-m-d H:i:s'),
                    ];

                    // Update SplitPart Function
                    $split_part_output = $this->SplitPart_Model->update_split_part($split_part_data);

                    if (isset($split_part_output) && $split_part_output) {

                        // Update SplitPart Success
                        $message = [
                            'status' => true,
                            'message' => 'Update Split Part Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update SplitPart Error
                        $message = [
                            'status' => false,
                            'message' => 'Update Split Part Fail : [Update Data Fail]',
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
     * Show SplitPart Item API
     * ---------------------------------
     * @method : GET
     * @link : split_part/item
     */
    public function item_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // SplitPart Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load SplitPart Function
            $output = $this->SplitPart_Model->select_split_part_item($this->input->get('Rec_ID'));

            if (isset($output) && $output) {

                // Show SplitPart All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show split part item successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show SplitPart All Error
                $message = [
                    'status' => false,
                    'message' => 'Split Part Item data was not found in the database',
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
     * Exec SplitPart Transaction API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : split_part/exec_transaction
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

            // SplitPart Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $split_part_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $split_part_permission = array_filter($split_part_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($split_part_permission[array_keys($split_part_permission)[0]]['Created']) {

                    $tag_data = [
                        'Rec_ID' => intval($this->input->post('Rec_ID')),
                        'QR_NO' => $this->input->post('QR_NO'),
                        'Tag_ID' => intval($this->input->post('Tag_ID')),
                        'Username' => $split_part_token['UserName'],
                    ];

                    // Exec SplitPart Transaction Function
                    $split_part_output = $this->SplitPart_Model->exec_split_part_transaction($tag_data);

                    if (isset($split_part_output) && $split_part_output) {

                        if(boolval($split_part_output[0]['Result_status']) === true)
                        {
                        
                            // Exec SplitPart Transaction Success
                            $message = [
                                'status' => true,
                                'message' => $split_part_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_OK);
                        }
                        else
                        {
                             // Exec SplitPart Transaction Error Condition
                             $message = [
                                'status' => false,
                                'message' => $split_part_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }

                    } else {

                        // Exec SplitPart Transaction Error
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
