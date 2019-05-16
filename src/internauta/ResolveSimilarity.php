<?php

namespace Muchacuba\Internauta;

/**
 * @di\service({
 *     private: true
 * })
 */
class ResolveSimilarity
{
    /**
     * @param string[] $comparators
     * @param string   $recipient
     *
     * @return int
     */
    public function resolve($comparators, $recipient)
    {
        $recipient = current(explode('@', $recipient));

        $higher = 0;
        foreach ($comparators as $comparator) {
            $s = similar_text($recipient, $comparator);

            if ($s > $higher) {
                $higher = $s;
            }
        }

        return $higher;
    }
}