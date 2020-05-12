<?php

namespace SimplePasswordGenerator;

use SimplePasswordGenerator\Exception\NotImplementException;
use InvalidArgumentException;

/**
 * Secure password generator
 * @since 1.0.0
 * @license BSD 3-Clause Lisence
 */
class PasswordGenerator
{
    /**
     * Version
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * multibyte convert
     * @var string
     */
    const ENCODING = "UTF-8";

    /**
     * Length
     * @var int 
     */
    protected $length = 12;

    /**
     * Original character space
     * @var string
     */
    protected $keySpace = '';

    /**
     * Use symbols 
     * By default, symbols presets upon ASCII code 
     */
    protected $symbols = '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';

    /**
     * Is use numeric characters
     * @var bool
     */
    protected $useNumeric = true;

    /**
     * Is use lower alphabet
     * @var bool
     */
    protected $useLowerAlphabet = true;

    /**
     * Is use upper alphabet
     * @var bool
     */
    protected $useUpperAlphabet = true;

    /**
     * Is use symbols
     * @var bool
     */
    protected $useSymbols = true;

    /**
     * To get rid of the similar-looking characters (e.g  q (upper to 'Q') and 9 (numeric nine)) 
     * @var bool
     */
    protected $useTrimSimilarLooking = false;

    /**
     * 
     * @var bool
     */
    protected $use_random_int = false;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->checkFunction();
        $this->prepare();
    }

    /**
     * Generate password
     */
    public function generate()
    {
        $func = function ($useRamdomInt, $length) {
            return ($useRamdomInt)
                ? random_int(0, $length)
                : (hexdec(bin2hex(openssl_random_pseudo_bytes(4))) % $length);
        };

        $keySpaceLength = mb_strlen($this->keySpace, self::ENCODING);
        $keySpaceArray = $this->strSplit($this->keySpace);
        $password = '';

        for ($index = 0; $index < $this->length; $index++) {
            $i = ($func($this->use_random_int, $keySpaceLength) ?: 1) - 1;
            $password .= $keySpaceArray[$i];
        }

        return $password;
    }

    /**
     * Gets password length
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set Length
     * @return this
     */
    public function setLength(int $length = 12)
    {
        if ($length < 1 || $length > 128) {
            throw new InvalidArgumentException('$length must be the range between 1 and 128 characters.');
        }

        $this->length = $length;
        $this->prepare();
    }

    /**
     * Enable or disable numeric characters
     * @return this
     */
    public function useNumeric(bool $useNumeric)
    {
        $this->useNumeric = $useNumeric;
        $this->prepare();

        return $this;
    }

    /**
     * Enable or disable lower alphabet characters
     * @return this
     */
    public function useLowerAlphabet(bool $useLowerAlphabet)
    {
        $this->useLowerAlphabet = $useLowerAlphabet;
        $this->prepare();

        return $this;
    }

    /**
     * Enable or disable upper alphabet characters
     * @return this
     */
    public function useUpperAlphabet(bool $useUpperAlphabet)
    {
        $this->useUpperAlphabet = $useUpperAlphabet;
        $this->prepare();

        return $this;
    }

    /**
     * Enable or disable to trim similar-looking characters
     * @return this
     */
    public function useTrimSimilarLooking(bool $useTrimSimilarLooking)
    {
        $this->useTrimSimilarLooking = $useTrimSimilarLooking;
        $this->prepare();

        return $this;
    }

    /**
     * Enable or disable upper alphabet characters
     * @return this
     */
    public function useSymbols(bool $useSymbols)
    {
        $this->useSymbols = $useSymbols;
        $this->prepare();

        return $this;
    }

    /**
     * Sets Symbols 
     * @param string $symbols 
     */
    public function setSymbols(string $symbols = '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~')
    {
        $original = array_unique($this->strSplit($symbols));
        $symbolSpace = [];

        foreach ($original as $c) {
            $hexValue = hexdec(bin2hex($c));

            // 0 to 9
            if ($hexValue >= 0x30 && $hexValue <= 0x39) {
                continue;
            }

            // A to Z
            if ($hexValue >= 0x41 && $hexValue <= 0x5a) {
                continue;
            }

            // a to z
            if ($hexValue >= 0x61 && $hexValue <= 0x7a) {
                continue;
            }

            $symbolSpace[] = hex2bin(dechex($hexValue));
        }

        $this->symbols = implode($symbolSpace);
    }

    /**
     * Check function exists
     * 
     * @throws NotImplementException As it is not exists both 'random_int' and 'openssl_random_pseudo_bytes' function
     */
    protected function checkFunction()
    {
        if (function_exists("random_int")) {
            $this->use_random_int = true;
            return;
        }

        if (!function_exists("openssl_random_pseudo_bytes")) {
            throw new NotImplementException('openssl_random_pseudo_bytes is not exists');
        }
    }

    /**
     * Check function exists
     * 
     * @throws NotImplementException As it is not exists both 'random_int' and 'openssl_random_pseudo_bytes' function
     */
    protected function strSplit(string $str, $length = 1)
    {
        if (function_exists("mb_str_split")) {
            return mb_str_split($str, $length, self::ENCODING);
        }

        return \preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Prepare kye space
     */
    protected function prepare()
    {
        $this->prepareKeySpace();
        $this->trimSimilarLooking();
    }

    /**
     * Prepare
     */
    protected function prepareKeySpace()
    {
        $this->keySpace = '';

        if ($this->useNumeric) {
            $this->keySpace .= '0123456789';
        }

        if ($this->useLowerAlphabet) {
            $this->keySpace .= 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($this->useUpperAlphabet) {
            $this->keySpace .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($this->useSymbols) {
            $this->keySpace .= $this->symbols;
        }
    }

    /**
     * Trim similar looking
     * @return void
     */
    protected function trimSimilarLooking()
    {
        if (!$this->useTrimSimilarLooking) {
            return;
        }

        $similarLookingList = [
            '0', 'o', 'O', '1',
            'I', 'l', '6', 'b',
            '9', 'g', 'q'
        ];

        $excludedAmbigunousSymbols = $this->strSplit('"\'(),.:;[]{}`|');

        foreach ($similarLookingList as $row) {
            $this->keySpace = str_replace($row, '', $this->keySpace);
        }
        foreach ($excludedAmbigunousSymbols as $row) {
            $this->keySpace = str_replace($row, '', $this->keySpace);
        }
    }
}
