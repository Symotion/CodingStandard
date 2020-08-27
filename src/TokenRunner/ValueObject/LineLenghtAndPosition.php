<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject;

final class LineLenghtAndPosition
{
    /**
     * @var int
     */
    private $lineLenght;

    /**
     * @var int
     */
    private $currentPosition;

    public function __construct(int $lineLenght, int $currentPosition)
    {
        $this->lineLenght = $lineLenght;
        $this->currentPosition = $currentPosition;
    }

    public function getLineLenght(): int
    {
        return $this->lineLenght;
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }
}
