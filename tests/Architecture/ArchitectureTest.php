<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPat\Test\PHPat;
use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;

final class ArchitectureTest
{
    // ==========================================
    // LAYER DEPENDENCY RULES
    // ==========================================

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

    /**
     * DTOs should not depend on Repositories.
     * Note: DTOs may wrap Entities for response objects.
     */
    public function test_dtos_should_not_depend_on_repositories(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\DataTransferObjects/', true))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\Repositories/', true))
            ->because('DTOs should not depend on Repositories');
    }

    /**
     * Events should not depend on UseCases or Controllers.
     */
    public function test_events_should_not_depend_on_usecases_or_controllers(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\Events/', true))
            ->shouldNotDependOn()
            ->classes(
                Selector::classname('/.*UseCase.*/', true),
                Selector::inNamespace('App\Http\Controllers')
            )
            ->because('Events should only carry data, not depend on application logic');
    }

    /**
     * Infrastructure layer should not depend on Http layer.
     */
    public function test_infrastructure_should_not_depend_on_http(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Infrastructure'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Http'))
            ->because('Infrastructure should be independent of HTTP layer');
    }

    // ==========================================
    // DOMAIN ISOLATION RULES
    // ==========================================

    /**
     * Post domain should not depend on Website domain internals.
     */
    public function test_post_domain_should_not_depend_on_website_internals(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Post'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Website\UseCases'),
                Selector::inNamespace('App\Website\Repositories'),
                Selector::inNamespace('App\Website\DataTransferObjects')
            )
            ->because('Post domain should not depend on Website domain internals');
    }

    /**
     * Website domain should not depend on Post domain internals.
     */
    public function test_website_domain_should_not_depend_on_post_internals(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Website'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Post\UseCases'),
                Selector::inNamespace('App\Post\Repositories'),
                Selector::inNamespace('App\Post\DataTransferObjects')
            )
            ->because('Website domain should not depend on Post domain internals');
    }

    /**
     * User domain should not depend on other domain internals.
     */
    public function test_user_domain_should_not_depend_on_other_domains(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\User'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Post'),
                Selector::inNamespace('App\Website')
            )
            ->because('User domain should be independent of other domains');
    }

    // ==========================================
    // NAMING CONVENTION RULES
    // ==========================================

    /**
     * Classes in UseCases namespace should end with UseCase.
     */
    public function test_usecase_classes_should_be_suffixed_with_usecase(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\UseCases/', true))
            ->shouldBeNamed('/.*UseCase$/', true)
            ->because('UseCase classes should follow naming convention');
    }

    /**
     * Repository interfaces should end with Interface.
     */
    public function test_repository_interfaces_should_be_suffixed_with_interface(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::classname('/.*RepositoryInterface$/', true))
            ->shouldBeNamed('/.*Interface$/', true)
            ->because('Repository interfaces should follow naming convention');
    }

    /**
     * Contracts should end with Contract.
     */
    public function test_contracts_should_be_suffixed_with_contract(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('/App\\\\.*\\\\Contracts/', true))
            ->shouldBeNamed('/.*Contract$/', true)
            ->because('Contracts should follow naming convention');
    }

    // ==========================================
    // FRAMEWORK ISOLATION RULES
    // ==========================================

    /**
     * Controllers should not directly use Eloquent models (use repositories).
     * Note: Route model binding is allowed (Website entity).
     */
    public function test_controllers_should_not_use_post_or_user_entities_directly(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Http\Controllers'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Post\Entities'),
                Selector::inNamespace('App\User\Entities')
            )
            ->because('Controllers should use UseCases, not Post/User Entities directly');
    }
}
