<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 08-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Pramadillo\PayForPost\Symfony\Component\Translation\Dumper;

use Pramadillo\PayForPost\Symfony\Component\Translation\MessageCatalogue;

/**
 * CsvFileDumper generates a csv formatted string representation of a message catalogue.
 *
 * @author Stealth35
 */
class CsvFileDumper extends FileDumper
{
    private $delimiter = ';';
    private $enclosure = '"';

    /**
     * {@inheritdoc}
     */
    public function formatCatalogue(MessageCatalogue $messages, $domain, array $options = [])
    {
        $handle = fopen('php://memory', 'r+');

        foreach ($messages->all($domain) as $source => $target) {
            fputcsv($handle, [$source, $target], $this->delimiter, $this->enclosure);
        }

        rewind($handle);
        $output = stream_get_contents($handle);
        fclose($handle);

        return $output;
    }

    /**
     * Sets the delimiter and escape character for CSV.
     *
     * @param string $delimiter Delimiter character
     * @param string $enclosure Enclosure character
     */
    public function setCsvControl($delimiter = ';', $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'csv';
    }
}
