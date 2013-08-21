<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public $table_name = '';
    public $primary_key = '';
    public $primary_filter = 'intval';
    public $order_by = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($ids = FALSE)
    {
        /* set flag - if we passed a single id we should return a single record */
        $single = $ids == FALSE || is_array($ids) ? FALSE : TRUE;

        /* limit results to one or more ids */
        if ($ids !== FALSE)
        {
            /* $ids should always be an array */
            is_array($ids) || $ids = array($ids);

            /* sanatise ids */
            $filter = $this->primary_filter;
            $ids = array_map($filter, $ids);

            $this->db->where_in($this->primary_key, $ids);
        }

        /* set order by */
        count($this->db->ar_orderby) || $this->db->order_by($this->order_by);

        /* return results */
        $single == FALSE || $this->db->limit(1);
        $method = $single ? 'row_array' : 'result_array';
        return $this->db->get($this->table_name)->$method();
    }

    public function get_by($key, $val = FALSE, $orwhere = FALSE, $single = FALSE)
    {
        /* limit results */
        if (!is_array($key))
        {
            $this->db->where(htmlentities($key), htmlentities($val));
        }
        else
        {
            $key = array_map('htmlentities', $key);
            $where_method = $orwhere == TRUE ? 'or_where' : 'where';
            $this->db->$where_method($key);
        }

        /* return results */
        $single == FALSE || $this->db->limit(1);
        $method = $single ? 'row_array' : 'result_array';
        return $this->db->get($this->table_name)->$method();
    }

    public function get_key_value($key_field, $value_field, $ids = FALSE)
    {
        /* get records */
        $this->db->select($key_field.', '.$value_field);
        $result = $this->get($ids);

        /* turn results into key=>value pair array */
        $data = array();

        if (count($result) > 0)
        {
            if ($ids != FALSE && !is_array($ids))
            {
                $result = array($result);
            }

            foreach ($result as $row)
            {
                $data[$row[$key_field]] = $row[$value_field];
            }
        }

        return $data;
    }

    public function get_assoc($ids = FALSE)
    {
        /* get records */
        $result = $this->get($ids);

        /* turn results into an associative array */
        if ($ids != FALSE && !is_array($ids))
        {
            $result = array($result);
        }

        $data = $this->to_assoc($result);

        return $data;
    }

    public function to_assoc($result = array())
    {
        $data = array();

        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                $tmp = array_values(array_slice($row, 0, 1));
                $data[$tmp[0]] = $row;
            }
        }

        return $data;
    }

    public function save($data, $id = FALSE)
    {
        $this->_before_save($data, $id); /* run before the save */

        /* insert or update */
        if ($id == FALSE)
        {
            /* this is an insert */
            $this->db->set($data)->insert($this->table_name);
        }
        else
        {
            /* this is an update */
            $filter = $this->primary_filter;
            $this->db->set($data)->where($this->primary_key, $filter($id))->update($this->table_name);
        }

        $this->_after_save($data, $id);

        /* save and return the id */
        return $id == FALSE ? $this->db->insert_id() : $id;
    }

    protected function _before_save($data, $id)
    {

    }

    protected function _after_save($data, $id)
    {

    }

    public function delete($ids)
    {

        $this->_before_delete($ids); /* run before the save */

        $filter = $this->primary_filter;
        $ids = !is_array($ids) ? array($ids) : $ids;

        foreach ($ids as $id)
        {
            $id = $filter($id);
            if ($id)
            {
                if ($this->db->where($this->primary_key, $id)->limit(1)->delete($this->table_name))
                {
                    $this->_after_delete($ids); /* run after the save */
                    return TRUE;
                }
            }
        }
    }

    public function delete_by($key, $value)
    {
        if (empty($key))
        {
            return FALSE;
        }

        $this->db->where(htmlentities($key), htmlentities($value))->delete($this->table_name);
    }

    protected function _before_delete($id)
    {

    }

    protected function _after_delete($id)
    {

    }

    public function valid($id)
    {
        $this->db->where($this->primary_key, $id);
        $result = $this->db->get($this->table_name);

        if ($result->num_rows() > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

}
