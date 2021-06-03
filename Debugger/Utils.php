<?php

namespace Codememory\Components\Big\Debugger;

use Codememory\Components\Big\Utils as BigUtils;

/**
 * Class Utils
 * @package Codememory\Components\Big\Debugger
 *
 * @author  Codememory
 */
class Utils
{

    /**
     * @var BigUtils
     */
    private BigUtils $bigUtils;

    /**
     * Utils constructor.
     *
     * @param BigUtils $bigUtils
     */
    public function __construct(BigUtils $bigUtils)
    {

        $this->bigUtils = $bigUtils;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns half of the number of debug lines displayed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return float
     */
    public function getHalf(): float
    {

        return ceil($this->bigUtils->getNumberLinesCode() / 2);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the starting line position from where to start
     * displaying the debug code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $line
     *
     * @return int
     */
    public function startPositionFromCenter(int $line): int
    {

        $start = $line - $this->getHalf();

        return $start < 1 ? 1 : $start;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the end position where to finish displaying
     * the debug code output
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $line
     * @param int $maxLines
     *
     * @return int
     */
    public function endPositionFromCenter(int $line, int $maxLines): int
    {

        $end = $line + $this->getHalf();

        return $end > $maxLines ? $maxLines : $end;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of open pattern strings from line start to end line
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int   $start
     * @param int   $end
     * @param array $arrayLinesTemplate
     *
     * @return array
     */
    public function getLinesTemplate(int $start, int $end, array $arrayLinesTemplate): array
    {

        $lines = [];

        for ($i = $start; $i <= $end; $i++) {
            $lines[$i] = $arrayLinesTemplate[$i];
        }

        return $lines;

    }

}