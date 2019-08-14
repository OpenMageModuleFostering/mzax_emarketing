<?php
/*
 * NOTICE:
 * This code has been slightly altered by the Mzax_Emarketing module to use old php namespaces.
 */
/**
 * ua-parser
 *
 * Copyright (c) 2011-2012 Dave Olsen, http://dmolsen.com
 *
 * Released under the MIT license
 */
#namespace UAParser\Util;

#use UAParser\Exception\FetcherException;

class UAParser_Util_Fetcher
{
    private $resourceUri = 'https://raw.githubusercontent.com/ua-parser/uap-core/master/_regexes.yaml';

    /** @var resource */
    private $streamContext;

    public function __construct($streamContext = null)
    {
        if (is_resource($streamContext) && get_resource_type($streamContext) === 'stream-context') {
            $this->streamContext = $streamContext;
        } else {
            $this->streamContext = stream_context_create(
                array(
                    'ssl' => array(
                        'verify_peer'            => true,
                        'verify_depth'           => 10,
                        'cafile'                 => __DIR__ . '/../../resources/ca-bundle.crt',
                        static::getPeerNameKey() => 'www.github.com',
                        'disable_compression'    => true,
                    )
                )
            );
        }
    }

    public function fetch()
    {
        $level = error_reporting(0);
        $result = file_get_contents($this->resourceUri, null, $this->streamContext);
        error_reporting($level);

        if ($result === false) {
            $error = error_get_last();
            throw UAParser_Exception_FetcherException::httpError($this->resourceUri, $error['message']);
        }

        return $result;
    }

    public static function getPeerNameKey()
    {
        return version_compare(PHP_VERSION, '5.6') === 1 ? 'peer_name' : 'CN_match';
    }
}
