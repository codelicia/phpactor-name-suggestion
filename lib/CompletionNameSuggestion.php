<?php

namespace Codelicia\Extension\Suggestion;

use Generator;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\TextDocument;
use Phpactor\WorseReflection\ReflectorBuilder;
use function lcfirst;
use function preg_match;
use function preg_replace;

class CompletionNameSuggestion implements Completor
{
    public function complete(TextDocument $source, ByteOffset $byteOffset) : Generator
    {
        $reflector = ReflectorBuilder::create()
            ->addSource((string)$source)
            ->build();

        // TODO: should it complete things for other methods signature as well?
        $reflection = $reflector->reflectOffset($source, $byteOffset);

        $scope      = explode('#', $reflection->frame()->name());
        $upperScope = $scope[0];

        if ('MethodDeclaration' !== $upperScope) {
            return;
        }

        $reflection = $reflector->reflectOffset($source, ByteOffset::fromInt($byteOffset->toInt() - 3));
        $type       = $reflection->symbolContext()->types()->getIterator()->current();

        if ($reflector->reflectClassesIn((string)$source)->has((string)$type)) {
            return;
        }

        if ($type !== null && $type->isClass()) {
            $variableName = lcfirst($type->className()->short());
            $variableName = preg_replace(['/Interface$/', '/Abstract$/'], '', $variableName);

            if (preg_match('/Wrapper$/', $variableName)) {
                $variableWithoutWrapperInName = preg_replace('/Wrapper$/', '', $variableName);
                yield Suggestion::createWithOptions('$' . lcfirst($variableWithoutWrapperInName), [
                    'short_description' => 'Codelicia/Name Suggestion',
                    'type'              => Suggestion::TYPE_VARIABLE,
                ]);
            }

            if (preg_match('/Event$/', $variableName)) {
                yield Suggestion::createWithOptions('$event', [
                    'short_description' => 'Codelicia/Name Suggestion',
                    'type'              => Suggestion::TYPE_VARIABLE,
                ]);
            }

            if (preg_match('/Command$/', $variableName)) {
                yield Suggestion::createWithOptions('$command', [
                    'short_description' => 'Codelicia/Name Suggestion',
                    'type'              => Suggestion::TYPE_VARIABLE,
                ]);
            }

            yield Suggestion::createWithOptions('$' . lcfirst($variableName), [
                'short_description' => 'Codelicia/Name Suggestion',
                'type'              => Suggestion::TYPE_VARIABLE,
            ]);
        }
    }
}
