<?php

namespace SimplePasswordGenerator\Tests\Cases;

use SimplePasswordGenerator\Exception\NotImplementException;
use SimplePasswordGenerator\Tests\OverrideTestCase;
use InvalidArgumentException;

class PasswordGeneratorTest extends OverrideTestCase
{
    /**
     * 
     */
    public static $opensslExists = true;
    public static $randomIntExists = true;
    public static $mbStrSplitExists = true;

    protected function setUp(): void
    {
        self::$opensslExists = true;
        self::$randomIntExists = true;
        self::$mbStrSplitExists = \function_exists("mb_str_split");
    }

    public function testInitialize()
    {
        $obj = new MockPasswordGenerator();
        $this->assertTrue($obj->get_use_random_int());
    }

    public function testcheckFunctionThrowsNotImplementException()
    {
        self::$randomIntExists = false;
        self::$opensslExists = false;
        $this->expectException(NotImplementException::class);

        $obj = new MockPasswordGenerator(false);
        $obj->checkFunctionFacade();
    }

    public function testGetLength()
    {
        $obj = new MockPasswordGenerator();
        $expected = 12;
        $actual = $obj->getLength();

        $this->assertEquals($expected, $actual);

        $expected2 = $obj->getLength();
        $actual2 = $obj->getLengthFacade();

        $this->assertEquals($expected2, $actual2);
    }

    public function testSetLength()
    {
        $obj = new MockPasswordGenerator();
        for ($expected = 1; $expected <= 128; $expected++) {
            $obj->setLength($expected);
            $actual = $obj->getLength();
            $this->assertEquals($expected, $actual);
        }
    }

    public function testSetLengthThrowsInvalidArgumentExceptionLessThan1()
    {
        $threshold = 0;

        $this->expectException(InvalidArgumentException::class);
        $obj = new MockPasswordGenerator();
        $obj->setLength($threshold);
    }

    public function testSetLengthThrowsInvalidArgumentExceptionMoreThan128()
    {
        $threshold = 129;

        $this->expectException(InvalidArgumentException::class);
        $obj = new MockPasswordGenerator();
        $obj->setLength($threshold);
    }

    public function testUseNumeric()
    {
        $expected = false;

        $obj = new MockPasswordGenerator();
        $obj->useNumeric($expected);
        $actual = $obj->getUseNumeric();

        $this->assertEquals($expected, $actual);

        $expected2 = true;
        $obj->useNumeric($expected2);
        $actual2 = $obj->getUseNumeric();

        $this->assertEquals($expected2, $actual2);
    }

    public function testUseLowerAlphabet()
    {
        $expected = false;

        $obj = new MockPasswordGenerator();
        $obj->useLowerAlphabet($expected);
        $actual = $obj->getUseLowerAlphabet();

        $this->assertEquals($expected, $actual);

        $expected2 = true;
        $obj->useLowerAlphabet($expected2);
        $actual2 = $obj->getUseLowerAlphabet();

        $this->assertEquals($expected2, $actual2);
    }

    public function testUseUpperAlphabet()
    {
        $expected = false;

        $obj = new MockPasswordGenerator();
        $obj->useUpperAlphabet($expected);
        $actual = $obj->getUseUpperAlphabet();

        $this->assertEquals($expected, $actual);

        $expected2 = true;
        $obj->useUpperAlphabet($expected2);
        $actual2 = $obj->getUseUpperAlphabet();

        $this->assertEquals($expected2, $actual2);
    }

    public function testUseSymbols()
    {
        $expected = false;

        $obj = new MockPasswordGenerator();
        $obj->useSymbols($expected);
        $actual = $obj->getUseSymbols();

        $this->assertEquals($expected, $actual);

        $expected2 = true;
        $obj->useSymbols($expected2);
        $actual2 = $obj->getUseSymbols();

        $this->assertEquals($expected2, $actual2);
    }

    public function testUseTrimSimilarLooking()
    {
        $expected = false;

        $obj = new MockPasswordGenerator();
        $obj->useTrimSimilarLooking($expected);
        $actual = $obj->useTrimSimilarLookingFacade();

        $this->assertEquals($expected, $actual);

        $expected2 = true;
        $obj->useTrimSimilarLooking($expected2);
        $actual2 = $obj->useTrimSimilarLookingFacade();

        $this->assertEquals($expected2, $actual2);
    }

    public function testSetSymbolsWhenMbStrSplitUsed()
    {
        self::$mbStrSplitExists = true;

        if (!\function_exists("mb_str_split")) {
            $this->expectException(NotTestImplementException::class);
        }

        $expected = '!"#';

        $obj = new MockPasswordGenerator();
        $obj->setSymbols($expected);
        $actual = $obj->getSymbolsFacade();

        $this->assertEquals($expected, $actual);

        $expected2 = '!@#$%^&*()こんにちわ';
        $obj->setSymbols($expected2);
        $actual2 = $obj->getSymbolsFacade();

        $this->assertEquals($expected2, $actual2);
    }

    public function testSetSymbolsWhenStrSplitUsed()
    {
        self::$mbStrSplitExists = false;
        $expected = '!"#';

        $obj = new MockPasswordGenerator();
        $obj->setSymbols($expected);
        $actual = $obj->getSymbolsFacade();

        $this->assertEquals($expected, $actual);

        $expected2 = '!@#$%^&*()こんにちわ';
        $obj->setSymbols($expected2);
        $actual2 = $obj->getSymbolsFacade();

        $this->assertEquals($expected2, $actual2);
    }

    public function testSetSymbolsDuplicatedChacters()
    {
        $expected = '!"#';
        $obj = new MockPasswordGenerator();
        $obj->setSymbols($expected . $expected);
        $actual = $obj->getSymbolsFacade();

        $this->assertEquals($expected, $actual);

        $expected2 = '!@#$%^&*()こんにちわ';
        $obj->setSymbols($expected2 . $expected2);
        $actual2 = $obj->getSymbolsFacade();

        $this->assertEquals($expected2, $actual2);
    }

    public function testSetSymbolsIncludingAlphabet()
    {
        $expected = '!"#';

        $obj = new MockPasswordGenerator();
        $obj->setSymbols($expected . 'abcxyzABCXYZ01289');
        $actual = $obj->getSymbolsFacade();

        $this->assertEquals($expected, $actual);
    }


    private function prepareDepencency()
    {
        $this->setDependencies([
            "testUseNumeric", "testUseLowerAlphabet",
            "testUseUpperAlphabet", "testUseSymbols",
            "testSetSymbols", "testSetSymbolsDuplicatedChacters",
            "testSetSymbolsIncludingAlphabet", "testUseTrimSimilarLooking"
        ]);
    }

    public function testPrepareKeySpaceCaseEmpty()
    {
        $this->prepareDepencency();

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(false);
        $obj->useLowerAlphabet(false);
        $obj->useUpperAlphabet(false);
        $obj->useSymbols(false);

        $obj->setSymbols('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~');
        $obj->prepareFacade();

        $expected01 = '';
        $actual01 = $obj->getKeySpaceFacade();
        $this->assertEquals($expected01, $actual01);
    }

    public function testPrepareKeySpaceCaseOnlyNumeric()
    {
        $this->prepareDepencency();

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(true);
        $obj->useLowerAlphabet(false);
        $obj->useUpperAlphabet(false);
        $obj->useSymbols(false);

        $obj->setSymbols('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~');
        $obj->prepareFacade();

        $expected01 = '0123456789';
        $actual01 = $obj->getKeySpaceFacade();
        $this->assertEquals($expected01, $actual01);
    }

    public function testPrepareKeySpaceCaseOnlyLowerAlphabet()
    {
        $this->prepareDepencency();

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(false);
        $obj->useLowerAlphabet(true);
        $obj->useUpperAlphabet(false);
        $obj->useSymbols(false);

        $obj->setSymbols('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~');
        $obj->prepareFacade();

        $expected01 = 'abcdefghijklmnopqrstuvwxyz';
        $actual01 = $obj->getKeySpaceFacade();
        $this->assertEquals($expected01, $actual01);
    }

    public function testPrepareKeySpaceCaseOnlyUpperAlphabet()
    {
        $this->prepareDepencency();

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(false);
        $obj->useLowerAlphabet(false);
        $obj->useUpperAlphabet(true);
        $obj->useSymbols(false);

        $obj->setSymbols('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~');
        $obj->prepareFacade();

        $expected01 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $actual01 = $obj->getKeySpaceFacade();
        $this->assertEquals($expected01, $actual01);
    }


    public function testPrepareKeySpaceCaseOnlySymbols()
    {
        $this->prepareDepencency();

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(false);
        $obj->useLowerAlphabet(false);
        $obj->useUpperAlphabet(false);
        $obj->useSymbols(true);

        $obj->setSymbols('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~');
        $obj->prepareFacade();

        $expected01 = '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';
        $actual01 = $obj->getKeySpaceFacade();
        $this->assertEquals($expected01, $actual01);
    }

    public function testPrepareKeySpaceCaseMultiCase()
    {
        $this->prepareDepencency();

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(true);
        $obj->useLowerAlphabet(true);
        $obj->useUpperAlphabet(true);
        $obj->useSymbols(true);

        $obj->setSymbols('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~');
        $obj->prepareFacade();

        $expected01 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';
        $actual01 = $obj->getKeySpaceFacade();
        $this->assertEquals($expected01, $actual01);
    }


    public function testGenerateUseRandomInt()
    {
        self::$opensslExists = false;
        self::$randomIntExists = true;

        $obj = new MockPasswordGenerator();
        $password = $obj->generate();

        $expectLength = $obj->getLength();
        $actualLength = strlen($password);
        $this->assertEquals($expectLength, $actualLength);

        $password2 = $obj->generate();
        $this->assertNotEquals($password, $password2);
    }

    public function testGenerateUseOpenSSL()
    {
        self::$opensslExists = true;
        self::$randomIntExists = false;

        $obj = new MockPasswordGenerator();
        $password = $obj->generate();

        $expectLength = $obj->getLength();
        $actualLength = strlen($password);
        $this->assertEquals($expectLength, $actualLength);

        $password2 = $obj->generate();
        $this->assertNotEquals($password, $password2);
    }

    public function testTrimSimilarLookingCheckOnlyToNumeric()
    {
        self::$mbStrSplitExists = false;

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(true);
        $obj->useLowerAlphabet(false);
        $obj->useUpperAlphabet(false);
        $obj->useSymbols(false);
        $obj->useTrimSimilarLooking(true);

        $expect = trim('234578');
        $actual = $obj->getKeySpaceFacade();
        $this->assertEquals($expect, $actual);
    }

    public function testTrimSimilarLookingCheckOnlyToAlphabet()
    {
        self::$mbStrSplitExists = false;

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(false);
        $obj->useLowerAlphabet(true);
        $obj->useUpperAlphabet(true);
        $obj->useSymbols(false);
        $obj->useTrimSimilarLooking(true);

        $expect = trim('acdefhijkmnprstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ');
        $actual = $obj->getKeySpaceFacade();
        $this->assertEquals($expect, $actual);
    }

    public function testTrimSimilarLookingCheckOnlyToSymbol()
    {
        self::$mbStrSplitExists = false;

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(false);
        $obj->useLowerAlphabet(false);
        $obj->useUpperAlphabet(false);
        $obj->useSymbols(true);
        $obj->useTrimSimilarLooking(true);

        $expect = '!#$%&*+-/<=>?@\^_~';
        $actual = $obj->getKeySpaceFacade();
        $this->assertEquals($expect, $actual);
    }

    public function testTrimSimilarLookingAll()
    {
        self::$mbStrSplitExists = false;

        $obj = new MockPasswordGenerator(false);
        $obj->useNumeric(true);
        $obj->useLowerAlphabet(true);
        $obj->useUpperAlphabet(true);
        $obj->useSymbols(true);
        $obj->useTrimSimilarLooking(true);

        $expect = '234578';
        $expect .= 'acdefhijkmnprstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $expect .= '!#$%&*+-/<=>?@\^_~';
        $actual = $obj->getKeySpaceFacade();
        $this->assertEquals($expect, $actual);
    }
}
