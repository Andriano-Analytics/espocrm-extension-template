<?php

use Espo\Core\Container;
use Espo\Core\Exceptions\Error;
use Espo\Entities\Extension;
use Espo\ORM\EntityManager;

use Espo\Modules\{@name}\Classes\Constants;

class BeforeInstall
{
    private EntityManager $entityManager;

    public function __construct(Container $container)
    {
        $this->entityManager = $container->get("entityManager");
    }

    public function run(): void
    {
        if (!$this->isInstallAllowed())
            throw new Error("The installation has been stopped due to missing requirements");
    }

    private function isInstallAllowed(): bool {
        $error = False;

        foreach(Constants::REQUIRED_EXTENSIONS as $extension) {
            $count = $this->em->getRDBRepository(Extension::ENTITY_TYPE)->where(["name" => $extension])->count();
            if($count != 1) {
                $GLOBALS["log"]->error("Missing extension", [$extension]);
                $error = True;
            }
        }

        return $error;
    }
}
