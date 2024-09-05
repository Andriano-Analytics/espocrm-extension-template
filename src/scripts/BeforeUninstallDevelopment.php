<?php

use Espo\Core\Container;
use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\ORM\EntityManager;

use Espo\Modules\{@name}\Classes\Constants;
use Espo\Modules\{@name}\Classes\ConstantsDevelopment;

class BeforeUninstallDevelopment
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
    }
}
