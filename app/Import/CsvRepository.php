<?php

namespace App\Import;

use League\Csv\Reader;

class CsvRepository
{
    public function getAll($file, array $options = [])
    {
        $reader = $this->createReader($file, $options);
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        $headers = $this->uniquifyHeaders($headers);
        $records = $reader->getRecords($headers);

        return $records;
    }

    public function getFiltered($file, array $filters, array $options = [])
    {
        $all = $this->getAll($file, $options);
        return $this->filter($all, $filters);
    }

    protected function uniquifyHeaders(array $headers)
    {
        $used = [];
        $uniquified = [];

        foreach ($headers as $h => $header) {
            for ($i = 0; isset($used[$header]); $i++) {
                $header = sprintf('%s_%s', $headers[$h], $i);
            }

            $used[$header] = true;
            $uniquified[] = $header;
        }

        return $uniquified;
    }

    /**
     * @param string $file
     * @return Reader
     */
    protected function createReader($file, array $options = [])
    {
        $reader = Reader::createFromPath($file, 'r');

        if (isset($options['delimiter'])) {
            $reader->setDelimiter($options['delimiter']);
        }

        if (isset($options['enclosure'])) {
            $reader->setEnclosure($options['enclosure']);
        }

        if (isset($options['escape'])) {
            $reader->setEscape($options['escape']);
        }

        if (isset($options['newline'])) {
            $reader->setNewline($options['newline']);
        }

        if (isset($options['input_encoding'])) {
            if (!$reader->isActiveStreamFilter()) {
                throw new \LogicException('Stream filter is not active');
            }

            $conversionFilter = $this->getConversionFilter($options['input_encoding']);
            $reader->appendStreamFilter($conversionFilter);
        }

        return $reader;
    }

    protected function getConversionFilter($input_encoding)
    {
        return sprintf('convert.iconv.%s/UTF-8', $input_encoding);
    }

    protected function filter(\Iterator $records, array $filters)
    {
        return new \CallbackFilterIterator($records, function ($current, $key) use ($filters) {
            foreach ($filters as $filter) {
                if (!$filter($current, $key)) {
                    return false;
                }
            }

            return true;
        });
    }
}