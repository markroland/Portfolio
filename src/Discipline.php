<?php

/**
 * Discipline
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

namespace MarkRoland;

/**
 * Discipline
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 2.2
 *
 **/
class Discipline{

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
     * Get all disciplines
     * @return array Summary of discipline information
     */
    function get_disciplines(){

        $disciplines = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_disciplines'));
            if($query->execute()){
                $disciplines = $query->fetchAll();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $disciplines;
    }

    /**
     * Get a discipline by ID
     * @param integer $discipline_id A unique discipline ID
     * @return array Summary of discipline information
     */
    function get_discipline($discipline_id){

        $discipline = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_discipline_by_id', '?'));
            $query->bindValue(1, $discipline_id, \PDO::PARAM_INT);
            if($query->execute()){
                $discipline = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $discipline ?: array();
    }

    /**
     * Get a discipline by word
     * @param string $discipline_word Discipline text
     * @return array Summary of discipline information
     */
    function get_discipline_by_word($discipline_word){

        $discipline = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('get_discipline_by_word', '?'));
            $query->bindValue(1, $discipline_word, \PDO::PARAM_STR);
            if($query->execute()){
                $discipline = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $discipline ?: array();
    }

    /**
     * Add a discipline
     * @param string $discipline_word Discipline text
     * @return array Summary of discipline information
     */
    function add_discipline($discipline_word){

        $discipline_id = 0;

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('add_discipline', '?, @discipline_id'));
            $query->bindValue(1, $discipline_word, \PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $query = $this->db_conn->prepare("SELECT @discipline_id");
            if($query->execute()){
                $discipline_id = (int) $query->fetchColumn();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_discipline($discipline_id);
    }

    /**
     * Update a discipline
     * @param integer $discipline_id A unique discipline ID
     * @param string $discipline_word Discipline text
     * @return array Summary of discipline information
     */
    function update_discipline($discipline_id, $discipline_word){

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('update_discipline', '?, ?'));
            $query->bindValue(1, $discipline_id, \PDO::PARAM_INT);
            $query->bindValue(2, $discipline_word, \PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_discipline($discipline_id);
    }

    /**
     * Delete a discipline
     * @param integer $discipline_id A unique discipline ID
     * @return boolean Whether a matching discipline no longer exists
     */
    function delete_discipline($discipline_id){

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('delete_discipline', '?'));
            $query->bindValue(1, $discipline_id, \PDO::PARAM_INT);
            $query->execute();
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return empty($this->get_discipline($discipline_id));
    }
}
