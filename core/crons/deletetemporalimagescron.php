<?php

class DeleteTemporalImagesCron extends Cron
{
	const DIFFERENCE_HOURS = 12;

	protected function _run_cron()
	{
		@ set_time_limit(0);

		$images = ZfImageFile::delete_rows(array(array('time_to_sec(timediff(now(),date_added )) / 3600 < '.self::DIFFERENCE_HOURS), 'temporal'));

	}
}