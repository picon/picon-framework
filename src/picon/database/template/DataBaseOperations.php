<?php

/**
 * Picon Framework
 * http://code.google.com/p/picon-framework/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Picon Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Picon Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Picon Framework.  If not, see <http://www.gnu.org/licenses/>.
 * */

namespace picon;

/**
 * All database operations that are expected
 * @author Martin Cassidy
 * @package database/template
 */
interface DataBaseOperations
{
	/**
	 * Run a query of given SQL.
	 * Invoke the row mapper for each result found
	 * Replace values in the query with the passed arguments. Query
	 * values should be a type specifier
	 */
	function query($sql, RowMapper $mapper, $arguments = null);

	/**
	 * Run a query of given SQL.
	 * Replace values in the query with the passed arguments. Query
	 * values should be a type specifier
	 * @return the number of affected records
	*/
	function update($sql, $arguments = null);

	/**
	 * Run the given SQL
	*/
	function execute($sql);

	/**
	 * Run the given SQL, with arguments
	 * A single record with a single column is expected. This will be returned
	 * as an integer
	*/
	function queryForInt($sql, $arguments = null);

	/**
	 * Run the query and return the last inserted it
	*/
	function insert($sql, $arguments = null);
}

?>
