<?php defined('BASEPATH') or exit('No direct script access allowed');

class Permission_Model extends MY_Model
{

    /**
     * User Permission
     * ---------------------------------
     * @param : null
     */
    public function select_user_permission($UserName = null, $Platform = null)
    {

        $this->set_db('default');

        $sql = "

            exec SP_UserPermission ?,?

        ";

        $query = $this->db->query($sql, [$UserName, $Platform]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Group Permission
     * ---------------------------------
     * @param : null
     */
    public function select_group_permission($Group_Index = null, $Platform = null)
    {

        $this->set_db('default');

        $sql = "

            exec SP_GroupPermission ?,?

        ";

        $query = $this->db->query($sql, [$Group_Index, $Platform]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Insert User Permission
     * ---------------------------------
     * @param : FormData
     */
    public function insert_user_permission($param = [])
    {
        $this->set_db('default');

        $this->db->trans_begin();

        $sql = "

            delete from se_UserPermission where UserPermission_Index in ( 
                select  UserPermission_Index 
                from    se_UserPermission up inner join se_Menu menu on up.Menu_Index = menu.Menu_Index
                        inner join se_Platform pf on menu.Platform_Index = pf.Platform_Index
                where   pf.Id = ? and User_Index = ?
            )

        ";

        $this->db->query($sql, [$param['filter']['Platform'], $param['filter']['User_Group_Value']]);

        foreach ($param['items'] as $value) {

            $sum_permission = intval($value['Created']) + intval($value['Readed']) + intval($value['Updated']) + intval($value['Deleted']) + intval($value['Exported']) + intval($value['Printed']) + intval($value['Approved1']) + intval($value['Approved2']);

            //can't create permission if sum of permission is 0
            if ($sum_permission > 0) {

                $data = [
                    'Created' => $value['Created'],
                    'Readed' => $value['Readed'],
                    'Updated' => $value['Updated'],
                    'Deleted' => $value['Deleted'],
                    'Exported' => $value['Exported'],
                    'Printed' => $value['Printed'],
                    'Approved1' => $value['Approved1'],
                    'Approved2' => $value['Approved2'],
                    'AddBy' => $param['filter']['AddBy'],
                    'AddDate' => $param['filter']['AddDate'],
                    'UpdateBy' => null,
                    'UpdateDate' => null,
                    'CancelBy' => null,
                    'CancelDate' => null,
                    'User_Index' => $param['filter']['User_Group_Value'],
                    'Menu_Index' => $value['Menu_Index'],
                ];

                $this->db->insert('se_UserPermission', $data);

            }

        }

        return $this->check_begintrans(); /*$this->db->error()*/;

    }

    /**
     * Insert Group Permission
     * ---------------------------------
     * @param : FormData
     */
    public function insert_group_permission($param = [])
    {
        $this->set_db('default');

        $this->db->trans_begin();

        $sql = "

            delete from se_GroupPermission where GroupPermission_Index in ( 
                select  GroupPermission_Index 
                from    se_GroupPermission up inner join se_Menu menu on up.Menu_Index = menu.Menu_Index
                        inner join se_Platform pf on menu.Platform_Index = pf.Platform_Index
                where   pf.Id = ? and Group_Index = ?
            )

        ";

        $this->db->query($sql, [$param['filter']['Platform'], $param['filter']['User_Group_Value']]);

        foreach ($param['items'] as $value) {

            $sum_permission = intval($value['Created']) + intval($value['Readed']) + intval($value['Updated']) + intval($value['Deleted']) + intval($value['Exported']) + intval($value['Printed']) + intval($value['Approved1']) + intval($value['Approved2']);

            //can't create permission if sum of permission is 0
            if ($sum_permission > 0) {
                $data = [
                    'Created' => $value['Created'],
                    'Readed' => $value['Readed'],
                    'Updated' => $value['Updated'],
                    'Deleted' => $value['Deleted'],
                    'Exported' => $value['Exported'],
                    'Printed' => $value['Printed'],
                    'Approved1' => $value['Approved1'],
                    'Approved2' => $value['Approved2'],
                    'AddBy' => $param['filter']['AddBy'],
                    'AddDate' => $param['filter']['AddDate'],
                    'UpdateBy' => null,
                    'UpdateDate' => null,
                    'CancelBy' => null,
                    'CancelDate' => null,
                    'Group_Index' => $param['filter']['User_Group_Value'],
                    'Menu_Index' => $value['Menu_Index'],
                ];

                $this->db->insert('se_GroupPermission', $data);
            }

        }

        return $this->check_begintrans(); /*$this->db->error()*/;

    }

}
