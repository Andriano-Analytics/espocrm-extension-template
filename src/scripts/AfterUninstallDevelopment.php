<?php

use Espo\Core\Container;
use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\ORM\EntityManager;
use Espo\Entities\ScheduledJob;

use Espo\Modules\{@name}\Classes\Constants;
use Espo\Modules\{@name}\Classes\ConstantsDevelopment;

class AfterUninstallDevelopment
{
    private EntityManager $entityManager;
    private Metadata $metadata;
    private Log $log;

    public function __construct(Container $container)
    {
        $this->entityManager = $container->get("entityManager");
        $this->metadata = $container->getByClass(Metadata::class);
        $this->log = $container->getByClass(Log::class);
    }

    public function run(): void
    {
        $this->removeJobs();
    }

    private function removeJobs(): void
    {
        $job = $this->entityManager->getRDBRepository(ScheduledJob::ENTITY_TYPE)
            ->where(array('job' => 'SandboxScheduled'))->findOne();

        if ($job)
          $this->entityManager->removeEntity($job);
    }
}