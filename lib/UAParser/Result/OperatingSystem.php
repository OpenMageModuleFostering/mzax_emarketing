<?php
/*
 * NOTICE:
 * This code has been slightly altered by the Mzax_Emarketing module to use old php namespaces.
 */
/**
 * ua-parser
 *
 * Copyright (c) 2011-2013 Dave Olsen, http://dmolsen.com
 * Copyright (c) 2013-2014 Lars Strojny, http://usrportage.de
 *
 * Released under the MIT license
 */
#namespace UAParser\Result;

class UAParser_Result_OperatingSystem extends UAParser_Result_AbstractVersionedSoftware
{
    /** @var string */
    public $major;

    /** @var string */
    public $minor;

    /** @var string */
    public $patch;

    /** @var string */
    public $patchMinor;

    public function toVersion()
    {
        return $this->formatVersion($this->major, $this->minor, $this->patch, $this->patchMinor);
    }
}
