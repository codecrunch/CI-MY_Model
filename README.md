CI-MY_Model
===========

Codeigniter MY_Model
--------------------

### Install

Copy MY_Model.php to folder application/core

### Usage

````php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'user';
        $this->primary_key = 'user_id';
        $this->order_by = $this->primary_key.' ASC';
    }

}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
````
