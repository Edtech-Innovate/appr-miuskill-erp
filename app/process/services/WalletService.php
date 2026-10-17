<?php

require_once "BaseService.php";

class WalletService extends BaseService
{
    public function list($uniId, $getDataLimit, $filters)
    {
        // Decode filters if JSON string
        if (is_string($filters)) {
            $filters = json_decode($filters);
        }
//   echo('<pre>');print_r($filters);die;
        $this->logger->info("Fetching wallet list", [
            "uni_id"  => $uniId,
            "filters" => $filters
        ]);

        /* =========================
           BASE SQL / WHERE CLAUSE
        ========================= */
        $baseSql = " FROM Wallets
            LEFT JOIN Users ON Wallets.Added_By = Users.ID
            LEFT JOIN Users AS ApprovedUser ON Wallets.Approved_By = ApprovedUser.ID
            LEFT JOIN Universities ON Wallets.University_ID = Universities.ID
            WHERE Wallets.University_ID = ?";

        $params = [$uniId];

        /* =========================
           OPTIONAL FILTERS
        ========================= */

        // Transaction ID
        if (!empty($filters->transaction_id)) {
            $baseSql .= " AND Wallets.Transaction_ID = ?";
            $params[] = $filters->transaction_id;
        }
         if (!empty($filters->transaction_type)) {
            $baseSql .= " AND Wallets.Type = ?";
            $params[] = $filters->transaction_type;
        }

        // Transaction Date (single / range)
        if (!empty($filters->transaction_start) || !empty($filters->transaction_end)) {
            $start = !empty($filters->transaction_start)
                ? date('Y-m-d', strtotime($filters->transaction_start))
                : null;
            $end = !empty($filters->transaction_end)
                ? date('Y-m-d', strtotime($filters->transaction_end))
                : null;

            if ($start && $end) {
                $baseSql .= " AND DATE(Wallets.Transaction_Date) BETWEEN ? AND ?";
                $params[] = $start;
                $params[] = $end;
            } elseif ($start) {
                $baseSql .= " AND DATE(Wallets.Transaction_Date) = ?";
                $params[] = $start;
            } elseif ($end) {
                $baseSql .= " AND DATE(Wallets.Transaction_Date) = ?";
                $params[] = $end;
            }
        }

        // User filter
        if (!empty($filters->user_id)) {
            $baseSql .= " AND Wallets.User_ID = ?";
            $params[] = $filters->user_id;
        }

        // Minimum Balance
        if (!empty($filters->min_balance)) {
            $baseSql .= " AND Wallets.Balance >= ?";
            $params[] = $filters->min_balance;
        }

        // Maximum Balance
        if (!empty($filters->max_balance)) {
            $baseSql .= " AND Wallets.Balance <= ?";
            $params[] = $filters->max_balance;
        }

        /* =========================
           TOTAL COUNT
        ========================= */
        $countSql = "SELECT COUNT(*) FROM (
            SELECT Wallets.ID " . $baseSql . "
        ) AS total_table";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = (int) $countStmt->fetchColumn();

        /* =========================
           MAIN DATA QUERY
        ========================= */
        $sql = "
            SELECT 
                Wallets.*,
                CONCAT(Users.Name,' (',Users.Role,')') AS Added_for_User,
                CONCAT(ApprovedUser.Name,' (',ApprovedUser.Role,')') AS Approved_By_User,
                Universities.Vertical AS University_Name
            " . $baseSql . "
            ORDER BY Wallets.ID DESC
        ";

        if (!empty($getDataLimit)) {
            $sql .= " LIMIT {$getDataLimit}";
        }

        $stmt = $this->db->prepare($sql);
        
        $stmt->execute($params);
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
//  echo('<pre>');print_r($data);die;
        /* =========================
           FINAL RESPONSE
        ========================= */
        return [
            'data'        => $data,        // limited rows
            'total_count' => $totalCount   // total filtered rows
        ];
    }
}
