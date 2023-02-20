<?php

declare(strict_types=1);

namespace Codelicia\Extension\Suggestion;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\MapResolver\Resolver;

final class CompletionNameSuggestionExtension implements Extension
{
    public function load(ContainerBuilder $container): void
    {
        $container->register(
            'name_suggestions.completion',
            static function (Container $container): CompletionNameSuggestion {
                return new CompletionNameSuggestion();
            },
            [CompletionExtension::TAG_COMPLETOR => []],
        );
    }

    public function configure(Resolver $schema): void
    {
    }
}
