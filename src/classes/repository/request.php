<?php
/**
 * @package     Joomla.Tester
 * @subpackage  Repository
 *
 * @copyright   Copyright (C) 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * CRUD class for repository Pull Requests.
 *
 * @package     Joomla.Tester
 * @subpackage  Repository
 * @since       1.0
 */
class PTRepositoryRequest extends JTable
{

	public $pull_id;

	public $github_id;

	public $milestone_id;

	public $title;

	public $state;

	public $is_mergeable;

	public $is_merged;

	public $user;

	public $avatar_url;

	public $created_time;

	public $updated_time;

	public $closed_time;

	public $merged_time;

	public $data;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   1.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__pull_requests', 'pull_id', $db);

		$this->data = (object) array();
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True if the object is ok
	 *
	 * @see     JTable::check
	 * @since   1.0
	 */
	public function check()
	{
		if (trim($this->github_id) == '' || trim($this->title) == '')
		{
			return false;
		}
		return true;
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   mixed  $src     Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable::bind
	 * @since   1.0
	 */
	public function bind($src, $ignore = '')
	{
		if (is_object($src) && isset($src->data) && is_scalar($src->data))
		{
			$src->data = json_decode($src->data);
		}
		elseif (is_array($src) && isset($src['data']) && is_scalar($src['data']))
		{
			$src['data'] = json_decode($src['data']);
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Method to create and execute a SELECT WHERE query.
	 *
	 * @param   array  $options  Array of options
	 *
	 * @return  string  The database query result
	 *
	 * @since   1.0
	 */
	public function find($options = array())
	{
		// Get the JDatabaseQuery object
		$query = $this->_db->getQuery(true);

		foreach ($options as $col => $val)
		{
			$query->where($col . ' = ' . $this->_db->quote($val));
		}

		$query->select($this->_db->quoteName($this->_tbl_key));
		$query->from($this->_db->quoteName($this->_tbl));
		$this->_db->setQuery($query);

		return $this->_db->loadColumn(0);
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @see     JTable::load
	 * @since   1.0
	 */
	public function load($keys = null, $reset = true)
	{
		$success = parent::load($keys, $reset);

		// Keep the data property unserialized.
		if ($success && !empty($this->data) && is_string($this->data))
		{
			$this->data = json_decode($this->data);
		}

		return $success;
	}
	/**
	 * Overrides JTable::store to check unique fields.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JTable::store
	 * @since   1.0
	 */
	public function store($updateNulls = false)
	{
		// Serialize the data property for storage.
		if (isset($this->data) && !is_scalar($this->data))
		{
			$this->data = json_encode($this->data);
		}

		$success = parent::store($updateNulls);

		// Keep the data property during standard usage.
		if (!empty($this->data))
		{
			$this->data = json_decode($this->data);
		}

		return $success;
	}
}
