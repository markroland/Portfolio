<?php

/**
 * Project
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
 * Project
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 3
 *
 **/
class Project{

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
     * Execute a stored procedure that returns one row.
     * @param string $procedure_name Stored procedure name
     * @param array $params Stored procedure parameters
     * @return array Row data
     */
    private function fetch_one($procedure_name, array $params = array()){

        $row = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call($procedure_name, implode(',', array_fill(0, count($params), '?'))));
            if($query->execute($params)){
                $row = $query->fetch();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $row ?: array();
    }

    /**
     * Execute a stored procedure that returns rows.
     * @param string $procedure_name Stored procedure name
     * @param array $params Stored procedure parameters
     * @return array Row data
     */
    private function fetch_all($procedure_name, array $params = array()){

        $rows = array();

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call($procedure_name, implode(',', array_fill(0, count($params), '?'))));
            if($query->execute($params)){
                $rows = $query->fetchAll();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $rows;
    }

    /**
     * Execute a stored procedure without returning rows.
     * @param string $procedure_name Stored procedure name
     * @param array $params Stored procedure parameters
     * @return boolean Whether execution succeeded
     */
    private function execute($procedure_name, array $params = array()){

        $result = false;

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call($procedure_name, implode(',', array_fill(0, count($params), '?'))));
            $result = $query->execute($params);
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $result;
    }

    /**
     * Get all projects.
     * @return array Summary of project information
     */
    function get_projects(){
        return $this->fetch_all('get_projects_admin');
    }

    /**
     * Get a project with relationship data.
     * @param integer $project_id A unique project ID
     * @return array Summary of project information
     */
    function get_project($project_id){

        $project = $this->fetch_one('get_project_by_id', array($project_id));

        if( empty($project) ){
            return array();
        }

        $project['disciplines'] = $this->pluck_ids($this->fetch_all('get_project_disciplines', array($project_id)), 'discipline_id');
        $project['keywords'] = $this->pluck_ids($this->fetch_all('get_project_keywords', array($project_id)), 'keyword_id');
        $project['media'] = $this->pluck_ids($this->fetch_all('get_project_media', array($project_id)), 'medium_id');
        $project['related_projects'] = $this->pluck_ids($this->fetch_all('get_related_projects', array($project_id)), 'project_id');
        $project['items'] = $this->fetch_all('get_project_items', array($project_id));

        return $project;
    }

    /**
     * Add a project.
     * @param array $project Project data
     * @return array Summary of project information
     */
    function add_project(array $project){

        $project_id = 0;

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('add_project', '?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,@project_id'));
            $query->execute($this->project_values($project));
            $query->closeCursor();

            $query = $this->db_conn->prepare("SELECT @project_id");
            if($query->execute()){
                $project_id = (int) $query->fetchColumn();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        if( $project_id > 0 ){
            $this->save_relationships($project_id, $project);
        }

        return $this->get_project($project_id);
    }

    /**
     * Update a project.
     * @param integer $project_id A unique project ID
     * @param array $project Project data
     * @return array Summary of project information
     */
    function update_project($project_id, array $project){

        $this->execute('update_project', array_merge(array($project_id), $this->project_values($project)));
        $this->save_relationships($project_id, $project);

        return $this->get_project($project_id);
    }

    /**
     * Delete a project and its editable relationship records.
     * @param integer $project_id A unique project ID
     * @return boolean Whether a matching project no longer exists
     */
    function delete_project($project_id){

        $this->execute('delete_project_disciplines', array($project_id));
        $this->execute('delete_project_keywords', array($project_id));
        $this->execute('delete_project_media', array($project_id));
        $this->execute('delete_related_projects', array($project_id));
        $this->execute('delete_project_items', array($project_id));
        $this->execute('delete_project', array($project_id));

        return empty($this->get_project($project_id));
    }

    /**
     * Get a project item by ID.
     * @param integer $item_id A unique item ID
     * @return array Summary of project item information
     */
    function get_project_item($item_id){
        return $this->fetch_one('get_project_item_by_id', array($item_id));
    }

    /**
     * Get unique project item purpose values.
     * @return array Project item purpose values
     */
    function get_project_item_purposes(){
        return $this->fetch_all('get_project_item_purposes');
    }

    /**
     * Add a project item.
     * @param integer $project_id A unique project ID
     * @param array $item Project item data
     * @return array Summary of project item information
     */
    function add_project_item($project_id, array $item){

        $item_id = 0;

        try {
            $query = $this->db_conn->prepare($this->get_procedure_call('add_project_item', '?,?,?,?,?,?,?,?,?,@item_id'));
            $query->execute($this->project_item_values($project_id, $item));
            $query->closeCursor();

            $query = $this->db_conn->prepare("SELECT @item_id");
            if($query->execute()){
                $item_id = (int) $query->fetchColumn();
            }
            $query->closeCursor();
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $this->get_project_item($item_id);
    }

    /**
     * Update a project item.
     * @param integer $item_id A unique item ID
     * @param integer $project_id A unique project ID
     * @param array $item Project item data
     * @return array Summary of project item information
     */
    function update_project_item($item_id, $project_id, array $item){

        $this->execute('update_project_item', array_merge(array($item_id), $this->project_item_values($project_id, $item)));

        return $this->get_project_item($item_id);
    }

    /**
     * Delete a project item.
     * @param integer $item_id A unique item ID
     * @return boolean Whether a matching item no longer exists
     */
    function delete_project_item($item_id){

        $this->execute('delete_project_item', array($item_id));

        return empty($this->get_project_item($item_id));
    }

    /**
     * Convert stored procedure rows to integer IDs.
     * @param array $rows Row data
     * @param string $key ID column
     * @return array Integer IDs
     */
    private function pluck_ids(array $rows, $key){

        $ids = array();

        foreach($rows as $row){
            if( isset($row[$key]) ){
                $ids[] = (int) $row[$key];
            }
        }

        return $ids;
    }

    /**
     * Get project fields in stored procedure order.
     * @param array $project Project data
     * @return array Project values
     */
    private function project_values(array $project){

        return array(
            !empty($project['publish']) ? 1 : 0,
            $project['grade'] ?? 0,
            ($project['start_date'] ?? '') !== '' ? $project['start_date'] : NULL,
            ($project['completion_date'] ?? '') !== '' ? $project['completion_date'] : NULL,
            $project['title'] ?? '',
            $project['url_safe_title'] ?? '',
            $project['synopsis'] ?? '',
            $project['description'] ?? '',
            $project['tutorial'] ?? '',
            !empty($project['open_source']) ? 1 : 0,
            $project['location'] ?? '',
            $project['width_inches'] ?? 0,
            $project['height_inches'] ?? 0,
            $project['depth_inches'] ?? 0,
            $project['weight_lbs'] ?? 0
        );
    }

    /**
     * Get project item fields in stored procedure order.
     * @param integer $project_id A unique project ID
     * @param array $item Project item data
     * @return array Project item values
     */
    private function project_item_values($project_id, array $item){

        return array(
            $project_id,
            $item['rank'] ?? 0,
            $item['purpose'] ?? '',
            $item['media_type'] ?? '',
            $item['URL'] ?? '',
            $item['width'] ?? 0,
            $item['height'] ?? 0,
            $item['title'] ?? '',
            $item['description'] ?? ''
        );
    }

    /**
     * Replace editable project relationships.
     * @param integer $project_id A unique project ID
     * @param array $project Project data
     * @return void
     */
    private function save_relationships($project_id, array $project){

        $this->replace_relationship($project_id, $project['disciplines'] ?? array(), 'delete_project_disciplines', 'add_project_discipline');
        $this->replace_relationship($project_id, $project['keywords'] ?? array(), 'delete_project_keywords', 'add_project_keyword');
        $this->replace_relationship($project_id, $project['media'] ?? array(), 'delete_project_media', 'add_project_medium');
        $this->replace_related_projects($project_id, $project['related_projects'] ?? array());
    }

    /**
     * Replace one project lookup relationship.
     * @param integer $project_id A unique project ID
     * @param array $ids Selected lookup IDs
     * @param string $delete_procedure Delete procedure name
     * @param string $add_procedure Add procedure name
     * @return void
     */
    private function replace_relationship($project_id, array $ids, $delete_procedure, $add_procedure){

        $this->execute($delete_procedure, array($project_id));

        foreach(array_unique($ids) as $id){
            if( (int) $id > 0 ){
                $this->execute($add_procedure, array($project_id, (int) $id));
            }
        }
    }

    /**
     * Replace related project records.
     * @param integer $project_id A unique project ID
     * @param array $ids Selected related project IDs
     * @return void
     */
    private function replace_related_projects($project_id, array $ids){

        $this->execute('delete_related_projects', array($project_id));

        foreach(array_unique($ids) as $id){
            if( (int) $id > 0 && (int) $id != (int) $project_id ){
                $this->execute('add_related_project', array($project_id, (int) $id));
            }
        }
    }
}
