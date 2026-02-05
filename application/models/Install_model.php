<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Install_model extends CI_Model
{
    /**
     * Test database connection
     */
    public function test_db_connection($hostname, $username, $password, $database = null)
    {
        $mysqli = @new mysqli($hostname, $username, $password, $database);
        
        if ($mysqli->connect_error) {
            return [
                'status' => false,
                'message' => 'Connection failed: ' . $mysqli->connect_error
            ];
        }
        
        $mysqli->close();
        
        return [
            'status' => true,
            'message' => 'Connection successful!'
        ];
    }

    /**
     * Create database
     */
    public function create_database($hostname, $username, $password, $database)
    {
        $mysqli = @new mysqli($hostname, $username, $password);
        
        if ($mysqli->connect_error) {
            return [
                'status' => false,
                'message' => 'Connection failed: ' . $mysqli->connect_error
            ];
        }
        
        // Check if database already exists
        $result = $mysqli->query("SHOW DATABASES LIKE '$database'");
        if ($result->num_rows > 0) {
            // Database exists, check if we should use it
            $mysqli->close();
            return [
                'status' => true,
                'message' => 'Database already exists and will be used.',
                'existing' => true
            ];
        }
        
        // Create database
        $sql = "CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        
        if ($mysqli->query($sql) === TRUE) {
            $mysqli->close();
            return [
                'status' => true,
                'message' => 'Database created successfully!'
            ];
        } else {
            $error = $mysqli->error;
            $mysqli->close();
            return [
                'status' => false,
                'message' => 'Error creating database: ' . $error
            ];
        }
    }

    /**
     * Execute SQL file
     */
    public function execute_sql_file($sql_file)
    {
        if (!file_exists($sql_file)) {
            return [
                'status' => false,
                'message' => 'SQL file not found'
            ];
        }
        
        $sql = file_get_contents($sql_file);
        
        if ($sql === false) {
            return [
                'status' => false,
                'message' => 'Failed to read SQL file'
            ];
        }
        
        // Get database credentials from config file
        require APPPATH . 'config/database.php';
        
        // Create mysqli connection using config
        $mysqli = @new mysqli(
            $db['default']['hostname'],
            $db['default']['username'],
            $db['default']['password'],
            $db['default']['database']
        );
        
        if ($mysqli->connect_error) {
            return [
                'status' => false,
                'message' => 'Database connection failed: ' . $mysqli->connect_error
            ];
        }
        
        // Split SQL statements
        $statements = $this->split_sql_statements($sql);
        
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }
            
            if ($mysqli->query($statement)) {
                $success_count++;
            } else {
                $error_count++;
                $errors[] = $mysqli->error;
            }
        }
        
        $mysqli->close();
        
        if ($error_count > 0) {
            return [
                'status' => false,
                'message' => "Executed $success_count statements successfully, $error_count failed",
                'errors' => $errors,
                'success_count' => $success_count,
                'error_count' => $error_count
            ];
        }
        
        return [
            'status' => true,
            'message' => "Successfully executed $success_count SQL statements",
            'success_count' => $success_count
        ];
    }

    /**
     * Split SQL file into individual statements
     */
    private function split_sql_statements($sql)
    {
        // Remove comments
        $sql = preg_replace('/--[^\n]*\n/', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon (considering statements can be multi-line)
        $statements = [];
        $current_statement = '';
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines and comments
            if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 2) === '/*') {
                continue;
            }
            
            $current_statement .= $line . ' ';
            
            // Check if statement ends with semicolon
            if (substr(rtrim($line), -1) === ';') {
                $statements[] = trim($current_statement);
                $current_statement = '';
            }
        }
        
        // Add last statement if exists
        if (!empty(trim($current_statement))) {
            $statements[] = trim($current_statement);
        }
        
        return $statements;
    }

    /**
     * Save database configuration to file
     */
    public function save_db_config($hostname, $username, $password, $database)
    {
        $config_file = APPPATH . 'config/database.php';
        $config_content = file_get_contents($config_file);
        
        // Replace database configuration values
        $config_content = preg_replace(
            "/'hostname'\s*=>\s*'[^']*'/",
            "'hostname' => '$hostname'",
            $config_content
        );
        
        $config_content = preg_replace(
            "/'username'\s*=>\s*'[^']*'/",
            "'username' => '$username'",
            $config_content
        );
        
        $config_content = preg_replace(
            "/'password'\s*=>\s*'[^']*'/",
            "'password' => '$password'",
            $config_content
        );
        
        $config_content = preg_replace(
            "/'database'\s*=>\s*'[^']*'/",
            "'database' => '$database'",
            $config_content
        );
        
        return file_put_contents($config_file, $config_content);
    }

    /**
     * Create installation lock file
     */
    public function create_lock_file()
    {
        $lock_file = APPPATH . 'config/installed.lock';
        $content = "Installation completed on: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Do not delete this file unless you want to reinstall the application.";
        
        return file_put_contents($lock_file, $content);
    }

    /**
     * Save uninstall secret key to config file
     */
    public function save_secret_key($secret_key)
    {
        $config_file = APPPATH . 'config/config.php';
        $config_content = file_get_contents($config_file);
        
        // Check if uninstall_secret_key already exists
        if (strpos($config_content, '$config[\'uninstall_secret_key\']') !== false) {
            // Replace existing value
            $config_content = preg_replace(
                "/\\\$config\\['uninstall_secret_key'\\]\\s*=\\s*'[^']*';/",
                "\$config['uninstall_secret_key'] = '" . addslashes($secret_key) . "';",
                $config_content
            );
        } else {
            // Add new config at the end before closing PHP tag
            $config_content = str_replace(
                "/* End of file config.php */",
                "\$config['uninstall_secret_key'] = '" . addslashes($secret_key) . "';\n\n/* End of file config.php */",
                $config_content
            );
        }
        
        return file_put_contents($config_file, $config_content);
    }

    /**
     * Check if installed
     */
    public function is_installed()
    {
        $lock_file = APPPATH . 'config/installed.lock';
        return file_exists($lock_file);
    }
}
