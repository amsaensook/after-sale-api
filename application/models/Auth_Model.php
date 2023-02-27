<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_Model extends MY_Model {

    /**
     * Login
     * ---------------------------------
     * @param : {array} username, password
     */
    public function select_login($param = []){

        $this->set_db('default');

        $sql = "

            declare @username varchar(50)
            declare @password varchar(50)

            set @username = ?
            set @password = ?

            select *

            from 	se_User u left join se_Group g on u.Group_Index = g.Group_Index and g.IsUse = 1

            where 	UserName = @username COLLATE Latin1_General_CS_AS and CurrentPassword = @password

        ";

        $query = $this->db->query($sql, [ $param['username'],md5($param['password']) ]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Permission
     * ---------------------------------
     * @param : {array} username, password
     */
    public function select_permission($param = [])
    {
        $this->set_db('default');

        $sql = "

            exec SP_Permission ?,?,?
          
        ";
        
        $query = $this->db->query($sql, [ $param['username'],md5($param['password']),$param['platform'] ]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

}