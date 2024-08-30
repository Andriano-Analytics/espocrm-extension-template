<?php

use Espo\Core\Container;
use Espo\Core\Exceptions\Error;
use Espo\ORM\EntityManager;

use Espo\Modules\{@name}\Classes\Constants;
use Espo\Modules\{@name}\Classes\ConstantsDevelopment;

class BeforeInstallDevelopment
{
    private EntityManager $entityManager;

    public function __construct(Container $container)
    {
        $this->entityManager = $container->get("entityManager");
    }

    public function run(): void
    {
    }
}