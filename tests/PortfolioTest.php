<?php

class PortfolioTest extends PHPUnit_Framework_TestCase {

    protected $Portfolio;

    public function setup() {

        // Connect to database using PDO
        $pdo_connection = null;
        try {
            $pdo_connection = new \PDO('mysql:host=localhost;dbname=portfolio;charset=utf8', 'portfolio_user', 'portfolio_pass');
            $pdo_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log('ERROR: ' . $e->getMessage() . "\n");
        }

        $this->Portfolio = new MarkRoland\Portfolio($pdo_connection);
    }

    public function test_get_all_projects() {
        $this->assertCount(64, $this->Portfolio->get_all_projects());
    }

    public function test_get_project_by_id()  {
        $project = $this->Portfolio->get_projects('id', 64);
        $this->assertSame('julian', $project[0]['overview']['url_safe_title']);
    }

    public function test_get_project_by_url()  {
        $project = $this->Portfolio->get_projects('url_safe_title', 'julian');
        $this->assertSame('julian', $project[0]['overview']['url_safe_title']);
    }

    public function test_get_projects_by_keyword()  {
        $project = $this->Portfolio->get_projects('keyword', 'Internet of Things');
        $this->assertSame('64', $project[0]['overview']['project_id']);
    }

    public function test_get_projects_by_medium()  {
        $projects = $this->Portfolio->get_projects('medium', 'HTML');
        $this->assertCount(17, $projects);
    }

    public function test_get_projects_by_discipline()  {
        $projects = $this->Portfolio->get_projects('discipline', 'engineering');
        $this->assertCount(7, $projects);
    }

    /*
    public function test_get_projects_by_popularity()  {
        $project = $this->Portfolio->get_projects('popularity');
        $this->assertSame('julian', $project[0]['overview']['url_safe_title']);
    }
    */

    public function test_get_projects_by_random()  {
        $project = $this->Portfolio->get_projects('random', null, 1);
        $this->assertCount(1, $project);
    }

    public function test_get_projects_by_random_offset()  {
        $project = $this->Portfolio->get_projects('random', null, 1, 1);
        $this->assertCount(1, $project);
    }

    /*
    public function test_get_projects_by_title()  {
        $project = $this->Portfolio->get_projects('title', 64);
        $this->assertSame('julian', $project[0]['overview']['url_safe_title']);
    }
    */

    public function test_get_project_disciplines()  {
        $results = $this->Portfolio->get_project_disciplines(64);
        $this->assertCount(4, $results);
    }

    public function test_get_project_items()  {
        $results = $this->Portfolio->get_project_items('project_id', 64);
        $this->assertCount(4, $results);
    }

    public function test_get_project_keywords()  {
        $results = $this->Portfolio->get_project_keywords(64);
        $this->assertCount(2, $results);
    }

    public function test_get_project_mediums()  {
        $results = $this->Portfolio->get_project_mediums(64);
        $this->assertCount(5, $results);
    }

    public function test_get_related_projects() {
        $results = $this->Portfolio->get_related_projects(31);
        $this->assertCount(2, $results);
    }

    public function test_get_project_hits() {
        $results = $this->Portfolio->get_project_hits(48, '2012-10-25', '2012-10-26');
        $this->assertSame('5', $results['data'][1]);
    }
}