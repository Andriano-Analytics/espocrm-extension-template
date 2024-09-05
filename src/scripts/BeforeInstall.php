<?php

use Espo\Core\Container;
use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Extension;
use Espo\ORM\EntityManager;

use Espo\Modules\{@name}\Classes\Constants;

class BeforeInstall
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
        if (!$this->isInstallAllowed())
            throw new Error("The installation has been stopped due to missing requirements");
    }

    private function isInstallAllowed(): bool {
        $allowed = True;

        foreach(Constants::REQUIRED_EXTENSIONS as $extension) {
            $count = $this->entityManager->getRDBRepository(Extension::ENTITY_TYPE)->where(["name" => $extension])->count();
            if($count != 1) {
                $this->log->error("Missing extension: {$extension}");
                $allowed = False;
            }
        }

        return $allowed;
    }
}
