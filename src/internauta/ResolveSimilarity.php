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
     */
    public function resolve($comparators, $recipient)
    {
        $recipient = current(explode('@', $recipient));

        foreach ($comparators as $comparator) {
            $s = similar_text($recipient, $comparator);

            if ($s > 5) {
                break;
            }

            return false;
        }

        return true;
    }
}