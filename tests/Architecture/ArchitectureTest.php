<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

final class ArchitectureTest
{
    /**
     * Controllers should not directly depend on Repositories.
     * They should use UseCases instead.
     */
    public function test_controllers_should_not_depend_on_repositories(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Http\Controllers'))
            ->shouldNotDependOn()
            ->classes(Selector::classname('/.*Repository.*/', true))
            ->because('Controllers should use UseCases, not Repositories directly');
    }

    /**
     * UseCases should not depend on Controllers.
     */
    public function test_usecases_should_not_depend_on_controllers(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::classname('/.*UseCase.*/', true))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Http\Controllers'))
            ->because('UseCases should not depend on Controllers');
    }

    /**
     * Entities should not depend on UseCases.
     */
    public function test_entities_should_not_depend_on_usecases(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\Entities/', true))
            ->shouldNotDependOn()
            ->classes(Selector::classname('/.*UseCase.*/', true))
            ->because('Entities should not depend on UseCases');
    }

    /**
     * Domain layer (Entities) should not depend on Infrastructure (Repositories implementations).
     */
    public function test_entities_should_not_depend_on_repository_implementations(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\Entities/', true))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\Repositories/', true))
            ->because('Entities should not depend on Repository implementations');
    }

    /**
     * Testing classes should not be used in production code.
     */
    public function test_production_code_should_not_depend_on_testing(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App'))
            ->excluding(Selector::inNamespace('/App\\\\.*\\\\Testing/', true))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('Tests'))
            ->because('Production code should not depend on test classes');
    }
}
