<?php

/**
 * Medium
 *
 * PHP version 8, 7, 5
 *
 * @category  Portfolio
 * @package   Portfolio
 * @author    Mark Roland
 * @copyright 2015 Mark Roland
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/markroland/composer-boilerplate
 **/

namespace MarkRoland\Portfolio;

/**
 * Medium
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 3
 *
 **/
class Medium{

    /**
     * @var Database Connection
     */
    private $db_conn;

    /**
     * @var Stored procedure database name
     */
    private $db_name;

    /**
     * Class constructor. Defines class variables
     */
    function __construct(\PDO $pdo_connection, $database_name = NULL){
        $this->db_conn = $pdo_connection;
        $this->db_name = $database_name;
    }

    /**
     * Get a stored procedure call
     * @param string $procedure_name Stored procedure name
     * @param string $params Stored procedure parameters
     * @return string Stored procedure call
     */
    private function get_procedure_call($procedure_name, $params = ''){

        if( !is_null($this->db_name) && $this->db_name != '' ){
            $procedure_name = '`' . str_replace('`', '``', $this->db_name) . '`.`' . $procedure_name . '`';
        }

        return "CALL " . $procedure_name . "(" . $params . ")";
    }

    /**
     * Get all media
     * @return array Summary of medium information
     */
    function get_media(){

        $media = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_media'));
            if($query->execute()){
                $media = $query->fetchAll();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $media;
    }

    /**
     * Get a medium by ID
     * @param integer $medium_id A unique medium ID
     * @return array Summary of medium information
     */
    function get_medium($medium_id){

        $medium = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_medium_by_id', '?'));
            $query->bindValue(1, $medium_id, \PDO::PARAM_INT);
            if($query->execute()){
                $medium = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $medium ?: array();
    }

    /**
     * Get a medium by word
     * @param string $medium_word Medium text
     * @return array Summary of medium information
     */
    function get_medium_by_word($medium_word){

        $medium = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_medium_by_word', '?'));
            $query->bindValue(1, $medium_word, \PDO::PARAM_STR);
            if($query->execute()){
                $medium = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $medium ?: array();
    }

    /**
     * Add a medium
     * @param string $medium_word Medium text
     * @return array Summary of medium information
     */
    function add_medium($medium_word){

        $medium_id = 0;

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('add_medium', '?, @medium_id'));
            $query->bindValue(1, $medium_word, \PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $query = $this->db_conn->prepare("SELECT @medium_id");
            if($query->execute()){
                $medium_id = (int) $query->fetchColumn();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_medium($medium_id);
    }

    /**
     * Update a medium
     * @param integer $medium_id A unique medium ID
     * @param string $medium_word Medium text
     * @return array Summary of medium information
     */
    function update_medium($medium_id, $medium_word){

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('update_medium', '?, ?'));
            $query->bindValue(1, $medium_id, \PDO::PARAM_INT);
            $query->bindValue(2, $medium_word, \PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_medium($medium_id);
    }

    /**
     * Delete a medium
     * @param integer $medium_id A unique medium ID
     * @return boolean Whether a matching medium no longer exists
     */
    function delete_medium($medium_id){

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('delete_medium', '?'));
            $query->bindValue(1, $medium_id, \PDO::PARAM_INT);
            $query->execute();
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return empty($this->get_medium($medium_id));
    }
}
