<?php

require_once __DIR__ . '/services/StudentService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/WalletService.php';
require_once __DIR__ . '/services/LedgerService.php';
require_once __DIR__ . '/services/DashboardsService.php';
require_once __DIR__ . '/services/filterService.php';
class RequestHandler
{
    private $services = [];
    private $logger;
    private $getDataLimit;
    private $filters;
    public function __construct($db)
    {
       
        $this->logger = new Logger();

        // Register all services
        $this->services = [
            "students" => new StudentService($db, $this->logger),
            "users"    => new UserService($db, $this->logger),
            "wallet"   => new WalletService($db, $this->logger),
            "ledger"   => new LedgerService($db, $this->logger),
            "dashboard" => new DashboardsService($db,$this->logger),
            "filter" => new filterService($db,$this->logger),
        ];
    }

    /**
     * Handle API request
     */
    public function handle($method, $uni_id = null,$getDataLimit=null,$filters=null)
    {
    //  echo('<pre>');print_r($getDataLimit);die;
    $this->logger->info("REQUEST RECEIVED", [
        "method" => $method,
        "uni_id" => $uni_id,
        "getDataLimit"=>$getDataLimit,
        "filters"=>$filters
        
    ]);

    if (!isset($this->services[$method])) {
        $this->logger->error("INVALID_METHOD", ["method" => $method]);
        throw new Exception("Invalid API method: $method");
    }

    $service = $this->services[$method];

    // ✅ ONLY pass uniId
    return $service->list($uni_id,$getDataLimit,$filters);
}

}
