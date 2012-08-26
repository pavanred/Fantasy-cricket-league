<?php
    /**
    * o------------------------------------------------------------------------------o
    * | This package is licensed under the Phpguru license. A quick summary is       |
    * | that for commercial use, there is a small one-time licensing fee to pay. For |
    * | registered charities and educational institutes there is a reduced license   |
    * | fee available. You can read more  at:                                        |
    * |                                                                              |
    * |                  http://www.phpguru.org/static/license.html                  |
    * o------------------------------------------------------------------------------o
    *
    * © Copyright 2008 Richard Heyes
    */

    /**
    * A datagrid class. The appearance can be customised with CSS
    * See the example file for how.
    * 
    * CHANGES
    * 
    * 3rd March 2010
    * ==============
    *  o Changed all the short PHP tags (<?) to their longer versions (<?php)
    *
    * 17th August 2008
    * ================
    *  o Added support for array based data sources, as well as MySQL result sets
    *  o Seperated the MySQL/Array handling code out into seperate classes
    *  o Fixed bug with empty result sets
    * 
    * 6th May 2008
    * ============
    *  o Allowed disabling of ordering (completely)
    *  o Paging now has the "paging" CSS class (the next/prev links only). The bit that tells you how many rows
    *    there (x of y results) has the "paging_results" CSS class
    *  o Order by links now preserve existing GET variables - thanks Tom
    *  o Fixed hard coded perPage - thanks David
    *  o Added NoSort() method which, unsurprisingly, disables sorting for a given column (or columns)
    * 
    * 20th April 2008
    * ===============
    *  o Column headers no longer have htmlspecialchars() applied to them, the same as
    *    actual column data
    *  o Added a factory method that creates an RGrid object with the correct ordering
    *
    * 12th April 2008
    * ===============
    *  o Added column ordering support TODO: 1) Allow user to disable sorting
    *    2) Allow configuration of the sort indicator
    *
    * 28th March 2008
    * ===============
    *  o Added example3.php and example4.php
    *
    * 24th March 2008
    * ===============
    *  o Added example2.php which is styled to look like a Windows datagrid
    *
    * xth March 2008
    * ==============
    *  o Changed default colors to be a bit more "jazzy", or less "lame"
    *  o Added header and footer capability
    *  o Table headers - <th> tags now have a col_x class
    *
    * 5th March 2008
    * ==============
    *  o Added a few new methods: GetPageCount(), GetRowCount() and SetPerPage()
    *  o Made sure you can (if you want to) call DisplaY() multiple times on the same page
    *
    * 29th February 2008
    * ==================
    *  o Initial release
    */
    class RGrid
    {
        /**
        * Holds the order by information
        */
        private static $orderby;
        private static $orderdir;
        
        /**
        * Properties. I really can't be bothered right now to document each one,
        * so they're all lumped together here
        */
        public $allowSorting;
        public $showHeaders;
        public $headerHTML;
        public $cellpadding;
        public $cellspacing;
        public $numresults;
        public $startnum;
        public $perPage;
        public $colnum;
        public $noSpecialChars;
        public $colnames;
        public $rowcallback;
        public $headers;

        private $noSort;
        private $initialcols;
        private $connection;
        private $resultset;
        private $hiddenColumns;
        
        /**
        * Creates a datagrid from an array. Similar to a MySQL datasrc
        * 
        * @param array $data The data (array)
        */
        public static function CreateFromArray($array)
        {
            /**
            * Order by
            */
            if (isset($_GET['orderDir']) AND !empty($_GET['orderBy'])) {

                // Store it so the direction indicators appear
                RGrid::$orderby['column']    = $_GET['orderBy'];
                RGrid::$orderby['direction'] = $_GET['orderDir'];
                
                // FIXME - implement sorting
                uasort($array, array('RGrid', '_sortArray'));
           }

            $grid =  new RGrid($array);

            return $grid;
        }

        /**
        * Creates an RGrid object for you and returns it
        *
        * @param resource   $connection The connection to the database. This can also be an array
        *                               containing host/user/pass/dbas parameters to connect to the
        *                               database. This can also be used to create an RGrid from
        *                               an array data source by supplying an array instead of the
        *                               database connection, eg: $grid = RGrid::Create($myArray);
        * @param string     $sql        The SQL query with or without the ORDER BY clause
        */
        public static function Create($connection, $sql = null)
        {
            /**
            * Creates an array based datagrid if the first arg is am array
            */
            if (is_array($connection) AND is_null($sql)) {
                return RGrid::CreateFromArray($connection);
            }

            // Connect if need be
            if (is_array($connection)) {
                $host = $connection['hostname'];
                $user = $connection['username'];
                $pass = $connection['password'];
                $dbas = $connection['database'];
                
                $connection = mysql_connect($host, $user, $pass) OR die('<span style="color: red">Failed to connect: ' . mysql_error() . '</span>');
                
                mysql_select_db($dbas);
            }

            /**
            * Order by
            */
            if (isset($_GET['orderDir']) AND !empty($_GET['orderBy'])) {

                // Store it so the direction indicators appear
                RGrid::$orderby['column']    = $_GET['orderBy'];
                RGrid::$orderby['direction'] = $_GET['orderDir'];
                
                $orderby = 'ORDER BY ' . $_GET['orderBy'] . ' ' . ($_GET['orderDir'] ? 'ASC' : 'DESC');
                $sql = preg_replace('/ORDER\s+BY.*(ASC|DESC)/is', $orderby, $sql);
            }

            /**
            * Perform the query to get the result set
            */

            $resultset = mysql_query($sql, $connection);

            $grid =  new RGrid($connection, $resultset);
            
            // If the query doesn't have an ORDER BY, then disable ordering
            if (strpos($sql, 'ORDER BY') === false) {
                $grid->allowSorting = false;
            }
            return $grid;
        }

        /**
        * The constructor
        * 
        * @param mixed    $connection This can be either a MySQL connection resource or an array
        * @param resource $resultset Only used for MySQL based datagrids - the MySQL result.
        */
        public function __construct($connection, $resultset = null)
        {
            $this->noSort         = array();
            $this->allowSorting   = true;
            $this->showHeaders    = true;
            $this->headerHTML     = '';
            $this->cellpadding    = 0;
            $this->cellspacing    = 0;
            $this->connection     = $connection;
            $this->resultset      = $resultset;
            $this->numresults     = is_resource($connection) ? mysql_num_rows($resultset) : count($connection);
            $this->startnum       = @(int)$_GET['start'];
            $this->perPage        = 20;
            $this->hiddenColumns  = array();
            $this->colnum         = is_resource($connection) ? mysql_num_fields($this->resultset) : count($connection[0]);
            $this->noSpecialChars = array();

            // Don't allow startnum to be lower than zero
            if ($this->startnum < 0) {
                $this->startnum = 0;
            }
            
            // Don't allow startnum to be greater than the number of rows in the result set,
            // well, one less to allow for zero indexing
            if ($this->startnum >= $this->numresults) {
                $this->startnum = 0;
            }

            // Check the MySQL connection is valid
            if (!is_resource($connection) AND !is_array($connection)) {
                die('<p /><span style="color: red">Error - the MySQL connection you have passed to the RGrid constructor is not valid</span>');
            }

            // Check the MySQL result set is valid
            if (is_resource($connection) AND (!$resultset OR !is_resource($resultset))) {
                die('<p /><span style="color: red">Error - the MySQL result set you have passed to the RGrid constructor is not valid</span>');
            }
        }
        
        /**
        * Sets the displayed header names for the columns
        *
        * @param array $cols The column names
        */
        public function SetDisplayNames($cols)
        {
            $this->colnames = $cols;
        }
        
        /**
        * Hides a particular column, or multiple columns
        *
        * @param ... strings One or more column names
        */
        public function HideColumn()
        {
            $this->hiddenColumns = array_unique(func_get_args());
        }
        
        /**
        * Sets the column names (not using the display names) that
        * don't get htmlspecialchars() applied to them
        *
        * @param string ... One or more column names
        */
        public function NoSpecialChars()
        {
            $this->noSpecialChars = func_get_args();
        }
        
        /**
        * This method allows you to specify one or more columns that cannot be sorted by
        * 
        * @param string ... The column name (s). You can specify one or more.
        */
        public function NoSort()
        {
            $args = func_get_args();
            
            foreach ($args as $v) {
                $this->noSort[] = $v;
            }

            // Should do this before running the query, but hey ho.
            if (in_array(RGrid::$orderby['column'], $this->noSort)) {
                die('<span style="color: red">You are not allowed to sort by that column</span>');
            }
        }

        /**
        * Returns the number of pages in the datagrid
        *
        * @return int The number of pages in the datagrid
        */
        public function GetPageCount()
        {
            $count = is_resource($this->connection) ? mysql_num_rows($this->resultset) : count($this->connection);

            return  ceil($count / $this->perPage);
        }
        
        /**
        * Returns the number of rows in the result set.
        *
        * @return int The number of rows
        */
        public function GetRowCount()
        {
            return $this->numresults;
        }
        
        /**
        * I can't see the need for this, but you may. Simply returns the MySQL result set.
        *
        * @return resource The MySQL result set
        */
        public function GetResultset()
        {
            if (is_array($this->connection)) {
                die('<span style="color: red">Cannot get the result set - data source is an array</span>');
            }

            return $this->resultset;
        }
        
        
        /**
        * Returns the MySQL connection
        *
        * @return resource The MySQL resouce
        */
        public function GetConnection()
        {
            if (is_array($this->connection)) {
                die('<span style="color: red">Cannot get the connection - data source is an array</span>');
            }

            return $this->connection;
        }
        
        /**
        * Sets the header HTML./ This is NOT related to the table
        * column headers. This is here purely for decorative purposes.
        *
        * @param string $html The HTML to set
        */
        public function SetHeaderHTML($html)
        {
            $this->headerHTML = $html;
        }

        /**
        * Sets the MySQL connection
        *
        * @param resource $connection The MySQL connection resouce
        */
        public function SetConnection($connection)
        {
            if (is_array($this->connection)) {
                die('<span style="color: red">Cannot set the connection - data source is an array</span>');
            }

            $this->connection = $connection;
        }

        /**
        * This function sets the amount of rows to display
        * per page
        *
        * @param int $perPage How many rows to show per page
        */
        public function SetPerPage($perPage)
        {
            $this->perPage = $perPage;
        }
        
        /**
        * For whatever reason you can use this to set the MySQL
        * result set
        *
        * @param resource $result The MySQL result set. If you do use this method, it
        *                          should come before the call to Display
        */
        public function SetResultset($resultset)
        {
            if (is_array($this->connection)) {
                die('<span style="color: red">Cannot set the result set - data source is an array</span>');
            }

            $this->resultset  = $resultset;
            $this->numresults = mysql_num_rows($this->resultset);
            $this->colnum     = mysql_num_fields($this->resultset) - count($this->hiddenColumns);
        }
        
        /**
        * Adds a rowcallback function which gets called just before each row is going
        * to be displayed
        *
        * @param string &$row The function name that is the callback function.
        */
        public function AddCallback($callback)
        {
            $this->rowcallback = $callback;
        }

        /**
        * Shows the datagrid.
        */
        function Display()
        {
            /**
            * Seek to the correct place in the result set
            */
            if (is_array($this->connection)) {
                $this->orig_array = $this->connection;
                $this->connection = array_slice($this->connection, $this->startnum, $this->perPage);
            } else {
                if (mysql_num_rows($this->resultset)) {
                    mysql_data_seek($this->resultset, $this->startnum);
                }
            }

            /**
            * Initialise the row number
            */
            $rownum = 0;

            /**
            * Get the headers from the first row, then seek back to zero
            */
            $row = is_array($this->connection) ? $this->connection[0] : mysql_fetch_array($this->resultset, MYSQL_ASSOC);
            $this->headers     = !empty($row) ? array_keys($row) : array();
            $this->initialcols = count($row);
            $this->colnum = (is_array($this->connection) ? count($row) : mysql_num_fields($this->resultset)) - count($this->hiddenColumns);
            is_array($this->connection) || mysql_num_rows($this->resultset) == 0 ? null : mysql_data_seek($this->resultset, $this->startnum);
            $rowcount = 0;

            ?>
<script language="javascript" type="text/javascript">
<!--
    /**
    * The row mouseover function
    */
    function MouseOver(rownum)
    {
        var tags = document.getElementsByTagName('td')

        for (var i=0; i<tags.length; i++) {
            if(tags[i].className.indexOf('row_' + rownum + ' ') != -1) {
                tags[i].className = tags[i].className += ' mouseover';
            };
        }
    }
    
    /**
    * the row mouseout function
    */
    function MouseOut(rownum)
    {
        var tags = document.getElementsByTagName('td')

        for (var i=0; i<tags.length; i++) {
            if(tags[i].className.indexOf('row_' + rownum) != -1) {
                tags[i].className = tags[i].className.replace(/ mouseover/, '');
            };
        }
    }
// -->
</script>
<table border="0" cellspacing="<?php echo $this->cellspacing ?>" cellpadding="<?php echo $this->cellpadding ?>" class="datagrid">
    <thead>
        <?php if($this->headerHTML): ?>
            <tr>
                <th id="header" colspan="<?php echo $this->colnum ?>">
                    <?php echo $this->headerHTML ?>
                </th>
            </tr>
        <?php endif ?>

        <?php if($this->showHeaders): ?>
                <tr>
                    <?php foreach($this->headers as $k => $h): ?>
                        <?php if(in_array($h, $this->hiddenColumns)) continue ?>

                        <th class="col_<?php echo $k ?>" title="<?php echo ($printable = !empty($this->colnames[$h]) ? $this->colnames[$h] : $h) ?>">

                            <?php if($this->allowSorting AND !in_array($h, $this->noSort)): ?>
                                <a href="<?php echo $this->getQueryString() ?>&orderBy=<?php echo $h ?>&orderDir=<?php echo (!empty($_GET['orderDir']) && $_GET['orderBy'] == $h ? 0 : 1) ?>">
                                    <?php echo $printable ?>
                                </a>
                            
                            <?php else: ?>
                                <?php echo $printable ?>
                            <?php endif ?>
                            
                            <?php if($this->allowSorting): ?>
                                <!-- The order indicator -->
    
                                <?php if($h == RGrid::$orderby['column']): ?>
                                    <span style="font-family: WebDings">
                                        <?php echo (!empty(RGrid::$orderby['direction']) && trim(RGrid::$orderby['direction']) == 1 ? 5 : 6)?>
                                    </span>
                                <?php endif?>
                            <?php endif?>
                        </th>
                    <?php endforeach?>
                </tr>
        <?php endif?>
    </thead>

    <tbody>
        <?php while($row = (is_array($this->connection) ? current($this->connection) : mysql_fetch_array($this->resultset, MYSQL_ASSOC))):?>

            <?php $colnum = 0; @$rowcount++?>

            <?php if($this->rowcallback):?>
                <?php //call_user_func($this->rowcallback, &$row)?>
				
            <?php endif?>

            <tr onmouseover="MouseOver(<?php echo intval($rownum)?>)" onmouseout="MouseOut(<?php echo intval($rownum)?>)">
                <?php foreach($row as $k => $v):?>

                    <?php if(in_array($k, $this->hiddenColumns)) continue?>

                    <td class="row_<?php echo intval($rownum)?> col_<?php echo (!empty($colnum) ? $colnum : 0)?> <?php if($rownum % 2 == 1):?>altrow<?php endif?>  <?php if($colnum % 2 == 1):?>altcol<?php endif?>">
                        <?php echo (in_array($k, $this->noSpecialChars) ? $v : htmlspecialchars($v))?>
                    </td>
                    
                    <?php $colnum++?>
                <?php endforeach?>
            </tr>

            <?php if($rownum++ == ($this->perPage - 1) ) break?>
            <?php if(is_array($this->connection)): next($this->connection); endif?>
        <?php endwhile?>
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="<?php echo $this->colnum?>" class="paging">
                <?php if(@$this->startnum > 0):?>
                    <span style="float: left">
                        <a href="<?php echo $this->getQueryString(intval($this->startnum) - $this->perPage)?>">
                            &laquo; Prev
                        </a>
                    </span>
                <?php endif?>


                <?php if($this->numresults > (@$this->startnum + $this->perPage)):?>
                    <span style="float: right">
                        <a href="<?php echo $this->getQueryString(intval($this->startnum) + $this->perPage)?>">
                            Next &raquo;
                        </a>
                    </span>
                <?php endif?>
            </td>
        </tr>
        
        <tr>
            <td align="center" colspan="<?php echo $this->colnum?>" class="paging_results">
                <?php echo ($this->numresults > 0 ? intval($this->startnum) + 1 : 0)?>-<?php echo (intval($this->startnum) + $rowcount)?> of <?php echo intval($this->numresults)?> results
            </td>
        </tr>
    </tfoot>
</table>
            <?php
        }
        
        /**
        * A private method used to build the query string
        *
        * @param  int    The starting number
        * @return string The query string
        */
        private function getQueryString($startnum = null)
        {
            if ($startnum === null) {
                $startnum = !empty($_GET['start']) ? $_GET['start'] : 0;
            }

            $_GET['start'] = $startnum;

            $qs = '?';
            foreach ($_GET as $k => $v) {
                $qs .= urlencode($k) . '=' . urlencode($v)  . '&';
            }

            // If the query string is just a question mark, lose it
            if ($qs == '?') {
                $qs = '';
            }

            return preg_replace('/&$/', '', $qs);
        }
        
        /**
        * Sort an array based datagrid
        */
        private function _sortArray($a, $b)
        {
            if (empty(RGrid::$orderby)) {
                RGrid::$orderby  = key($a);
                RGrid::$orderdir = 1; // Ascending
            }

            // Ascending
            if (RGrid::$orderby['direction']) {
                if ($a[RGrid::$orderby['column']] > $b[RGrid::$orderby['column']]) {
                    return 1;
                } elseif ($a[RGrid::$orderby['column']] < $b[RGrid::$orderby['column']]) {
                    return -1;
                } else {
                    return 0;
                }

            // Descending
            } else {

                if ($a[RGrid::$orderby['column']] > $b[RGrid::$orderby['column']]) {
                    return -1;
                } elseif ($a[RGrid::$orderby['column']] < $b[RGrid::$orderby['column']]) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }
?>