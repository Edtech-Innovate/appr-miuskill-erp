

<?php

require_once "BaseService.php";

class DashboardsService extends BaseService
{
    
    public function list($uniId, $filters = [])
    {
        // echo('xsxas');die;
 $dashboardData = [
            "overview" => [
                "total_students"   => 1250,
                "total_users"      => 85,
                "active_students"  => 1100,
                "inactive_students"=> 150,
                "wallet_balance"   => 452000,
            ],

            "today_stats" => [
                "new_students" => 18,
                "new_users"    => 3,
                "payments"     => 12,
            ],

            "charts" => [
                "students_by_course" => [
                    ["course" => "B.Voc IT", "count" => 420],
                    ["course" => "B.Voc Retail", "count" => 310],
                    ["course" => "B.Voc Agriculture", "count" => 270],
                    ["course" => "B.Voc Healthcare", "count" => 250],
                ],

                "monthly_admissions" => [
                    "Jan" => 45,
                    "Feb" => 60,
                    "Mar" => 75,
                    "Apr" => 90,
                    "May" => 110,
                ],
            ],

            "recent_activity" => [
                [
                    "type" => "student",
                    "message" => "New student registered",
                    "time" => "10 minutes ago"
                ],
                [
                    "type" => "payment",
                    "message" => "Fee payment received",
                    "time" => "30 minutes ago"
                ],
                [
                    "type" => "user",
                    "message" => "New center user created",
                    "time" => "1 hour ago"
                ],
            ],
        ];
        return $dashboardData;
        // $this->logger->info("Fetching student list", [
        //     "uni_id" => $uniId,
        //     "filters" => $filters
        // ]);

        // $sql = "SELECT * FROM Students WHERE University_ID = ?";
        // $params = [$uniId];

        // // OPTIONAL FILTER (example)
        // if (!empty($filters['course_id'])) {
        //     $sql .= " AND Course_ID = ?";
        //     $params[] = $filters['course_id'];
        // }

        // $stmt = $this->db->prepare($sql);
        // $stmt->execute($params);

        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

