<?php 

class HTMLPageDevelopToolMigrations extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '/migrations';

	/*-------------------------------------------------------------*/

	public static function ajax_add_migration()
	{
		$json = new AjaxJSONFormResponse();

		$json->set_error('No se pudo crear la migracion');

		$name = trim($_POST['name']);
		$migration = ZfMigration::generate_migration_files($name);

		if($migration)
		{
			$json->set_success(true);
		}
		else
		{
			$json->set_error('No se pudo crear la migración');
		}

		$json->out();
	}

	/*-----------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Migrations';
	}
	
	protected static function _get_show_index()
	{
		return true;
	}
	
	/*-----------------------------------------*/

	protected $_processed_migrations;
	protected $_migrations;
	protected $_selected_id_migration;
	protected $_migrations_success = false;

	public function __construct() {
		
		parent::__construct();

		$this->_process_post();

		$this->_processed_migrations = ZfMigration::list_processed_migrations_id();
		$this->_migrations = ZfMigration::list_migrations_id();
		$this->_selected_id_migration = ZfMigration::get_last_processed_migration_id();

		if(!empty($this->_migrations) && count($this->_migrations) > count($this->_processed_migrations))
		{
			$this->_selected_id_migration = $this->_migrations[count($this->_migrations)-1];
		}
	}

	public function _process_post()
	{

		if($_POST['id_migration'])
		{
			$id_migration = $_POST['id_migration'];

			if(!$id_migration)
			{
				return;
			}

			$processed_migrations = ZfMigration::list_processed_migrations_id();
			$migrations = ZfMigration::list_migrations_id();
			$selected_id_migration = ZfMigration::get_last_processed_migration_id();

			if($id_migration == '-1')
			{
				$migrations_rollback = array_reverse($processed_migrations);

				foreach($migrations_rollback as $id_migration)
				{
					$migration = ZfMigration::get_by_id_migration($id_migration);
					$migration->migration_rollback();
				}

				$this->_migrations_success = true;

			}
			else if($id_migration == $selected_id_migration)
			{
				$this->_migrations_success = true;
				return;
			}
			else if(in_array($id_migration, $processed_migrations))
			{
				$this->_migrations_success = true;
				$pos = array_search($id_migration, $processed_migrations);
				$migrations_rollback = array_slice($processed_migrations, $pos+1);
				$migrations_rollback = array_reverse($migrations_rollback);

				foreach($migrations_rollback as $id_migration)
				{
					$migration = ZfMigration::get_by_id_migration($id_migration);
					$migration->migration_rollback();
				}
			}
			else
			{
				$this->_migrations_success = true;
				$count_processed_migrations = count($processed_migrations);
				$pos = array_search($id_migration, $migrations);
				$run_migrations = array_slice($migrations, $count_processed_migrations, $pos - $count_processed_migrations + 1);

				if(empty($run_migrations))
				{
					$run_migrations[] = $id_migration;
				}

				foreach($run_migrations as $id_migration)
				{
					$migration = new ZfMigration();
					$migration->set_id_migration($id_migration);
					$migration->save();
					$migration->migration_run();
				}
			}

			$this->_migrations_success = true;
		}
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('processed_migrations', $this->_processed_migrations);
		$this->set_param('migrations', $this->_migrations); 
		$this->set_param('selected_id_migration', $this->_selected_id_migration);
		$this->set_param('migrations_success', $this->_migrations_success);
	}
}
