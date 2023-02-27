<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_Model extends MY_Model
{

    /**
     * User
     * ---------------------------------
     * @param : null
     */
    public function select_user()
    {

        $this->set_db('default');

        $sql = "
           select Title+' '+FirstName+' '+LastName as FullName ,se_User.*,se_Group.Id as GroupID from se_User inner join se_Group on se_User.Group_Index = se_Group.Group_Index
        ";

        $query = $this->db->query($sql);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Insert User
     * ---------------------------------
     * @param : FormData
     */
    public function insert_user($param = [])
    {
        $this->set_db('default');

        return ($this->db->insert('se_User', $param['data'])) ? true : false/*$this->db->error()*/;

    }

     /**
     * Update User
     * ---------------------------------
     * @param : FormData
     */
    public function update_user($param = [])
    {
        $this->set_db('default');

        return ($this->db->update('se_User', $param['data'], ['User_Index'=> $param['index']])) ? true : false/*$this->db->error()*/;

    }

     /**
     * Delete User
     * ---------------------------------
     * @param : User_Index
     */
    public function delete_user($param = [])
    {
        $this->set_db('default');

        $this->db->trans_begin();

        $this->db->delete('se_UserPermission', ['User_Index'=> $param['index']]);

        $this->db->delete('se_User', ['User_Index'=> $param['index']]);

        return $this->check_begintrans()/*$this->db->error()*/;

    }


}
