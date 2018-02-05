<?php

class Dashboard
{
    public $debug = TRUE;
    protected $db_pdo;

    public function generateNewKeyListAction($data)
    {
        $pdo = $this->getPdo();
        $sql = 'DELETE FROM `generated_keys`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $dateTime = date('Y-m-d H:i:s');
        $count = $data['count'];

         $sql = 'SELECT * FROM `users` WHERE `user_level` != "admin"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
         $result = array();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }


        $indexes = array();
        $x = 0;
        if($count > count($result)){
            $count = count($result);
        }
        while($x < $count){

            $index = array_rand($result);
            if(!in_array($index, $indexes)){
                $indexes[] = $index;
                $sql = 'INSERT INTO `generated_keys` (`user_id`, `public_key`, `date_time`) VALUES ('.$result[$index]['id'].', "'.$result[$index]['public_key'].'", "'.$dateTime.'")';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $x++;
            }

        }

        return
            json_encode(
                true
        );

    }

    public function clearKeyListAction()
    {
        $pdo = $this->getPdo();
        $sql = 'DELETE FROM `generated_keys`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return json_encode(true);

    }

    public function getAllGeneratedList(){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `generated_keys`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    public function getTotalPublicIds(){
        $pdo = $this->getPdo();
        $sql = 'SELECT count(id) as totalCount FROM `users` WHERE `user_level` IS NULL';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $data = array(
           'totalCount' => $result['totalCount']
        );
        return json_encode($data);
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
