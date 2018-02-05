<?php

class Table
{
    public $debug = TRUE;
    protected $db_pdo;


    public function tablePointsBoard(){


        $aColumns = array( '`id`', '`public_key`', '`points`');


        // Indexed column (used for fast and accurate table cardinality)
        $sIndexColumn = 'id';

        // DB table to use
        $sTable = 'users';
        // Input method (use $_GET, $_POST or $_REQUEST)
        $input = $_GET;

        /**
         * Paging
         */
        $firstResult = "";
        $maxResults = "";
        if ( isset( $input['iDisplayStart'] ) && $input['iDisplayLength'] != '-1' ) {
            $firstResult = intval( $input['iDisplayStart'] );
            $maxResults = intval( $input['iDisplayLength'] );
        }else{

        }


        /**
         * Ordering
         */
        $aOrderingRules = array();
        if ( isset( $input['iSortCol_0'] ) ) {
            $iSortingCols = intval( $input['iSortingCols'] );
            for ( $i=0 ; $i<$iSortingCols ; $i++ ) {
                if ( $input[ 'bSortable_'.intval($input['iSortCol_'.$i]) ] == 'true' ) {
                    $aOrderingRules[] =
                        $aColumns[ intval( $input['iSortCol_'.$i] ) ]
                        . " " .($input['sSortDir_'.$i]==='asc' ? 'ASC' : 'DESC');
                }
            }
        }

        if (!empty($aOrderingRules)) {
            $sOrder = " ORDER BY ".implode(", ", $aOrderingRules);
        } else {
            $sOrder = "";
        }


        /**
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $iColumnCount = count($aColumns);
        $aFilteringRules = array();
        if ( isset($input['sSearch']) && $input['sSearch'] != "" ) {
            $aFilteringRules = array();
            for ( $i=0 ; $i<$iColumnCount ; $i++ ) {
                if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' ) {
                    $aFilteringRules[] = $aColumns[$i]." LIKE '%". $input['sSearch'] ."%'";

                }
            }


            if (!empty($aFilteringRules)) {
                $aFilteringRules = array('(' . implode(" OR ", $aFilteringRules) . ')');
            }
        }else{
            // custom filter
            if(isset($_GET['isAdmin'])){
                $isAdmin = $_GET['isAdmin'];
            }

        }

// Individual column filtering
        for ( $i=0 ; $i<$iColumnCount ; $i++ ) {
            if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' && $input['sSearch_'.$i] != '' ) {
                $aFilteringRules[] = $aColumns[$i]." LIKE '%" . $input['sSearch_'.$i] ."%'";
            }
        }

	 if (!empty($aFilteringRules)) {
            $sWhere = " WHERE ".implode(" AND ", $aFilteringRules). " AND `user_level` = 'regular' ";
        } else {
            $sWhere = " WHERE `user_level` = 'regular'";
        }

        /**
         * SQL queries
         * Get data to display
         */
        $aQueryColumns = implode(', ', $aColumns);
        $pdo = $this->getPdo();
        $rResult = array();
        if($input['iDisplayLength'] == '-1'){

            $sql = "SELECT $aQueryColumns FROM ".$sTable.$sWhere.$sOrder;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rResult[] = $row;
            }
        }else{
            $sql = "SELECT $aQueryColumns FROM ".$sTable.$sWhere.$sOrder . " LIMIT ".$firstResult.",".$maxResults;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rResult[] = $row;
            }
        }


        $iFilteredTotal = count($rResult);

        $sql = "SELECT count(id) as totalCount FROM ".$sTable.$sWhere.$sOrder;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        $iTotal = $total['totalCount'];



        /**
         * Output
         */


        $output = array(
            "sEcho"                => intval($input['sEcho']),
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => array(),
        );


        $i = 0;


        foreach($rResult as $column){

            $row = array();
            $row[] = $column['id'];
            $row[] = '#'.$column['public_key'];
            $row[] = 'T '.$column['points'];
            $output['aaData'][] = $row;

            $i++;
        }
        return json_encode( $output );
    }


    public function tablePointsBoardAdmin(){


        $aColumns = array( '`id`', '`public_key`', '`private_key_1`', '`private_key_2`', '`private_key_3`',  'points');



        // Indexed column (used for fast and accurate table cardinality)
        $sIndexColumn = 'id';

        // DB table to use
        $sTable = 'users';
        // Input method (use $_GET, $_POST or $_REQUEST)
        $input = $_GET;

        /**
         * Paging
         */
        $firstResult = "";
        $maxResults = "";
        if ( isset( $input['iDisplayStart'] ) && $input['iDisplayLength'] != '-1' ) {
            $firstResult = intval( $input['iDisplayStart'] );
            $maxResults = intval( $input['iDisplayLength'] );
        }else{

        }


        /**
         * Ordering
         */
        $aOrderingRules = array();
        if ( isset( $input['iSortCol_0'] ) ) {
            $iSortingCols = intval( $input['iSortingCols'] );
            for ( $i=0 ; $i<$iSortingCols ; $i++ ) {
                if ( $input[ 'bSortable_'.intval($input['iSortCol_'.$i]) ] == 'true' ) {
                    $aOrderingRules[] =
                        $aColumns[ intval( $input['iSortCol_'.$i] ) ]
                        . " " .($input['sSortDir_'.$i]==='asc' ? 'ASC' : 'DESC');
                }
            }
        }

        if (!empty($aOrderingRules)) {
            $sOrder = " ORDER BY ".implode(", ", $aOrderingRules);
        } else {
            $sOrder = "";
        }


        /**
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $iColumnCount = count($aColumns);
        $aFilteringRules = array();
        if ( isset($input['sSearch']) && $input['sSearch'] != "" ) {
            $aFilteringRules = array();
            for ( $i=0 ; $i<$iColumnCount ; $i++ ) {
                if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' ) {
                    $aFilteringRules[] = $aColumns[$i]." LIKE '%". $input['sSearch'] ."%'";

                }
            }


            if (!empty($aFilteringRules)) {
                $aFilteringRules = array('(' . implode(" OR ", $aFilteringRules) . ')');
            }
        }else{
            // custom filter
            if(isset($_GET['isAdmin'])){
                $isAdmin = $_GET['isAdmin'];
            }

        }

// Individual column filtering
        for ( $i=0 ; $i<$iColumnCount ; $i++ ) {
            if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' && $input['sSearch_'.$i] != '' ) {
                $aFilteringRules[] = $aColumns[$i]." LIKE '%" . $input['sSearch_'.$i] ."%'";
            }
        }

	 if (!empty($aFilteringRules)) {
            $sWhere = " WHERE ".implode(" AND ", $aFilteringRules). " AND `user_level` = 'regular'";
        } else {
            $sWhere = " WHERE `user_level` = 'regular'";
        }

        /**
         * SQL queries
         * Get data to display
         */
        $aQueryColumns = implode(', ', $aColumns);
        $pdo = $this->getPdo();
        $rResult = array();
        if($input['iDisplayLength'] == '-1'){

            $sql = "SELECT $aQueryColumns FROM ".$sTable.$sWhere.$sOrder;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rResult[] = $row;
            }
        }else{
            $sql = "SELECT $aQueryColumns FROM ".$sTable.$sWhere.$sOrder . " LIMIT ".$firstResult.",".$maxResults;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rResult[] = $row;
            }
        }


        $iFilteredTotal = count($rResult);

        $sql = "SELECT count(id) as totalCount FROM ".$sTable.$sWhere.$sOrder;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        $iTotal = $total['totalCount'];



        /**
         * Output
         */


        $output = array(
            "sEcho"                => intval($input['sEcho']),
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => array(),
        );


        $i = 0;


        foreach($rResult as $column){

            $row = array();
            $row[] = $column['id'];
            $row[] = '#'.$column['public_key'];
            $row[] = ($column['private_key_1'] != '' ?  '#'.$column['private_key_1'] : '');
            $row[] = ($column['private_key_2'] != '' ?  '#'.$column['private_key_2'] : '');
            $row[] = ($column['private_key_3'] != '' ?  '#'.$column['private_key_3'] : '');
            $row[] = 'T '.$column['points'];
            $output['aaData'][] = $row;

            $i++;
        }
        return json_encode( $output );
    }


    public function tableGeneratedList(){

        $aColumns = array( '`public_key`', '`date_time`');

        // Indexed column (used for fast and accurate table cardinality)
        $sIndexColumn = '`id`';

        // DB table to use
        $sTable = 'generated_keys';
        // Input method (use $_GET, $_POST or $_REQUEST)
        $input = $_GET;

        /**
         * Paging
         */
        $firstResult = "";
        $maxResults = "";
        if ( isset( $input['iDisplayStart'] ) && $input['iDisplayLength'] != '-1' ) {
            $firstResult = intval( $input['iDisplayStart'] );
            $maxResults = intval( $input['iDisplayLength'] );
        }else{

        }


        /**
         * Ordering
         */
        $aOrderingRules = array();
        if ( isset( $input['iSortCol_0'] ) ) {
            $iSortingCols = intval( $input['iSortingCols'] );
            for ( $i=0 ; $i<$iSortingCols ; $i++ ) {
                if ( $input[ 'bSortable_'.intval($input['iSortCol_'.$i]) ] == 'true' ) {
                    $aOrderingRules[] =
                        $aColumns[ intval( $input['iSortCol_'.$i] ) ]
                        . " " .($input['sSortDir_'.$i]==='asc' ? 'ASC' : 'DESC');
                }
            }
        }

        if (!empty($aOrderingRules)) {
            $sOrder = " ORDER BY ".implode(", ", $aOrderingRules);
        } else {
            $sOrder = "";
        }


        /**
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $iColumnCount = count($aColumns);
        $aFilteringRules = array();
        if ( isset($input['sSearch']) && $input['sSearch'] != "" ) {
            $aFilteringRules = array();
            for ( $i=0 ; $i<$iColumnCount ; $i++ ) {
                if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' ) {
                    $aFilteringRules[] = $aColumns[$i]." LIKE '%". $input['sSearch'] ."%'";

                }
            }


            if (!empty($aFilteringRules)) {
                $aFilteringRules = array('(' . implode(" OR ", $aFilteringRules) . ')');
            }
        }else{
            // custom filter
            if(isset($_GET['isAdmin'])){
                $isAdmin = $_GET['isAdmin'];
            }

        }

// Individual column filtering
        for ( $i=0 ; $i<$iColumnCount ; $i++ ) {
            if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' && $input['sSearch_'.$i] != '' ) {
                $aFilteringRules[] = $aColumns[$i]." LIKE '%" . $input['sSearch_'.$i] ."%'";
            }
        }

        if (!empty($aFilteringRules)) {
            $sWhere = " WHERE ".implode(" AND ", $aFilteringRules);
        } else {
            $sWhere = " ";
        }


        /**
         * SQL queries
         * Get data to display
         */
        $aQueryColumns = implode(', ', $aColumns);
        $pdo = $this->getPdo();
        $rResult = array();
        if($input['iDisplayLength'] == '-1'){

            $sql = "SELECT $aQueryColumns FROM ".$sTable.$sWhere.$sOrder;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rResult[] = $row;
            }
        }else{
            $sql = "SELECT $aQueryColumns FROM ".$sTable.$sWhere.$sOrder . " LIMIT ".$firstResult.",".$maxResults;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rResult[] = $row;
            }
        }


        $iFilteredTotal = count($rResult);

        $sql = "SELECT count(id) as totalCount FROM ".$sTable.$sWhere.$sOrder;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        $iTotal = $total['totalCount'];



        /**
         * Output
         */


        $output = array(
            "sEcho"                => intval($input['sEcho']),
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => array(),
        );


        $i = 1;


        foreach($rResult as $column){

            $row = array();
            $row[] = $i.'. #'.$column['public_key'];
            $output['aaData'][] = $row;

            $output['generatedDate'] = date('m/d/Y', strtotime($column['date_time']));
            $output['generatedTime'] = date('H:i:s', strtotime($column['date_time']));
            $i++;
        }
        return json_encode( $output );
    }

    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }

    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN_MAIN, DB_USER_MAIN, DB_PWD_MAIN, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN_MAIN, DB_USER_MAIN, DB_PWD_MAIN);
            }
        }
        return $this->db_pdo;
    }
}
