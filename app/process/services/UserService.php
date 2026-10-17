<?php
require_once "BaseService.php";

class UserService extends BaseService
{
    public function list($uniId, $getDataLimit, $filters)
    {
        // Decode filters (string → array)
        $filters = json_decode($filters, true) ?? [];

        $this->logger->info("Fetching user list", [
            "getDataLimit" => $getDataLimit,
            "filters"      => $filters
        ]);

        /* =========================
           BASE SQL (COMMON)
        ========================= */
        $baseSql = "
            FROM Users
            LEFT JOIN Students 
                ON Users.ID = Students.Added_For
                AND Students.Step = 4
                AND Students.Process_By_Center IS NOT NULL
                AND Students.Payment_Received IS NOT NULL
            WHERE Users.Role IN ('Sub-Center', 'Center','Counsellor','Sub-Counsellor')
        ";

        $params = [];

        /* =========================
           FILTERS
        ========================= */

        // Vertical filter
        // if (!empty($filters['user_vertical'])) {
        //     $vertical = null;

        //     if ($filters['user_vertical'] === 'Edtech') {
        //         $vertical = 1;
        //     } else if ($filters['user_vertical'] === 'IITS') {
        //         $vertical = 2;
        //     }else if($filters['user_vertical'] === 'Rudra'){
        //         $vertical = 3;
        //     }

        //     if ($vertical !== null) {
        //         $baseSql .= " AND Users.Vertical= ?";
        //         $params[] = $vertical;
        //     }
        // }

        // User role filter
        if (!empty($filters['user_role'])) {
            $baseSql .= " AND Users.Role = ?";
            $params[] = $filters['user_role'];
        }

        // Created At (single / range)
        if (!empty($filters['processed_by_create_start']) || !empty($filters['processed_by_create_end'])) {

            $start = !empty($filters['processed_by_create_start'])
                ? date('Y-m-d', strtotime($filters['processed_by_create_start']))
                : null;

            $end = !empty($filters['processed_by_create_end'])
                ? date('Y-m-d', strtotime($filters['processed_by_create_end']))
                : null;

            if ($start && $end) {
                $baseSql .= " AND DATE(Users.Created_At) BETWEEN ? AND ?";
                $params[] = $start;
                $params[] = $end;

            } elseif ($start) {
                $baseSql .= " AND DATE(Users.Created_At) = ?";
                $params[] = $start;

            } elseif ($end) {
                $baseSql .= " AND DATE(Users.Created_At) = ?";
                $params[] = $end;
            }
        }

        /* =========================
           TOTAL COUNT (FIXED)
        ========================= */
        $countSql = "SELECT COUNT(DISTINCT Users.ID) " . $baseSql;

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = (int) $countStmt->fetchColumn();

        /* =========================
           DATA QUERY
        ========================= */
        $sql = "
            SELECT 
                Users.*,
                COUNT(Students.ID) AS Admissions
            " . $baseSql . "
            GROUP BY Users.ID
        ";

        if (!empty($getDataLimit)) {
            $sql .= " ORDER BY Users.ID ASC LIMIT {$getDataLimit}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         foreach ($data as &$row) {

//     switch ($row['vertical'] ?? null) {
//         case '1':
//             $row['verticalName'] = 'Edtech';
//             break;
//         case '2':
//             $row['verticalName'] = 'IITS';
//             break;
//         case '3':
//             $row['verticalName'] = 'Rudra';
//             break;
//         default:
//             $row['verticalName'] = '-';
//     }
// }
// unset($row);
        /* =========================
           FINAL RESPONSE
        ========================= */
        return [
            'data'        => $data,
            'total_count' => $totalCount
        ];
    }
}
