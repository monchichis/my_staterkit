<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setup_summary extends CI_Controller
{
    public function index()
    {
        if (!is_cli()) {
            echo "CLI only";
            return;
        }

        $this->load->database();

        $sql = "CREATE TABLE IF NOT EXISTS `tbl_summary_widgets` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `table_name` varchar(255) NOT NULL,
          `column_name` varchar(255) NOT NULL,
          `aggregate_func` varchar(20) NOT NULL,
          `bg_color_class` varchar(50) NOT NULL,
          `allowed_roles` text DEFAULT NULL,
          `is_active` tinyint(1) DEFAULT 1,
          `created_at` datetime DEFAULT current_timestamp(),
          `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($this->db->query($sql)) {
            echo "Table 'tbl_summary_widgets' created successfully.\n";
        } else {
            echo "Error creating table: " . $this->db->error()['message'] . "\n";
        }
    }
}
