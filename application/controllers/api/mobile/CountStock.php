<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class CountStock extends REST_Controller
{

    protected $MenuId = 'CountStockMobile';

    public function __construct()
    {

        parent::__construct();

        // Load CountStock_Model
        $this->load->model('mobile/CountStock_Model');

    }

    /**
     * Show CountStock All API
     * ---------------------------------
     * @method : GET
     * @link : count_stock/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // CountStock Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load CountStock Function
            $output = $this->CountStock_Model->select_count_stock();

            if (isset($output) && $output) {

                // Show CountStock All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show count stock all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show CountStock All Error
                $message = [
                    'status' => false,
                    'message' => 'Count Stock data was not found in the database',
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
     * Update CountStock API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : count_stock/update
     */
    public function update_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('CountStock_ID', 'CountStock_ID', 'trim|required');

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

            // CountStock Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $count_stock_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $count_stock_permission = array_filter($count_stock_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($count_stock_permission[array_keys($count_stock_permission)[0]]['Updated']) {

                    $count_stock_data['where'] = [
                        'CountStock_ID' =>  $this->input->post('CountStock_ID')
                    ];

                    $count_stock_data['data'] = [
                        'status' => 9,
                        'Update_By' => $count_stock_token['UserName'],
                        'Update_Date' => date('Y-m-d H:i:s'),
                    ];

                    // Update CountStock Function
                    $count_stock_output = $this->CountStock_Model->update_count_stock($count_stock_data);

                    if (isset($count_stock_output) && $count_stock_output) {

                        // Update CountStock Success
                        $message = [
                            'status' => true,
                            'message' => 'Update Count Stock Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);

                    } else {

                        // Update CountStock Error
                        $message = [
                            'status' => false,
                            'message' => 'Update Count Stock Fail : [Update Data Fail]',
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
     * Show CountStock Item API
     * ---------------------------------
     * @method : GET
     * @link : count_stock/item
     */
    public function item_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // CountStock Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load CountStock Function
            $output = $this->CountStock_Model->select_count_stock_item($this->input->get('CountStock_ID'));

            if (isset($output) && $output) {

                // Show CountStock All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show count stock item successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show CountStock All Error
                $message = [
                    'status' => false,
                    'message' => 'Count Stock Item data was not found in the database',
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
     * Exec CountStock Item API
     * ---------------------------------
     * @param: FormData
     * ---------------------------------
     * @method : POST
     * @link : count_stock/exec_item
     */
    public function exec_item_post()
    {

        header("Access-Control-Allow-Origin: *");

        # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
        $_POST = $this->security->xss_clean($_POST);

        # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
        $this->form_validation->set_rules('CountStock_ID', 'CountStock_ID', 'trim|required');
        $this->form_validation->set_rules('QR_NO', 'QR_NO', 'trim|required');
        $this->form_validation->set_rules('Item_ID', 'Item_ID', 'trim|required');

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

            // CountStock Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $count_stock_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $count_stock_permission = array_filter($count_stock_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                if ($count_stock_permission[array_keys($count_stock_permission)[0]]['Created']) {

                    $tag_data = [
                        'CountStock_ID' => intval($this->input->post('CountStock_ID')),
                        'QR_NO' => $this->input->post('QR_NO'),
                        'Item_ID' => intval($this->input->post('Item_ID')),
                        'Username' => $count_stock_token['UserName'],
                    ];

                    // Exec CountStock Item Function
                    $count_stock_output = $this->CountStock_Model->exec_count_stock_transaction($tag_data);

                    if (isset($count_stock_output) && $count_stock_output) {

                        if(boolval($count_stock_output[0]['Result_status']) === true)
                        {
                        
                            // Exec CountStock Item Success
                            $message = [
                                'status' => true,
                                'message' => $count_stock_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_OK);
                        }
                        else
                        {
                             // Exec CountStock Item Error Condition
                             $message = [
                                'status' => false,
                                'message' => $count_stock_output[0]['Result_Desc'],
                            ];

                            $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }

                    } else {

                        // Exec CountStock Item Error
                        $message = [
                            'status' => false,
                            'message' => 'Exec Item Fail : [Exec Data Fail]',
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
