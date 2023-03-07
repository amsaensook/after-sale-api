<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Issue extends REST_Controller
{

  protected $MenuId = 'IssueMobile';

  public function __construct()
  {

    parent::__construct();

    // Load Issue_Model
    $this->load->model('mobile/Issue_Model');
  }

  /**
   * Show Issue All API
   * ---------------------------------
   * @method : GET
   * @link : issue/index
   */
  public function index_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // Issue Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
      // Load Issue Function
      $output = $this->Issue_Model->select_issue();

      if (isset($output) && $output) {

        // Show Issue All Success
        $message = [
          'status' => true,
          'data' => $output,
          'message' => 'Show Issue all successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {

        // Show Issue All Error
        $message = [
          'status' => false,
          'message' => 'Issue data was not found in the database',
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
   * Update Issue API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : issue/update
   */
  public function update_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('Withdraw_ID', 'Withdraw_ID', 'trim|required');

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

      // Issue Token Validation
      $is_valid_token = $this->authorization_token->validateToken();

      if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

        $issue_token = json_decode(json_encode($this->authorization_token->userData()), true);
        $issue_permission = array_filter($issue_token['permission'], function ($permission) {
          return $permission['MenuId'] == $this->MenuId;
        });

        if ($issue_permission[array_keys($issue_permission)[0]]['Updated']) {

          $issue_data['where'] = [
            'Withdraw_ID' =>  $this->input->post('Withdraw_ID')
          ];

          $issue_data['data'] = [
            'status' => 9,
            'Update_By' => $issue_token['UserName'],
            'Update_Date' => date('Y-m-d H:i:s'),
          ];

          // Update Issue Function
          $issue_output = $this->Issue_Model->update_issue($issue_data);

          if (isset($issue_output) && $issue_output) {

            // Update Issue Success
            $message = [
              'status' => true,
              'message' => 'Update Issue Successful',
            ];

            $this->response($message, REST_Controller::HTTP_OK);
          } else {

            // Update Issue Error
            $message = [
              'status' => false,
              'message' => 'Update Issue Fail : [Update Data Fail]',
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
   * Show Issue Item API
   * ---------------------------------
   * @method : GET
   * @link : issue/item
   */
  public function item_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // Issue Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
      // Load Issue Function
      $output = $this->Issue_Model->select_issue_item($this->input->get('Withdraw_ID'));

      if (isset($output) && $output) {

        // Show Issue All Success
        $message = [
          'status' => true,
          'data' => $output,
          'message' => 'Show Issue item successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {

        // Show Issue All Error
        $message = [
          'status' => false,
          'message' => 'Issue Item data was not found in the database',
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
   * Exec Issue Transaction API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : issue/exec_transaction
   */
  public function exec_transaction_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('Withdraw_ID', 'Withdraw_ID', 'trim|required');
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

      // Issue Token Validation
      $is_valid_token = $this->authorization_token->validateToken();

      if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

        $issue_token = json_decode(json_encode($this->authorization_token->userData()), true);
        $issue_permission = array_filter($issue_token['permission'], function ($permission) {
          return $permission['MenuId'] == $this->MenuId;
        });

        if ($issue_permission[array_keys($issue_permission)[0]]['Created']) {

          $tag_data = [
            'Withdraw_ID' => intval($this->input->post('Withdraw_ID')),
            'QR_NO' => $this->input->post('QR_NO'),
            'Tag_ID' => intval($this->input->post('Tag_ID')),
            'Create_Date' => date('Y-m-d H:i:s'),
            'Create_By' => $issue_token['UserName'],
          ];

          // Exec Issue Item Function
          $issue_item = $this->Issue_Model->check_issue_item($tag_data);

          if (isset($issue_item) && $issue_item) {

            if (boolval($issue_item[0]['Result_status']) === true) {

              // Exec Issue Transaction Function
              $issue_output = $this->Issue_Model->exec_issue_transaction($tag_data);

              if (isset($issue_output) && $issue_output) {

                // Exec Issue Transaction Success
                $message = [
                  'status' => true,
                  'message' => 'Insert Request Sale Service Successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);
              } else {

                // Exec Issue Transaction Error
                $message = [
                  'status' => false,
                  'message' => 'Insert Request Sale Service Fail : [Insert Data Fail]',
                ];

                $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
              }
            } else {

              // Exec Issue Item Error Condition
              $message = [
                'status' => false,
                'message' => $issue_item[0]['Result_Desc'],
              ];

              $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
          } else {

            // Exec Issue Item Error
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


  /**
     * STOCK BAL
     * ---------------------------------
     * @method : POST
     * @link : issue/stock_bal
     */
    public function stock_bal_post()
    {
        header("Access-Control-Allow-Origin: *");

        $_POST = $this->security->xss_clean($_POST);

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // Tag Token Validation
        $is_valid_token = $this->authorization_token->validateToken();


            $qrcode_data = [
                'QR_NO' => $this->input->post('QR_NO'),
                'Location_ID' => $this->input->post('Location_ID'),
               
            ];

            $qrcode_output = $this->Issue_Model->select_stockbal($qrcode_data);

            if (isset($qrcode_output) && $qrcode_output) {

                // Show Tag All Success
                $message = [
                    'status' => true,
                    'data' => $qrcode_output,
                    'message' => 'Show QR CODE successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            }

        // } else {
        //     // Validate Error
        //     $message = [
        //         'status' => false,
        //         'message' => $is_valid_token['message'],
        //     ];

        //     $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
        // }
    }


    /**
   * Create Issue API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : issue/create
   */
    public function create_post()
    {

        header("Access-Control-Allow-Origin: *");

        $_POST = $this->security->xss_clean($_POST);

        
            // Load Authorization Token Library
            $this->load->library('Authorization_Token');

            // Transfer Team Token Validation
            $is_valid_token = $this->authorization_token->validateToken();

            if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

                $tf_token = json_decode(json_encode($this->authorization_token->userData()), true);
                $tf_permission = array_filter($tf_token['permission'], function ($permission) {
                    return $permission['MenuId'] == $this->MenuId;
                });

                $TF_Quotation_No = $this->input->post('Quotation_No'); 

                if ($tf_permission[array_keys($tf_permission)[0]]['Created']) {

                    $data['items'] = json_decode($this->input->post('Item'), true);

                    $data['user'] = [
                        'Create_By' => $tf_token['UserName'],
                        'Create_Date' => date('Y-m-d H:i:s'),
                    ];
    
                    $withdraw_output = $this->Issue_Model->insert_issue_item($data);
    
                    if (isset($withdraw_output) && $withdraw_output) {

                        $message = [
                            'status' => true,
                            'message' => 'Issue Successful',
                        ];

                        $this->response($message, REST_Controller::HTTP_OK);



                    } else {

                        // Create Issue Error
                        $message = [
                            'status' => false,
                            'message' => 'Issue Fail : [Insert Data Fail]',
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
