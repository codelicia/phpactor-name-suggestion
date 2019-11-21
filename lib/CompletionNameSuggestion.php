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

        $reflection = $reflector->reflectOffset($source, $byteOffset);
        if ('MethodDeclaration#__construct' !== $reflection->frame()->name()) {
            return;
        }

        $reflection = $reflector->reflectOffset($source, ByteOffset::fromInt($byteOffset->toInt() - 3));
        $type       = $reflection->symbolContext()->types()->getIterator()->current();

        if ($reflector->reflectClassesIn((string)$source)->has((string)$type)) {
            return;
        }

        // TODO: should it complete things for other methods signature as well?
        if ($type !== null && $type->isClass()) {
            $variableName = lcfirst($type->className()->short());


            yield Suggestion::createWithOptions('$' . lcfirst($variableName), [
                'short_description' => 'Codelicia/Name Suggestion',
                'type'              => Suggestion::TYPE_VARIABLE,
            ]);
        }
    }
}
