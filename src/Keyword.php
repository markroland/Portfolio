<?php

/**
 * Keyword
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
 * Keyword
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 3
 *
 **/
class Keyword{

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
     * Get all keywords
     * @return array Summary of keyword information
     */
    function get_keywords(){

        $keywords = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_keywords'));
            if($query->execute()){
                $keywords = $query->fetchAll();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $keywords;
    }

    /**
     * Get a keyword by ID
     * @param integer $keyword_id A unique keyword ID
     * @return array Summary of keyword information
     */
    function get_keyword($keyword_id){

        $keyword = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_keyword_by_id', '?'));
            $query->bindValue(1, $keyword_id, \PDO::PARAM_INT);
            if($query->execute()){
                $keyword = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $keyword ?: array();
    }

    /**
     * Get a keyword by word
     * @param string $keyword_word Keyword text
     * @return array Summary of keyword information
     */
    function get_keyword_by_word($keyword_word){

        $keyword = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_keyword_by_word', '?'));
            $query->bindValue(1, $keyword_word, \PDO::PARAM_STR);
            if($query->execute()){
                $keyword = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $keyword ?: array();
    }

    /**
     * Add a keyword
     * @param string $keyword_word Keyword text
     * @return array Summary of keyword information
     */
    function add_keyword($keyword_word){

        $keyword_id = 0;

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('add_keyword', '?, @keyword_id'));
            $query->bindValue(1, $keyword_word, \PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $query = $this->db_conn->prepare("SELECT @keyword_id");
            if($query->execute()){
                $keyword_id = (int) $query->fetchColumn();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_keyword($keyword_id);
    }

    /**
     * Update a keyword
     * @param integer $keyword_id A unique keyword ID
     * @param string $keyword_word Keyword text
     * @return array Summary of keyword information
     */
    function update_keyword($keyword_id, $keyword_word){

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('update_keyword', '?, ?'));
            $query->bindValue(1, $keyword_id, \PDO::PARAM_INT);
            $query->bindValue(2, $keyword_word, \PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_keyword($keyword_id);
    }

    /**
     * Delete a keyword
     * @param integer $keyword_id A unique keyword ID
     * @return boolean Whether a matching keyword no longer exists
     */
    function delete_keyword($keyword_id){

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('delete_keyword', '?'));
            $query->bindValue(1, $keyword_id, \PDO::PARAM_INT);
            $query->execute();
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return empty($this->get_keyword($keyword_id));
    }
}
