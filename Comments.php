<?php

namespace Codememory\Components\Big;

use Codememory\Support\Arr;

/**
 * Class Comments
 * @package Codememory\Components\Big
 *
 * @author  Codememory
 */
class Comments
{

    public const BEFORE = 'before';
    public const AFTER = 'after';

    /**
     * @param string $comment
     * @param string $where
     *
     * @return string
     */
    public function php(string $comment, string &$where): string
    {

        return $where = sprintf("// %s\n%s", $comment, $where);

    }

    /**
     * @param array  $comments
     * @param string $where
     *
     * @return string
     */
    public function multilinePhp(array $comments, string &$where): string
    {

        $comments = Arr::map($comments, function (mixed $key, mixed $value): array {
            return [
                sprintf(' * %s', $value)
            ];
        });
        $fullComment = sprintf("/**\n%s\n */\n", implode(PHP_EOL, $comments));

        return $where = $fullComment . $where;

    }

    /**
     * @param string $comment
     * @param string $where
     *
     * @return string
     */
    public function html(string $comment, string &$where): string
    {

        return $where = sprintf("<!-- %s -->\n%s", $comment, $where);

    }

}