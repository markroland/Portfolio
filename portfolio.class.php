<?php

/**
 * Portfolio
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 1.0
 *
 **/
class portfolio{

  /**
   * Get information about a portfolio project
   * @param integer $index_value A unique project identifier
   * @param integer $index The type of $index_value. Either project_id or url_safe_title
   * @return array Summary of project information
   */
  function get_project($index_value, $index = 'project_id'){
    switch($index){
      case 'url_safe_title':
        $query = sprintf("SELECT * FROM `markr34_portfolio`.`project` WHERE url_safe_title='%s'", mysql_real_escape_string($index_value));
        break;
      default:
        $query = sprintf("SELECT * FROM `markr34_portfolio`.`project` WHERE project_id=%d", mysql_real_escape_string($index_value));
        break;
    }

    $result = mysql_query($query);
    $row['overview'] = mysql_fetch_array($result,MYSQL_ASSOC);
    
    $completion_date = explode('-', $row['overview']['completion_date']);
    if( substr($row['overview']['completion_date'], 4, 6) == '-00-00' ){
      $row['overview']['formatted_date'] = date('Y', strtotime( str_replace('-00-00', '-01-01', $row['overview']['completion_date']) ));
    }elseif( substr($row['overview']['completion_date'], 7, 3) == '-00' ){
      $row['overview']['formatted_date'] = date('F Y', strtotime( str_replace('-00', '-01', $row['overview']['completion_date']) ));
    }else{
      $row['overview']['formatted_date'] = date('F js, Y', strtotime($row['overview']['completion_date']));
    }

    // Get related information
    $row['items'] = $this->get_project_items($row['overview']['project_id']);
    $row['disciplines'] = $this->get_project_disciplines($row['overview']['project_id']);
    $row['mediums'] = $this->get_project_mediums($row['overview']['project_id']);
    $row['related_projects'] = $this->get_related_projects($row['overview']['project_id']);

    return $row;
  }

  /**
   * Get the disciplines related to a project
   * @param integer $project_id A unique project ID
   * @return array Summary of disciplines used
   */
  function get_project_disciplines($project_id){
    $query = sprintf("SELECT discipline.*
                      FROM `markr34_portfolio`.`project_discipline`
                      LEFT JOIN `markr34_portfolio`.`discipline` USING(discipline_id)
                      WHERE project_id=%d",
                      mysql_real_escape_string($project_id));
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      $disciplines[$row['discipline_id']] = $row['discipline'];
    return $disciplines;
  }

  /**
   * Get items related to a project
   * @param integer $project_id A unique project ID
   * @return array Summary of items
   */
  function get_project_items($project_id){
    $query = sprintf("SELECT project_item.*
                      FROM `markr34_portfolio`.`project_item`
                      WHERE project_id=%d",
                      mysql_real_escape_string($project_id));
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      $items[] = $row;
    return $items;
  }

  /**
   * Get items related to a project
   * @param integer $project_id A unique project ID
   * @return array Summary of items
   */
  function get_project_keywords($project_id){
    $query = sprintf("SELECT keyword.*
                      FROM `markr34_portfolio`.`project_keyword`
                      LEFT JOIN `markr34_portfolio`.`keyword` USING(keyword_id)
                      WHERE project_id=%d",
                      mysql_real_escape_string($project_id));
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      $disciplines[$row['keyword_id']] = $row['keyword'];
    return $disciplines;
  }

  /**
   * Get the disciplines related to a project
   * @param integer $project_id A unique project ID
   * @return array Summary of disciplines used
   */
  function get_project_mediums($project_id){
    $query = sprintf("SELECT medium.*
                      FROM `markr34_portfolio`.`project_medium`
                      LEFT JOIN `markr34_portfolio`.`medium` USING(medium_id)
                      WHERE project_id=%d",
                      mysql_real_escape_string($project_id));
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      $disciplines[$row['medium_id']] = $row['medium'];
    return $disciplines;
  }

  /**
   * Get the disciplines related to a project
   * @param integer $project_id A unique project ID
   * @return array Summary of disciplines used
   */
  function get_related_projects($project_id){
    $query = sprintf("SELECT related_projects.project_id_A as project_id, project.title, project.url_safe_title
                      FROM `markr34_portfolio`.`related_projects`
                        LEFT JOIN `markr34_portfolio`.`project` ON project.project_id = related_projects.project_id_A
                      WHERE project_id_B=%d",
                      mysql_real_escape_string($project_id));
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      $related_projects[] = $row;
    $query = sprintf("SELECT related_projects.project_id_B as project_id, project.title, project.url_safe_title
                      FROM `markr34_portfolio`.`related_projects`
                        LEFT JOIN `markr34_portfolio`.`project` ON project.project_id = related_projects.project_id_B
                      WHERE project_id_A=%d",
                      mysql_real_escape_string($project_id));
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      $related_projects[] = $row;
    return $related_projects;
  }

} // END Class

?>