

<?php

require_once "BaseService.php";

class StudentService extends BaseService
{
    // public function list($uniId, $getDataLimit, array  $filters = [])
    // {
    //     $this->logger->info("Fetching student list", [
    //         "uni_id" => $uniId,
    //         "filters" => $filters
    //     ]);

    //     $sql = "SELECT 
    //         Students.*, 
    //         Courses.Name AS CourseName, 
    //         Sub_Courses.Name AS SubCourseName ,
    //         CONCAT(Users.Name, ' - ', Users.Code) AS user_name_code
    //     FROM Students 
    //     LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
    //     LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID
    //     LEFT JOIN Users ON Students.Added_For=Users.ID
    //     WHERE Students.University_ID = ? ";
    //   $params = [$uniId];

    //     // OPTIONAL FILTER (example)
    //     if (!empty($filters['course_id'])) {
    //         $sql .= " AND Course_ID = ?";
    //         $params[] = $filters['course_id'];
    //     }
    //      if (!empty($getDataLimit)) {
    //         $getDataLimit = $getDataLimit;
    //         $sql .= " ORDER BY Students.ID ASC LIMIT {$getDataLimit} ";
    //     }
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute($params);
        

    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    public function list($uniId, $getDataLimit, $filters)
{
    $filters=json_decode($filters)??"";
//   echo('<pre>');print_r($filters);die;
    $this->logger->info("Fetching student list", [
        "uni_id" => $uniId,
        "filters" => $filters
    ]);

    $baseSql = "
        FROM Students 
        LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
        LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID
        LEFT JOIN Users ON Students.Added_For = Users.ID
        WHERE Students.University_ID = ?
    ";

    $params = [$uniId];
    if (!empty($filters->student_id)) {
        $baseSql .= " AND Unique_ID = ?";
        $params[] = $filters->student_id;
    }
    if (!empty($filters->course)) {
        $baseSql .= " AND Sub_Course_ID = ?";
        $params[] = $filters->course;
    }
    if (!empty($filters->user)) {
        $baseSql .= " AND Added_For = ?";
        $params[] = $filters->user;
    }
    if (!empty($filters->processed_by_center_start) || !empty($filters->processed_by_center_end)) {

    // date format normalize (MM/DD/YYYY → YYYY-MM-DD)
    $start = !empty($filters->processed_by_center_start)
        ? date('Y-m-d', strtotime($filters->processed_by_center_start))
        : null;

    $end = !empty($filters->processed_by_center_end)
        ? date('Y-m-d', strtotime($filters->processed_by_center_end))
        : null;

    if ($start && $end) {
        // ✅ BETWEEN start & end
        $baseSql .= " AND DATE(Process_By_Center) BETWEEN ? AND ?";
        $params[] = $start;
        $params[] = $end;

    } elseif ($start) {
        // ✅ Only start date → same day data
        $baseSql .= " AND DATE(Process_By_Center) = ?";
        $params[] = $start;

    } elseif ($end) {
        // ✅ Only end date → same day data
        $baseSql .= " AND DATE(Process_By_Center) = ?";
        $params[] = $end;
    }
}
    if (!empty($filters->payment_received_start) || !empty($filters->payment_received_end)) {

    // date format normalize (MM/DD/YYYY → YYYY-MM-DD)
    $start = !empty($filters->payment_received_start)
        ? date('Y-m-d', strtotime($filters->payment_received_start))
        : null;

    $end = !empty($filters->payment_received_end)
        ? date('Y-m-d', strtotime($filters->payment_received_end))
        : null;

    if ($start && $end) {
        // ✅ BETWEEN start & end
        $baseSql .= " AND DATE(Payment_Received) BETWEEN ? AND ?";
        $params[] = $start;
        $params[] = $end;

    } elseif ($start) {
        // ✅ Only start date → same day data
        $baseSql .= " AND DATE(Payment_Received) = ?";
        $params[] = $start;

    } elseif ($end) {
        // ✅ Only end date → same day data
        $baseSql .= " AND DATE(Payment_Received) = ?";
        $params[] = $end;
    }
}
    if (!empty($filters->document_received_start) || !empty($filters->document_received_end)) {

    // date format normalize (MM/DD/YYYY → YYYY-MM-DD)
    $start = !empty($filters->document_received_start)
        ? date('Y-m-d', strtotime($filters->document_received_start))
        : null;

    $end = !empty($filters->document_received_end)
        ? date('Y-m-d', strtotime($filters->document_received_end))
        : null;

    if ($start && $end) {
        // ✅ BETWEEN start & end
        $baseSql .= " AND DATE(Document_Verified) BETWEEN ? AND ?";
        $params[] = $start;
        $params[] = $end;

    } elseif ($start) {
        // ✅ Only start date → same day data
        $baseSql .= " AND DATE(Document_Verified) = ?";
        $params[] = $start;

    } elseif ($end) {
        // ✅ Only end date → same day data
        $baseSql .= " AND DATE(Document_Verified) = ?";
        $params[] = $end;
    }
}

    // if (!empty($filters['course_id'])) {
    //     $baseSql .= " AND Course_ID = ?";
    //     $params[] = $filters['course_id'];
    // }
//  if (!empty($filters->student_id)) {
//         $baseSql .= " AND Unique_ID = ?";
//         $params[] = $filters->student_id;
//     }
    /* =========================
       ✅ TOTAL COUNT (FIXED)
    ========================= */
    $countSql = "SELECT COUNT(*) FROM (
        SELECT Students.ID
        " . $baseSql . "
    ) AS total_table";

    $countStmt = $this->db->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = (int) $countStmt->fetchColumn();

    /* =========================
       DATA QUERY (AS IS)
    ========================= */
    $sql = "
        SELECT 
            Students.*, 
            Courses.Name AS CourseName, 
            Sub_Courses.Name AS SubCourseName,
            CONCAT(Users.Name, ' - ', Users.Code) AS user_name_code
        " . $baseSql;

    if (!empty($getDataLimit)) {
        $sql .= " ORDER BY Students.ID ASC LIMIT {$getDataLimit} ";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    // print_r($stmt);die;
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo('<pre>');print_r($data);die;
    return [
        'data' => $data,          // limited data
        'total_count' => $totalCount // ✅ FULL TOTAL
    ];
}

}

