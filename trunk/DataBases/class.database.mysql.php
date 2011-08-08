<?php
/**
 * @revision      $Id: class.images.helper.php 335 2011-08-08 10:49:11Z agbiggora@gmail.com $
 * @created       Jul 2, 2010
 * @package       DataBase
 * @subpackage	  MySQL
 * @category      Tools
 * @version       1.0.4
 * @desc          Basic manipulation with image
 * @copyright     Copyright Alexey Gordeyev IK © 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.gordejev.lv/
 * @link          http://www.agjoomla.com/
 * @source        http://code.google.com/p/ag-php-classes/wiki/ImagesHelper
 */

Class DataBase
{
   var $_sql	   = null;
   var $_limit	   = null;
   var $_offset	   = null;
   var $_resource  = null;
   var $_cursor    = null;
   var $_quoted    = null;
   var $_nameQuote = null;
   var $_hasQuoted = null;

   /**
    *
    * Class Constructor
    * @return  void
    */
   function __construct()
   {
      $this->_resource = mysql_connect($this->db_host, $this->db_user, $this->db_pass);
      if (!$this->_resource) {
         $this->_errorNum = mysql_errno( $this->_resource );
         $this->_errorMsg = mysql_error( $this->_resource );
      }
      else{
         $db_selected = mysql_select_db($this->db_base, $this->_resource);
         if (!$db_selected) {
            $this->_errorNum = mysql_errno( $this->_resource );
            $this->_errorMsg = mysql_error( $this->_resource );
         }
         else{
            $this->setUTF();
         }
      }
   }

   /**
    *  @method      setUtf
    *  @return      bool
    *  @description set UTF-8 encoding for mysql connection
    */
   function setUTF()
   {
      $query = "SET NAMES utf8";
      if(mysql_query($query,$this->_resource)) { return true; }
      else { return false; }
   }

   /**
    *  @method      SetDBUser
    *  @params      string
    *  @return      bool
    *
    */
   function SetDBUser($user)
   {
      $this->db_user = $user;
      return true;
   }

   /**
    *  @method      SetDBBase
    *  @params      string
    *  @return      bool
    *
    */
   function SetDBBase($base)
   {
      $this->db_base = $base;
      return true;
   }

   /**
    *  @method      SetDBHost
    *  @params      string
    *  @return      bool
    *
    */
   function SetDBHost($host)
   {
      $this->db_host = $host;
      return true;
   }
   
   /**
    *  @method      SelectBase
    *  @return      bool
    *
    */
   function SelectBase()
   {
      if(mysql_select_db($this->db_base)) { return true;  }
      else { return false; }
   }

   /**
    *  @method      GetInfo
    *  @return      bool
    *
    */
   function GetInfo()
   {

   }

   /**
    * Sets the SQL query string for later execution.
    *
    * This function replaces a string identifier <var>$prefix</var> with the
    * string held is the <var>_table_prefix</var> class variable.
    *
    * @access public
    * @param  string The SQL query
    * @param  string The offset to start selection
    * @param  string The number of results to return
    * @param  string The common table prefix
    */
   function setQuery( $sql, $offset = 0, $limit = 0)
   {
      $this->_sql	  = $sql;
      $this->_limit	  = (int) $limit;
      $this->_offset  = (int) $offset;
   }

   /**
    * Get the active query
    *
    * @access public
    * @return string The current value of the internal SQL vairable
    */
   function getQuery()
   {
      return $this->_sql;
   }

   /**
    * Execute the query
    *
    * @access	public
    * @return mixed A database resource if successful, FALSE if not.
    */
   function query()
   {
      if (!is_resource($this->_resource)) {
         return false;
      }

      // Take a local copy so that we don't modify the original query and cause issues later
      $sql = $this->_sql;
      if ($this->_limit > 0 || $this->_offset > 0) {
         $sql .= ' LIMIT '.$this->_offset.', '.$this->_limit;
      }
      if ($this->_debug) {
         $this->_ticker++;
         $this->_log[] = $sql;
      }
      $this->_errorNum = 0;
      $this->_errorMsg = '';
      $this->_cursor = mysql_query( $sql, $this->_resource );

      if (!$this->_cursor)
      {
         $this->_errorNum = mysql_errno( $this->_resource );
         $this->_errorMsg = mysql_error( $this->_resource )." SQL=$sql";

         if ($this->_debug) {
            // Action
         }
         return false;
      }
      return $this->_cursor;
   }

   /**
    * Get a database escaped string
    *
    * @param	string	The string to be escaped
    * @param	boolean	Optional parameter to provide extra escaping
    * @return	string
    * @access	public
    * @abstract
    */
   function getEscaped( $text, $extra = false )
   {
      $result = mysql_real_escape_string( $text, $this->_resource );
      if ($extra) {
         $result = addcslashes( $result, '%_' );
      }
      return $result;
   }

   /**
    * Description
    *
    * @access public
    * @return int The number of affected rows in the previous operation
    * @since  1.0.5
    */
   function getAffectedRows()
   {
      return mysql_affected_rows( $this->_resource );
   }

   /**
    * Description
    *
    * @access public
    * @return int The number of rows returned from the most recent query.
    */
   function getNumRows( $cur=null )
   {
      if(!$cur) {
         $this->_offset = 0;
         $this->_limit = 0;
         $this->query();
      }
      return mysql_num_rows( $cur ? $cur : $this->_cursor );
   }


   /**
    * This method loads the first field of the first row returned by the query.
    *
    * @access public
    * @return The value returned in the query or null if the query failed.
    */
   function loadResult()
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $ret = null;
      if ($row = mysql_fetch_row( $cur )) {
         $ret = $row[0];
      }
      mysql_free_result( $cur );
      return $ret;
   }
   /**
    * Load an array of single field results into an array
    *
    * @access	public
    */
   function loadResultArray($numinarray = 0)
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $array = array();
      while ($row = mysql_fetch_row( $cur )) {
         $array[] = $row[$numinarray];
      }
      mysql_free_result( $cur );
      return $array;
   }


   /**
    * Fetch a result row as an associative array
    *
    * @access public
    * @return array
    */
   function loadAssoc()
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $ret = null;
      if ($array = mysql_fetch_assoc( $cur )) {
         $ret = $array;
      }
      mysql_free_result( $cur );
      return $ret;
   }

   /**
    * Load a assoc list of database rows
    *
    * @access public
    * @param  string The field name of a primary key
    * @return array If <var>key</var> is empty as sequential list of returned records.
    */
   function loadAssocList( $key='' )
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $array = array();
      while ($row = mysql_fetch_assoc( $cur )) {
         if ($key) {
            $array[$row[$key]] = $row;
         } else {
            $array[] = $row;
         }
      }
      mysql_free_result( $cur );
      return $array;
   }

   /**
    * This global function loads the first row of a query into an object
    *
    * @access	public
    * @return 	object
    */
   function loadObject( )
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $ret = null;
      if ($object = mysql_fetch_object( $cur )) {
         $ret = $object;
      }
      mysql_free_result( $cur );
      return $ret;
   }

   /**
    * Load a list of database objects
    *
    * If <var>key</var> is not empty then the returned array is indexed by the value
    * the database key.  Returns <var>null</var> if the query fails.
    *
    * @access public
    * @param  string The field name of a primary key
    * @return array If <var>key</var> is empty as sequential list of returned records.
    */
   function loadObjectList( $key='' )
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $array = array();
      while ($row = mysql_fetch_object( $cur )) {
         if ($key) {
            $array[$row->$key] = $row;
         } else {
            $array[] = $row;
         }
      }
      mysql_free_result( $cur );
      return $array;
   }

   /**
    * Load row
    *
    * @access	public
    * @return The first row of the query.
    */
   function loadRow()
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $ret = null;
      if ($row = mysql_fetch_row( $cur )) {
         $ret = $row;
      }
      mysql_free_result( $cur );
      return $ret;
   }

   /**
    * Load a list of database rows (numeric column indexing)
    *
    * @access public
    * @param  string The field name of a primary key
    * @return array If <var>key</var> is empty as sequential list of returned records.
    * If <var>key</var> is not empty then the returned array is indexed by the value
    * the database key.  Returns <var>null</var> if the query fails.
    */
   function loadRowList( $key=null )
   {
      if (!($cur = $this->query())) {
         return null;
      }
      $array = array();
      while ($row = mysql_fetch_row( $cur )) {
         if ($key !== null) {
            $array[$row[$key]] = $row;
         } else {
            $array[] = $row;
         }
      }
      mysql_free_result( $cur );
      return $array;
   }

   /**
    * Inserts a row into a table based on an objects properties
    *
    * @access	public
    * @param	string	The name of the table
    * @param	object	An object whose properties match table fields
    * @param	string	The name of the primary key. If provided the object property is updated.
    */
   function insertObject( $table, &$object, $keyName = NULL )
   {
      $fmtsql = 'INSERT INTO '.$this->nameQuote($table).' ( %s ) VALUES ( %s ) ';
      $fields = array();
      foreach (get_object_vars( $object ) as $k => $v) {
         if (is_array($v) or is_object($v) or $v === NULL) {
            continue;
         }
         if ($k[0] == '_') { // internal field
            continue;
         }
         $fields[] = $this->nameQuote( $k );
         $values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
      }
      $this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
      if (!$this->query()) {
         return false;
      }
      $id = $this->insertid();
      if ($keyName && $id) {
         $object->$keyName = $id;
      }
      return true;
   }

   /**
    * Update object in to table
    *
    * @access public
    * @param  string The name of table
    * @param  object The object to by updated
    * @param  string Primary key name
    * @param  bool $updateNulls
    *
    */
   function updateObject( $table, &$object, $keyName, $updateNulls=true )
   {
      $fmtsql = 'UPDATE '.$this->nameQuote($table).' SET %s WHERE %s';
      $tmp = array();
      foreach (get_object_vars( $object ) as $k => $v)
      {
         if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
            continue;
         }
         if( $k == $keyName ) { // PK not to be updated
            $where = $keyName . '=' . $this->Quote( $v );
            continue;
         }
         if ($v === null)
         {
            if ($updateNulls) {
               $val = 'NULL';
            } else {
               continue;
            }
         } else {
            $val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
         }
         $tmp[] = $this->nameQuote( $k ) . '=' . $val;
      }
      $this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
      return $this->query();
   }

   /**
    * Retun last inserted id
    *
    * @access public
    * @return mixed
    */
   function insertid()
   {
      return mysql_insert_id( $this->_resource );
   }
   /**
    * Show a list of the tables in to database
    *
    * @access  public
    * @return  array A list of all the tables in the database
    */
   function getTableList()
   {
      $this->setQuery( 'SHOW TABLES' );
      return $this->loadResultArray();
   }
   /**
    * Shows the CREATE TABLE statement that creates the given tables
    *
    * @access	public
    * @param 	mixed A table name or a list of table names
    * @return 	array A list the create SQL for the tables
    */
   function getTableCreate( $tables )
   {
      settype($tables, 'array'); //force to array
      $result = array();

      foreach ($tables as $tblval) {
         $this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
         $rows = $this->loadRowList();
         foreach ($rows as $row) {
            $result[$tblval] = $row[1];
         }
      }

      return $result;
   }

   /**
    * Retrieves information about the given tables
    *
    * @access	public
    * @param 	array|string 	A table name or a list of table names
    * @param	boolean			Only return field types, default true
    * @return	array An array of fields by table
    */
   function getTableFields( $tables, $typeonly = true )
   {
      settype($tables, 'array'); //force to array
      $result = array();

      foreach ($tables as $tblval)
      {
         $this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
         $fields = $this->loadObjectList();

         if($typeonly)
         {
            foreach ($fields as $field) {
               $result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
            }
         }
         else
         {
            foreach ($fields as $field) {
               $result[$tblval][$field->Field] = $field;
            }
         }
      }

      return $result;
   }

   /**
    * Adds a field or array of field names to the list that are to be quoted
    *
    * @access public
    * @param  mixed Field name or array of names
    * @since n1.5
    */
   function addQuoted( $quoted )
   {
      if (is_string( $quoted )) {
         $this->_quoted[] = $quoted;
      } else {
         $this->_quoted = array_merge( $this->_quoted, (array)$quoted );
      }
      $this->_hasQuoted = true;
   }

   /**
    * Checks if field name needs to be quoted
    *
    * @access public
    * @param string The field name
    * @return bool
    */
   function isQuoted( $fieldName )
   {
      if ($this->_hasQuoted) {
         return in_array( $fieldName, $this->_quoted );
      } else {
         return true;
      }
   }

   /**
    * Quote an identifier name (field, table, etc)
    *
    * @access	public
    * @param	string	The name
    * @return	string	The quoted name
    */
   function nameQuote( $s )
   {
      // Only quote if the name is not using dot-notation
      if (strpos( $s, '.' ) === false)
      {
         $q = $this->_nameQuote;
         if (strlen( $q ) == 1) {
            return $q . $s . $q;
         } else {
            return $q{0} . $s . $q{1};
         }
      }
      else {
         return $s;
      }
   }

   /**
    * Get a quoted database escaped string
    *
    * @param	string	A string
    * @param	boolean	Default true to escape string, false to leave the string unchanged
    * @return	string
    * @access public
    */
   function Quote( $text, $escaped = true )
   {
      return '\''.($escaped ? $this->getEscaped( $text ) : $text).'\'';
   }

   /**
    * ADODB compatability function
    *
    * @access	public
    * @param	string SQL
    * @since	1.5
    */
   function GetCol( $query )
   {
      $this->setQuery( $query );
      return $this->loadResultArray();
   }
   /**
    * ADODB compatability function
    *
    * @access public
    * @param string SQL
    * @return array
    * @since 1.5
    */
   function GetRow( $query )
   {
      $this->setQuery( $query );
      $result = $this->loadRowList();
      return $result[0];
   }
   /**
    * ADODB compatability function
    *
    * @access public
    * @param string SQL
    * @return mixed
    * @since 1.5
    */
   function GetOne( $query )
   {
      $this->setQuery( $query );
      $result = $this->loadResult();
      return $result;
   }
   /**
    * ADODB compatability function
    *
    * @since 1.5
    */
   function ErrorMsg()
   {
      return $this->getErrorMsg();
   }

   /**
    * ADODB compatability function
    *
    * @since 1.5
    */
   function ErrorNo()
   {
      return $this->getErrorNum();
   }


   function __destruct()
   {


   }

}

?>