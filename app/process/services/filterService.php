<?php

require_once "BaseService.php";

class FilterService extends BaseService
{
    public function list($uni_id)
    {
        // method is IMPLIED = filter
        $method = 'filter';
// echo('<pre>');print_r($uni_id);die;
        $this->logger->info("FILTER_SERVICE_CALLED", [
            "method" => $method,
            "uni_id" => $uni_id
        ]);

        return [
            "students" => [
                "courses" => $this->courses($uni_id),
                "users"   => $this->users($uni_id),
                "status"  => ["Active", "Inactive"]
            ],
            'users'=>[
                "usersRole"   => $this->usersRole($uni_id),
                "status"  => ["Active", "Inactive"]
                ],
            'wallet'=>[
                "walletFilter"=> $this->walletFilter($uni_id),
                ],
                'ledgers'=>[
                "ledgersUsers"=>  $this->users($uni_id),
                ]
        ];
    }

    /* ========================
       DATA PROVIDERS
    ======================== */

     private function courses($uni_id)
     {
        // print_r($uni_id);die;
        $stmt = $this->db->prepare(
            "SELECT ID, Name 
             FROM Sub_Courses 
             WHERE University_ID = ?"
        );
        $stmt->execute([$uni_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     private function users()
     {
        $stmt = $this->db->prepare("SELECT ID,   CONCAT(Name, ' - ', Code) AS Name,Role FROM Users ");
        $stmt->execute();
        // print_r($stmt->fetchAll(PDO::FETCH_ASSOC));die;
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     private function usersRole()
     {
    $stmt = $this->db->prepare("SELECT DISTINCT Role FROM Users");
    $stmt->execute();
    $rolesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $roles = [];
    $verticals = [];

    foreach ($rolesData as $row) {
        $roles[] = $row['Role'];
        
    }
    // $verticals =[
    // '1'=>'Edtech',
    // '2'=>'IITS',
    // '3'=>'Rudra'
    // ]; 
    
    return [
        'roles' => $roles,
        'verticals' => $verticals
    ];
}
     private function walletFilter()
     {
        $stmt = $this->db->prepare("SELECT DISTINCT Type FROM Wallets ");
        $stmt->execute();
        // print_r($stmt->fetchAll(PDO::FETCH_ASSOC));die;
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }


}
