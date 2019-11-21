<?php

namespace Codelicia\Extension\Suggestion;

use Phpactor\Completion\Core\Completor;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\MapResolver\Resolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CompletionNameSuggestionExtension implements Extension
{
    public function __construct()
    {
    }

    public function load(ContainerBuilder $container): void
    {
        $container->register(
            'name_suggestions.completion',
            static function (Container $container): Completor {
                return new CompletionNameSuggestion();
            },
            [CompletionExtension::TAG_COMPLETOR => []]
        );
    }

    public function configure(Resolver $schema)
    {
    }
}
