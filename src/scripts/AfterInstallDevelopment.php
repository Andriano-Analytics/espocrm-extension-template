<?php

use Espo\Core\Container;
use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\ORM\EntityManager;
use Espo\Entities\ScheduledJob;

use Espo\Modules\{@name}\Classes\Constants;
use Espo\Modules\{@name}\Classes\ConstantsDevelopment;

class AfterInstallDevelopment
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
        $this->createJobs();
    }

    private function createJobs(): void
    {
        $job = $this->entityManager->getRDBRepository(ScheduledJob::ENTITY_TYPE)
            ->where(array('job' => 'SandboxScheduled'))->findOne();

        if (!$job)
            $job = $this->entityManager->newEntity(ScheduledJob::ENTITY_TYPE);

        $job->set(array(
            'name' => "[{@nameLabel}]: Sandbox (Scheduled)",
            'job' => 'SandboxScheduled',
            'status' => "Inactive",
            'scheduling' => '*/5 * * * *',
        ));

        $this->entityManager->saveEntity($job);
    }
}