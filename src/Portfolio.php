<?php

/**
 * Portfolio
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
 * Portfolio
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 2.2
 *
 **/
class Portfolio{

    /**
     * @var Database Connection
     */
    private $db_conn;

    /**
     * Class constructor. Defines class variables
     */
    function __construct(\PDO $pdo_connection){
        $this->db_conn = $pdo_connection;
    }

    /**
     * Get all projects
     * Is this necessary?
     * @return array Summary of project information
     */
    function get_all_projects(){
        return $this->get_projects('all');
    }

    /**
     * Get information about a portfolio project
     * @param integer $index_value A unique project identifier
     * @param integer $index The type of $index_value. Either project_id or url_safe_title
     * @return array Summary of project information
     */
    function get_projects($filter, $filter_param = NULL, $limit = NULL, $offset = NULL){

        $projects = array();

        // Initialize query
        $query = "SELECT * FROM `project`";
        $params = array($filter_param);

        // Add to query
        if($filter == 'all'){

            $query .= " ORDER BY title ASC";

        }elseif($filter == 'date'){

            if( $filter_param == 'ASC' ){
                $query .= " ORDER BY start_date ASC";
            }
            else{
                $query .= " ORDER BY completion_date IS NOT NULL, completion_date DESC, start_date DESC";
                $params = [];
            }

        }elseif($filter == 'id'){

            $query .= " WHERE project_id = ?";

        }elseif($filter == 'url_safe_title'){

            $query .= " WHERE url_safe_title = ?";

        }elseif($filter == 'keyword'){

            $query = "SELECT project.*
                FROM `keyword`
                    RIGHT JOIN project_keyword USING(keyword_id)
                    RIGHT JOIN project USING(project_id)
                WHERE keyword = ?";

        }elseif($filter == 'medium'){

            $query = "SELECT project.*
                FROM `medium`
                    RIGHT JOIN project_medium USING(medium_id)
                    RIGHT JOIN project USING(project_id)
                WHERE medium = ?";

        }elseif($filter == 'discipline'){

            $query = "SELECT project.*
                FROM `discipline`
                    RIGHT JOIN project_discipline USING(discipline_id)
                    RIGHT JOIN project USING(project_id)
                WHERE discipline = ?";

        }elseif($filter == 'popularity'){

            $query = "SELECT `project`.*, SUM(hits) AS num_hits
                FROM `project`
                    LEFT JOIN `project_hits` USING(project_id)
                WHERE `date` > ?
                    AND `date` <= ?
                GROUP BY `project_id`
                ORDER BY num_hits DESC";

            $params = array(
                date('Y-m-d', strtotime('-4 weeks')),
                date('Y-m-d', strtotime('yesterday'))
            );

        }elseif($filter == 'random'){

            $query .= " ORDER BY rand()";

        }elseif($filter == 'title'){

            if( $filter_param != 'ASC' && $filter_param != 'DESC' ){
                $order_by = 'ASC';
            }

            $query .= " ORDER BY title " . $order_by;

        }

        // Include limit and offset parameters, if relevant & provided
        if( $filter != 'id' && $limit > 0){
            if($offset > 0){
                $query .= sprintf(" LIMIT %d, %d", $offset, $limit);
            }
            else{
                $query .= sprintf(" LIMIT %d", $limit);
            }
        }

        // Execute query
        $rows = [];
        try {
            $query = $this->db_conn->prepare($query);
            if($query->execute($params)){
                $rows = $query->fetchAll();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        // Loop through results
        foreach( (array) $rows as $row){

            $project = array();
            $project['overview'] = $row;

            $completion_date = explode('-', $row['completion_date']);
            if( substr($row['completion_date'], 4, 6) == '-00-00' ){
                $project['overview']['formatted_date'] = date('Y', strtotime( str_replace('-00-00', '-01-01', $row['completion_date']) ));
            }elseif( substr($row['completion_date'], 7, 3) == '-00' ){
                $project['overview']['formatted_date'] = date('F Y', strtotime( str_replace('-00', '-01', $row['completion_date']) ));
            }else{
                $project['overview']['formatted_date'] = date('F jS, Y', strtotime($row['completion_date']));
            }

            $project['overview']['in_progress'] = 0;
            if( is_null($row['completion_date']) || substr($row['completion_date'],0,4) == '0000' ){
                $project['overview']['in_progress'] = 1;
                $project['overview']['formatted_date'] = date('F jS, Y', strtotime($row['start_date']));
            }

            // Get related information
            $project['items'] = $this->get_project_items('project_id', $project['overview']['project_id']);
            $project['disciplines'] = $this->get_project_disciplines($project['overview']['project_id']);
            $project['mediums'] = $this->get_project_mediums($project['overview']['project_id']);
            $project['related_projects'] = $this->get_related_projects($project['overview']['project_id']);

            // Filter related projects so only published projects are shown
            $project['related_projects'] = array_filter($project['related_projects'], function($var) {
                return $var['publish'];
            });

            $projects[] = $project;
        }

        // Apply advanced sorting for if projects requested by date. This factors in incomplete projects
        // that have an "empty" (0000-00-00) completion date
        /*
        if( $filter == 'date' && $filter_param == 'DESC' ){
            foreach($projects as $proj) {
                if( $proj['overview']['completion_date'] != '0000-00-00')
                    $completed_projects[] = $proj;
                else
                    $incomplete_projects[] = $proj;
            }
            $projects = array_merge($incomplete_projects, $completed_projects);
        }
        */

        // Return project
        return $projects;
    }

    /**
     * Get the disciplines related to a project
     * @param integer $project_id A unique project ID
     * @return array Summary of disciplines used
     */
    function get_project_disciplines($project_id){

        $results = array();
        $disciplines = array();

        try {
            $query = $this->db_conn->prepare(
                "SELECT discipline.*
                FROM `project_discipline`
                LEFT JOIN `discipline` USING(discipline_id)
                WHERE project_id = ?"
            );
            $query->bindValue(1, $project_id, \PDO::PARAM_INT);
            if($query->execute()){
                $results = $query->fetchAll();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        // TODO: Rewrite this with array map?
        foreach($results as $discipline){
            $disciplines[$discipline['discipline_id']] = ucwords($discipline['discipline']);
        }

        return $disciplines;
    }

    /**
     * Get items related to a project
     * @param string $key The type of ID to be used
     * @param integer $id A ID of type $key
     * @return array Summary of items
     */
    function get_project_items($key = 'project_id', $id = 0){

        $items = array();

        try {

            if( $key == 'item_id' ){

                $query = $this->db_conn->prepare(
                    "SELECT project_item.*
                    FROM `project_item`
                    WHERE item_id = ?"
                );
                $query->bindValue(1, $id, \PDO::PARAM_INT);

            }else{

                $query = $this->db_conn->prepare(
                    "SELECT project_item.*
                    FROM `project_item`
                    WHERE project_id = ? AND rank > 0
                    ORDER BY rank, item_id ASC"
                );
                $query->bindValue(1, $id, \PDO::PARAM_INT);

            }

            if($query->execute()){
                $items = $query->fetchAll();
            }

        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        return $items;
    }

    /**
     * Get items related to a project
     * @param integer $project_id A unique project ID
     * @return array Summary of items
     */
    function get_project_keywords($project_id){

        $results = array();
        $keywords = array();

        try {
            $query = $this->db_conn->prepare(
                "SELECT keyword.*
                FROM `project_keyword`
                LEFT JOIN `keyword` USING(keyword_id)
                WHERE project_id = ?"
            );
            $query->bindValue(1, $project_id, \PDO::PARAM_INT);
            if($query->execute()){
                $results = $query->fetchAll();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        // TODO: Rewrite this with array map?
        foreach($results as $keyword){
            $keywords[$keyword['keyword_id']] = ucwords($keyword['keyword']);
        }

        return $keywords;
    }

    /**
     * Get the disciplines related to a project
     * @param integer $project_id A unique project ID
     * @return array Summary of disciplines used
     */
    function get_project_mediums($project_id){

        $results = array();
        $mediums = array();

        try {
            $query = $this->db_conn->prepare(
                "SELECT medium.*
                FROM `project_medium`
                LEFT JOIN `medium` USING(medium_id)
                WHERE project_id = ?"
            );
            $query->bindValue(1, $project_id, \PDO::PARAM_INT);
            if($query->execute()){
                $results = $query->fetchAll();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        // TODO: Rewrite this with array map?
        foreach($results as $medium){
            $mediums[$medium['medium_id']] = ucwords($medium['medium']);
        }

        return $mediums;
    }

    /**
     * Get the related projects
     * @param integer $project_id A unique project ID
     * @return array Summary of disciplines used
     */
    function get_related_projects($project_id){

        $related_projects = array();

        try {
            $query = $this->db_conn->prepare(
                "SELECT related_projects.project_id_A as project_id, project.title, project.url_safe_title, publish
                FROM `related_projects`
                    LEFT JOIN `project` ON project.project_id = related_projects.project_id_A
                WHERE project_id_B = ?"
            );
            $query->bindValue(1, $project_id, \PDO::PARAM_INT);
            if($query->execute()){
                $projects_A = $query->fetchAll();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        try {
            $query = $this->db_conn->prepare(
                "SELECT related_projects.project_id_B as project_id, project.title, project.url_safe_title, publish
                FROM `related_projects`
                    LEFT JOIN `project` ON project.project_id = related_projects.project_id_B
                WHERE project_id_A = ?"
            );
            $query->bindValue(1, $project_id, \PDO::PARAM_INT);
            if($query->execute()){
                $projects_B = $query->fetchAll();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
        }

        $related_projects = array_merge($projects_A, $projects_B);

        return $related_projects;
    }

    /**
     * Report the number of times that a project was viewed.
     * @param integer $project_id A unique project ID
     * @param string $start_date Start date in YYYY-MM-DD format (Inclusive)
     * @param string $end_date End date in YYYY-MM-DD format (Inclusive)
     * @return array A multidimensional array of hits and date values.
     */
    function get_project_hits($project_id, $start_date, $end_date){

        $hits = array();

        $i_date = $start_date;

        while( strtotime($i_date) <= strtotime($end_date) ){

            $num_hits = 0;

            try {

                $query = $this->db_conn->prepare(
                    "SELECT hits
                    FROM `project_hits`
                    WHERE project_id = ? AND `date` = ?"
                );
                $query->bindValue(1, $project_id, \PDO::PARAM_INT);
                $query->bindValue(2, $i_date, \PDO::PARAM_STR);
                if($query->execute()){
                    $num_hits = $query->fetchColumn();
                }

            } catch(\PDOException $e) {
                error_log($e->getMessage() . ' in file ' . __FILE__ . ' on line ' . __LINE__ . PHP_EOL);
            }

            $hits['data'][] = $num_hits;
            $hits['labels'][] = $i_date;

            $i_date = date ("Y-m-d", strtotime("+1 day", strtotime($i_date)));
        }

        return $hits;
    }

}