<?php

class ZfMigration extends ZfMigrationCache
{


	protected static function _generate_id_migration($name)
	{
		$date = strftime('%Y%m%d_%H%M%S');
		$name = preg_replace('#[^\w]+#', '', $name);
		$name = strtolower($name);
		return "{$date}_{$name}";
	}

	protected static function _get_migration_run_file($id_migration)
	{
		$migrations_dir = ZPHP::get_config('db.sql_migrations.dir');
		$filename_format = ZPHP::get_config('db.sql_migrations.run_format');
		$filename = sprintf($filename_format, $id_migration);
		$path = "{$migrations_dir}/{$filename}";
		return $path;
	}

	protected static function _get_migration_rollback_file($id_migration)
	{
		$migrations_dir = ZPHP::get_config('db.sql_migrations.dir');
		$filename_format = ZPHP::get_config('db.sql_migrations.rollback_format');
		$filename = sprintf($filename_format, $id_migration);
		$path = "{$migrations_dir}/{$filename}";
		return $path;
	}

	/**
	 *
	 * @return ZfMigration
	 *
	 */

	public static function generate_migration_files($name)
	{
		$id_migration = self::_generate_id_migration($name);

		$run_file = self::_get_migration_run_file($id_migration);
		$rollback_file = self::_get_migration_rollback_file($id_migration);

		if(touch($run_file) && touch($rollback_file))
		{
//			$migration = new ZfMigration();
//			$migration->set_id_migration($id_migration);
//			$migration->save();
			return true;
		}

		return false;

	}


	public static function list_processed_migrations_id()
	{
		return self::list_all_column('id_migration');
	}

	public static function list_migrations_id()
	{
		$migrations_id = array();

		$dirname = ZPHP::get_config('db.sql_migrations.dir');
		$files = FilesHelper::dir_list($dirname);

		$filename_format = ZPHP::get_config('db.sql_migrations.run_format');
		$filename_pattern = '#'.sprintf($filename_format, '(?P<id_migration>.+)').'#';

		foreach($files as $filename)
		{
			if(preg_match($filename_pattern, $filename, $match))
			{
				$migrations_id[] = $match['id_migration'];
			}
		}

		sort($migrations_id);

		return $migrations_id;
	}

	public static function get_last_processed_migration_id()
	{
		$id_migrations = self::list_processed_migrations_id();

		if(!empty($id_migrations))
		{
			return array_pop($id_migrations);
		}
	}

	/*-------------------------------------------------------------*/

	public function get_rollback_file()
	{
		return self::_get_migration_rollback_file($this->id_migration);
	}

	public function get_run_file()
	{
		return self::_get_migration_run_file($this->id_migration);
	}

	public function migration_rollback()
	{
		$file = $this->get_rollback_file();
		$sql = file_get_contents($file);

		$connection = DBConnection::get_default_connection();
		$connection->query($sql);

		self::delete_by_id_migration($this->id_migration);
	}

	public function migration_run()
	{
		$file = $this->get_run_file();
		$sql = file_get_contents($file);

		$connection = DBConnection::get_default_connection();
		$connection->query($sql);
	}


	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

}

