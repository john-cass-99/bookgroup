<?php
class Status {
	public const SUGGESTED = 0;
	public const CHOSEN = 2;
	public const DELETED = 3;

	public static function ToString($i)
	{
		return( ['Suggested', 'Not Used', 'Chosen', 'Deleted'][$i]);
	}
}
?>