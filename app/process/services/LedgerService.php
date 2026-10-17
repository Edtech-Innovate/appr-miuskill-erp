<?php
require_once "BaseService.php";

class LedgerService extends BaseService
{
    public function list($uniId, $getDataLimit, $filters)
    {
        $filters = json_decode($filters, true) ?? [];

        $this->logger->info("Fetching ledger list", [
            "uni_id"  => $uniId,
            "filters" => $filters
        ]);

        /* =========================
           ✅ TOTAL COUNT (FIXED)
        ========================= */
        $countSql = "
            SELECT COUNT(*)
            FROM Student_Ledgers AS SL
            LEFT JOIN Students AS ST ON SL.Student_ID = ST.ID
            WHERE SL.University_ID = ?
        ";
        $countParams = [$uniId];

        if (!empty($filters['transaction_id'])) {
            $countSql .= " AND SL.Transaction_ID = ?";
            $countParams[] = $filters['transaction_id'];
        }

        if (!empty($filters['users_id'])) {
            $countSql .= " AND ST.Added_For = ?";
            $countParams[] = $filters['users_id'];
        }

        if (!empty($filters['type'])) {
            $countSql .= " AND SL.Type = ?";
            $countParams[] = $filters['type'];
        }

        if (!empty($filters['min_amount'])) {
            $countSql .= " AND SL.Settlement_Amount >= ?";
            $countParams[] = $filters['min_amount'];
        }

        if (!empty($filters['max_amount'])) {
            $countSql .= " AND SL.Settlement_Amount <= ?";
            $countParams[] = $filters['max_amount'];
        }

        // ✅ transaction date (single OR range)
        if (!empty($filters['transaction_start']) || !empty($filters['transaction_end'])) {

            $start = !empty($filters['transaction_start'])
                ? date('Y-m-d', strtotime($filters['transaction_start']))
                : null;

            $end = !empty($filters['transaction_end'])
                ? date('Y-m-d', strtotime($filters['transaction_end']))
                : null;

            if ($start && $end) {
                $countSql .= " AND DATE(SL.Date) BETWEEN ? AND ?";
                $countParams[] = $start;
                $countParams[] = $end;
            } elseif ($start) {
                $countSql .= " AND DATE(SL.Date) = ?";
                $countParams[] = $start;
            } elseif ($end) {
                $countSql .= " AND DATE(SL.Date) = ?";
                $countParams[] = $end;
            }
        }

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $totalCount = (int) $countStmt->fetchColumn();

        /* =========================
           🔹 MAIN DATA QUERY (UNCHANGED, ONLY BUG FIX)
        ========================= */
        $params = [$uniId];

        $sql = "
           SELECT 
    SL.*,

    CONCAT(
        ST.First_Name, ' ',
        IFNULL(ST.Middle_Name, ''), ' ',
        ST.Last_Name
    ) AS StudentName,

    ST.Unique_ID,
    ST.Email,
    ST.Contact,

    CONCAT(U.Name, ' (', U.Code, ')') AS CenterName,


    /* ✅ PAYMENT TYPE / FEE STRUCTURE NAME */
    CASE
        WHEN SL.Type = 1 THEN 'Course Fee'
        WHEN SL.Type = 2 THEN 'Offline Student Fee'
        WHEN SL.Type = 3 THEN 'Wallet Payment'
        ELSE 'Unknown'
    END AS PaymentType

FROM Student_Ledgers AS SL

LEFT JOIN Students AS ST 
    ON SL.Student_ID = ST.ID

LEFT JOIN Users AS U 
    ON ST.Added_For = U.ID

LEFT JOIN Fee_Structures AS FS
    ON FS.ID = JSON_UNQUOTE(
        JSON_EXTRACT(
            JSON_KEYS(SL.Fee),
            CONCAT(
                '$[',
                JSON_LENGTH(JSON_KEYS(SL.Fee)) - 1,
                ']'
            )
        )
    )

WHERE SL.University_ID = ?";

        if (!empty($filters['transaction_id'])) {
            $sql .= " AND SL.Transaction_ID = ?";
            $params[] = $filters['transaction_id'];
        }

        if (!empty($filters['users_id'])) {
            $sql .= " AND ST.Added_For = ?";
            $params[] = $filters['users_id'];
        }

        // ✅ FIXED: was $baseSql → now $sql
        if (!empty($filters['transaction_start']) || !empty($filters['transaction_end'])) {

            $start = !empty($filters['transaction_start'])
                ? date('Y-m-d', strtotime($filters['transaction_start']))
                : null;

            $end = !empty($filters['transaction_end'])
                ? date('Y-m-d', strtotime($filters['transaction_end']))
                : null;

            if ($start && $end) {
                $sql .= " AND DATE(SL.Date) BETWEEN ? AND ?";
                $params[] = $start;
                $params[] = $end;
            } elseif ($start) {
                $sql .= " AND DATE(SL.Date) = ?";
                $params[] = $start;
            } elseif ($end) {
                $sql .= " AND DATE(SL.Date) = ?";
                $params[] = $end;
            }
        }

        if (!empty($getDataLimit)) {
            $sql .= " ORDER BY SL.ID ASC LIMIT {$getDataLimit}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($data as &$row) {
    if ((int)($row['Type'] ?? 0) === 1) {

        $feeArr = json_decode($row['Fee'] ?? '', true) ?? [];
        $row['Amount'] = array_sum(array_map('floatval', $feeArr));
        $row['Fee']=array_sum(array_map('floatval', $feeArr));
    }
}

        /* =========================
           ✅ FINAL RESPONSE
        ========================= */
        return [
            'data'        => $data,
            'total_count' => $totalCount
        ];
    }
}
