<?php defined('BASEPATH') or exit('No direct script access allowed');

class Menu_Model extends MY_Model
{

    /**
     * Menu
     * ---------------------------------
     * @param : null
     */
    public function select_menu()
    {

        $this->set_db('default');

        $sql = "
           select se_Menu.*,se_Platform.Name as PlatformName from se_Menu inner join se_Platform on se_Menu.Platform_Index = se_Platform.Platform_Index
        ";

        $query = $this->db->query($sql);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Insert Menu
     * ---------------------------------
     * @param : FormData
     */
    public function insert_menu($param = [])
    {
        $this->set_db('default');

        return ($this->db->insert('se_Menu', $param['data'])) ? $this->db->insert_id() : false/*$this->db->error()*/;

    }

     /**
     * Update Menu
     * ---------------------------------
     * @param : FormData
     */
    public function update_menu($param = [])
    {
        $this->set_db('default');

        return ($this->db->update('se_Menu', $param['data'], ['Menu_Index'=> $param['index']])) ? true : false/*$this->db->error()*/;

    }

     /**
     * Delete Menu
     * ---------------------------------
     * @param : Menu_Index
     */
    public function delete_menu($param = [])
    {
        $this->set_db('default');

        $this->db->trans_begin();

        $this->db->delete('se_GroupPermission', ['Menu_Index' => $param['index']]);

        $this->db->delete('se_UserPermission', ['Menu_Index' => $param['index']]);

        $this->db->delete('se_Menu', ['Menu_Index' => $param['index']]);

        return $this->check_begintrans() /*$this->db->error()*/;

    }

    /**
     * Parent Menu
     * ---------------------------------
     * @param : null
     */
    public function select_parent_menu()
    {

        $this->set_db('default');

        $sql = "
           select * from se_Menu where MenuType_Index in (1,2,5,6) and IsUse = 1 order by MenuType_Index asc, Seq asc
        ";

        $query = $this->db->query($sql);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Update Seq Main Menu
     * ---------------------------------
     * @param : [Menu_Index,MenuType_Index,Seq]
     */
    public function update_seq_main_menu($param = [])
    {

        $this->set_db('default');

        $sql = "
           
            declare @Menu_Index int
            declare @MenuType_Index int
            declare @Platform_Index int
            declare @NewSeq int
            declare @OldSeq	int
            
            set @Menu_Index = ?
            set @MenuType_Index = ?
            set @Platform_Index = ?
            set @NewSeq = ?
            
            select @OldSeq = Seq from se_Menu where Menu_Index = @Menu_Index --get old Seq
            
            if @OldSeq is null --new record
            begin
                
                update se_Menu set Seq = (select MAX(Seq)+1 from se_Menu where MenuType_Index = @MenuType_Index and Platform_Index = @Platform_Index) where MenuType_Index = @MenuType_Index and Platform_Index = @Platform_Index and Seq = @NewSeq
            
            end
            else --update record
            begin
            
                update se_Menu set Seq = @OldSeq where MenuType_Index = @MenuType_Index and Platform_Index = @Platform_Index and Seq = @NewSeq
            
            end

        ";

        return $this->db->query($sql,[$param['Menu_Index'],$param['MenuType_Index'],$param['Platform_Index'],$param['Seq']]) ? true : false;

    }

    /**
     * Update Seq Sub Menu
     * ---------------------------------
     * @param : [Menu_Index,Seq,Zero,ParentMenu_Index,ParentRoute]
     */
    public function update_seq_sub_menu($param = [])
    {

        $this->set_db('default');

        $sql = "
           
            declare @Menu_Index int
            declare @NewSeq int
            declare @OldSeq	int

            declare @Zero nvarchar(50)
            declare @ParentMenu_Index int
            declare @ParentRoute nvarchar(50)
            
            set @Menu_Index = ?
            set @NewSeq = ?
            set @Zero = ?
            set @ParentMenu_Index = ?
            set @ParentRoute = ?
            
            select @OldSeq = Seq from se_Menu where Menu_Index = @Menu_Index --get old Seq
            
            if @OldSeq is null --new record
            begin
                
                update se_Menu set Seq = (select MAX(Seq)+1 from se_Menu where Route like replace(@ParentRoute,'.0','')+'%' and Route like '%'+ @Zero and Menu_Index <> @ParentMenu_Index) where Route like replace(@ParentRoute,'.0','')+'%' and Route like '%'+ @Zero and Menu_Index <> @ParentMenu_Index and Seq = @NewSeq
            
            end
            else --update record
            begin
            
                update se_Menu set Seq = @OldSeq where Route like replace(@ParentRoute,'.0','')+'%' and Route like '%'+ @Zero and Menu_Index <> @ParentMenu_Index and Seq = @NewSeq
            
            end

        ";

        return $this->db->query($sql,[$param['Menu_Index'],$param['Seq'],$param['Zero'],$param['ParentMenu_Index'],$param['ParentRoute']]) ? true : false;

    }


}
