<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoPostIncPostDecRule\NoPostIncPostDecRuleTest
 */
final class NoPostIncPostDecRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Post operation are forbidden, as they make 2 values at the same line. Use pre instead';

    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [PostInc::class, PostDec::class];
    }

    /**
     * @param PostDec|PostInc $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        return [self::ERROR_MESSAGE];
    }
}
