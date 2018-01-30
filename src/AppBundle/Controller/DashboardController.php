<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 1/30/2018
 * Time: 1:44 AM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;


use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Users;
use AppBundle\Entity\UserKeys;
use AppBundle\Entity\GeneratedKeys;
class DashboardController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $isLoggedIn = $this->get('session')->get('isLoggedIn');
        if($isLoggedIn){
            $userData = $this->get('session')->get('userData');
            $data = array(
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
                'userId' => $userData['id'],
                'hasShared' => $userData['hasShared'],
                'points' => $userData['points'],
                'publicKey' => $userData['keys']['public'],
                'privateKey' => ($userData['hasShared'] == true ? $userData['keys']['private'][array_rand($userData['keys']['private'])] : '????????????'),
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR);
            if($userData['userLevel'] == 'admin'){
                $loc = 'admin-dashboard.html.twig';
                $data['publicIdCount'] = $this->getPublicIdCount();
            }else{
                $loc = 'index.html.twig';

            }
            return $this->render('main/'.$loc, $data
            );
        }else{
            return $this->redirect('/login');
        }
    }


    /**
     * @Route("/points-board")
     */
    public function pointsBoardPage()
    {
        $isLoggedIn = $this->get('session')->get('isLoggedIn');
        if($isLoggedIn){
            $userData = $this->get('session')->get('userData');
            $data = array(
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
                'userId' => $userData['id'],
                'hasShared' => $userData['hasShared'],
                'publicKey' => $userData['keys']['public'],
                'privateKey' => ($userData['hasShared'] == true ? $userData['keys']['private'][array_rand($userData['keys']['private'])] : '????????????'));
        }else{
            $data = array(
                'userId' => 0
            );
        }
        $data['publicIdCount'] = $this->getPublicIdCount();

        return $this->render('main/points-board.html.twig', $data);
    }


    /**
     * @Route("/admin/generate-id-list")
     */
    public function adminGenerateIdListPage()
    {
        $isLoggedIn = $this->get('session')->get('isLoggedIn');
        if($isLoggedIn){
            $userData = $this->get('session')->get('userData');
            if($userData['userLevel'] == 'admin'){
                $data = array(
                    'firstName' => $userData['firstName'],
                    'lastName' => $userData['lastName'],
                    'userId' => $userData['id'],
                    'hasShared' => $userData['hasShared'],
                    'points' => $userData['points'],
                    'publicKey' => $userData['keys']['public'],
                    'privateKey' => ($userData['hasShared'] == true ? $userData['keys']['private'][array_rand($userData['keys']['private'])] : '????????????'),
                    'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR);
                $data['publicIdCount'] = $this->getPublicIdCount();
                return $this->render('/main/generate-id-list.html.twig', $data
                );
            }else{
                return $this->redirect('/error');
            }

        }else{
            return $this->redirect('/login');
        }
    }


    /**
     * @Route("/admin/generate-new-key-list")
     */
    public function generateNewKeyListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['param'], true);
        $dateTime = date('Y-m-d H:i:s');
        $count = $data['count'];

        $sql = $em->createQuery(
            "DELETE
            FROM AppBundle:GeneratedKeys g
            "
        );
        $sql->execute();


        $sql = $em->createQuery(
            "SELECT u.publicKey
            FROM AppBundle:Users u
            "
        );

        $result = $sql->getResult();

        $indexes = array();
        $x = 0;
        if($count > count($result)){
            $count = count($result);
        }
        while($x < $count){

                $index = array_rand($result);
                 if(!in_array($index, $indexes)){
                    $indexes[] = $index;
                    $entity = new GeneratedKeys();
                    $entity->setPublicKey($result[$index]['publicKey']);
                    $entity->setDateTime(new \DateTime($dateTime));
                    $em->persist($entity);
                     $x++;
                }

        }


        $em->flush();

        return new Response(
            json_encode(
                true
            )
        );

    }

    /**
     * @Route("/admin/generate-clear-list")
     */
    public function clearKeyListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sql = $em->createQuery(
            "DELETE
            FROM AppBundle:GeneratedKeys g
            "
        );
        $sql->execute();

        return new Response(
            json_encode(true)
        );
    }

    /**
     * @Route("/admin/download-list")
     */

    public function downloadKeyListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:GeneratedKeys')->findAll();
        $textFile = '../web/tmp_files/public_keys.txt';
        $fh = fopen($textFile, 'w');
        if($entity){
            $i = 1;
            for($x = 0; $x < count($entity); $x++) {

                fwrite($fh, $i.'. #'.$entity[$x]->getPublicKey() . "\r\n");
                $i++;
            }
        }

        fclose($fh);



        $response = new BinaryFileResponse($textFile);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;

    }


    /**
     * @Route("/main/table/points-board")
     */

    public function tablePointsBoard(){
        $em = $this->getDoctrine()->getManager();
        $aColumns = array( 'p.id', 'p.publicKey',
            'p.points'
        );

        // Indexed column (used for fast and accurate table cardinality)
        $sIndexColumn = 'p.id';

        // DB table to use
        $sTable = 'AppBundle:Users';
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

        if($input['iDisplayLength'] == '-1'){

            $sQuery = $em->createQuery("
            SELECT $aQueryColumns
            FROM ".$sTable." p ".$sWhere.$sOrder."")

            ;
            $rResult = $sQuery->getResult();


            $sQuery = $em->createQuery("
            SELECT p
            FROM ".$sTable." p ".$sWhere.$sOrder."")

            ;
        }else{
            $sQuery = $em->createQuery("
        SELECT $aQueryColumns
        FROM ".$sTable." p ".$sWhere.$sOrder."")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
            ;
            $rResult = $sQuery->getResult();


            $sQuery = $em->createQuery("
        SELECT p
        FROM ".$sTable." p ".$sWhere.$sOrder."")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
            ;

        }


        $paginator = new Paginator($sQuery);
        $iFilteredTotal = count($paginator);

        $sQuery = $em->createQuery("
        SELECT p
        FROM ".$sTable." p ".$sWhere.$sOrder."");

        $iTotal = count($paginator = new Paginator($sQuery));



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
            $row[] = '#'.$column['publicKey'];
            $row[] = 'T '.$column['points'];
            $output['aaData'][] = $row;

            $i++;
        }
        return new Response( json_encode( $output ) );
    }

    /**
     * @Route("/main/table/generated-list")
     */

    public function tableGeneratedList(){
        $em = $this->getDoctrine()->getManager();
        $aColumns = array( 'p.publicKey', 'p.dateTime'
        );

        // Indexed column (used for fast and accurate table cardinality)
        $sIndexColumn = 'p.id';

        // DB table to use
        $sTable = 'AppBundle:GeneratedKeys';
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

        if($input['iDisplayLength'] == '-1'){

            $sQuery = $em->createQuery("
            SELECT $aQueryColumns
            FROM ".$sTable." p ".$sWhere.$sOrder."")

            ;
            $rResult = $sQuery->getResult();


            $sQuery = $em->createQuery("
            SELECT p
            FROM ".$sTable." p ".$sWhere.$sOrder."")

            ;
        }else{
            $sQuery = $em->createQuery("
        SELECT $aQueryColumns
        FROM ".$sTable." p ".$sWhere.$sOrder."")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
            ;
            $rResult = $sQuery->getResult();


            $sQuery = $em->createQuery("
        SELECT p
        FROM ".$sTable." p ".$sWhere.$sOrder."")
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults)
            ;

        }


        $paginator = new Paginator($sQuery);
        $iFilteredTotal = count($paginator);

        $sQuery = $em->createQuery("
        SELECT p
        FROM ".$sTable." p ".$sWhere.$sOrder."");

        $iTotal = count($paginator = new Paginator($sQuery));



        /**
         * Output
         */


        $output = array(
            "sEcho"                => intval($input['sEcho']),
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => array(),
            "generatedTime"          => '',
            "generatedDate"          => ''
        );


        $i = 1;


        foreach($rResult as $column){

            $row = array();
            $row[] = $i.'. #'.$column['publicKey'];
            $output['aaData'][] = $row;

            $output['generatedDate'] = $column['dateTime']->format('m/d/Y');
            $output['generatedTime'] = $column['dateTime']->format('H:i:s');
            $i++;
        }
        return new Response( json_encode( $output ) );
    }


    public function getPublicIdCount(){
        $em = $this->getDoctrine()->getManager();
        $sql = $em->createQuery(
            "SELECT count(u.id) as totalCount
            FROM AppBundle:Users u
            "
        );

        $result = $sql->getResult();
        return $result[0]['totalCount'];
    }

}