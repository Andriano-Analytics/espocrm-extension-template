<?php

namespace Espo\Modules\{@name}\Jobs;

use Espo\Core\Job\JobDataLess;
use Espo\Core\Utils\Log;
use Espo\ORM\EntityManager;

class SandboxScheduledDevelopment implements JobDataLess
{
    public function __construct(
        private EntityManager $entityManager,
        private Log $log,
    ) { }

    public function run(): void
    {
        $this->log->debug("Sandbox Scheduled");
    }
}