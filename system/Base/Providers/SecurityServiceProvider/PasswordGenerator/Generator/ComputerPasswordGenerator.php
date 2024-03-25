<?php

namespace System\Base\Providers\SecurityServiceProvider\PasswordGenerator\Generator;

use System\Base\Providers\SecurityServiceProvider\PasswordGenerator\Exception\CharactersNotFoundException;
use System\Base\Providers\SecurityServiceProvider\PasswordGenerator\Model\CharacterSet;
use System\Base\Providers\SecurityServiceProvider\PasswordGenerator\Model\Option\Option;

class ComputerPasswordGenerator extends AbstractPasswordGenerator
{
    const OPTION_UPPER_CASE = 'UPPERCASE';
    const OPTION_LOWER_CASE = 'LOWERCASE';
    const OPTION_NUMBERS = 'NUMBERS';
    const OPTION_SYMBOLS = 'SYMBOLS';
    const OPTION_AVOID_SIMILAR = 'AVOID_SIMILAR';
    const OPTION_LENGTH_MIN = 'LENGTH_MIN';
    const OPTION_LENGTH_MAX = 'LENGTH_MAX';

    const PARAMETER_UPPER_CASE = 'UPPERCASE';
    const PARAMETER_LOWER_CASE = 'LOWERCASE';
    const PARAMETER_NUMBERS = 'NUMBERS';
    const PARAMETER_SYMBOLS = 'SYMBOLS';
    const PARAMETER_SIMILAR = 'AVOID_SIMILAR';

    public function __construct()
    {
        $this
            ->setOption(self::OPTION_LENGTH_MIN, array('type' => Option::TYPE_INTEGER, 'default' => 8))
            ->setOption(self::OPTION_LENGTH_MAX, array('type' => Option::TYPE_INTEGER, 'default' => 12))
            ->setOption(self::OPTION_UPPER_CASE, array('type' => Option::TYPE_BOOLEAN, 'default' => true))
            ->setMinimumCount(self::OPTION_UPPER_CASE, null)
            ->setMaximumCount(self::OPTION_UPPER_CASE, null)
            ->setParameter(self::PARAMETER_UPPER_CASE, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')
            ->setOption(self::OPTION_LOWER_CASE, array('type' => Option::TYPE_BOOLEAN, 'default' => true))
            ->setMinimumCount(self::OPTION_LOWER_CASE, null)
            ->setMaximumCount(self::OPTION_LOWER_CASE, null)
            ->setParameter(self::PARAMETER_LOWER_CASE, 'abcdefghijklmnopqrstuvwxyz')
            ->setOption(self::OPTION_NUMBERS, array('type' => Option::TYPE_BOOLEAN, 'default' => true))
            ->setMinimumCount(self::OPTION_NUMBERS, null)
            ->setMaximumCount(self::OPTION_NUMBERS, null)
            ->setParameter(self::PARAMETER_NUMBERS, '0123456789')
            ->setOption(self::OPTION_SYMBOLS, array('type' => Option::TYPE_BOOLEAN, 'default' => true))
            ->setMinimumCount(self::OPTION_SYMBOLS, null)
            ->setMaximumCount(self::OPTION_SYMBOLS, null)
            ->setParameter(self::PARAMETER_SYMBOLS, '!@$%^&*()<>,.?/[]{}-=_+')
            ->setOption(self::OPTION_AVOID_SIMILAR, array('type' => Option::TYPE_BOOLEAN, 'default' => true))
            ->setParameter(self::PARAMETER_SIMILAR, 'iIl1Oo0')
        ;
    }

    protected function getCharacters($per)
    {
        $characters = '';

        if ($this->getOptionValue(self::OPTION_UPPER_CASE)) {
            $characters .= $this->getCharactersAsPer(self::OPTION_UPPER_CASE, $this->getParameter(self::PARAMETER_UPPER_CASE, ''), $per);
        }

        if ($this->getOptionValue(self::OPTION_LOWER_CASE)) {
            $characters .= $this->getCharactersAsPer(self::OPTION_LOWER_CASE, $this->getParameter(self::PARAMETER_LOWER_CASE, ''), $per);
        }

        if ($this->getOptionValue(self::OPTION_NUMBERS)) {
            $characters .= $this->getCharactersAsPer(self::OPTION_NUMBERS, $this->getParameter(self::PARAMETER_NUMBERS, ''), $per);
        }

        if ($this->getOptionValue(self::OPTION_SYMBOLS)) {
            $characters .= $this->getCharactersAsPer(self::OPTION_SYMBOLS, $this->getParameter(self::PARAMETER_SYMBOLS, ''), $per);
        }

        return $characters;
    }

    protected function getCharactersAsPer($option, $characters, $per)
    {
        if ($this->getOptionValue(self::OPTION_AVOID_SIMILAR)) {
            $removeCharacters = \str_split($this->getParameter(self::PARAMETER_SIMILAR, ''));
            $characters = \str_replace($removeCharacters, '', $characters);
        }

        $charactersAsPer = '';
        $charactersLength = \strlen($characters);

        if ($per === 'minimum') {
            $count = $this->getMinimumCount($option);
        } else if ($per === 'maximum') {
            $min = $this->getMinimumCount($option);
            $max = $this->getMaximumCount($option);

            $count = $max - $min;

            if ($count <= 0) {
                return $charactersAsPer;
            }
        }

        for ($i = 0; $i < $count; ++$i) {
            $charactersAsPer .= $characters[$this->randomInteger(0, $charactersLength - 1)];
        }

        return $charactersAsPer;
    }

    /**
     * Generate one password based on options.
     *
     * @return string password
     */
    public function generatePassword()
    {
        $password = $this->getCharacters('minimum');
        $passwordLength = \strlen($password);
        $expectedPasswordLength = $this->getLength('minimum');
        if ($passwordLength < $expectedPasswordLength) {
            $numbersOfCharsNeeded = $expectedPasswordLength - $passwordLength;
            $password .= $this->getCharacters('maximum');
            $passwordLength = \strlen($password);
        }

        if ($passwordLength > $expectedPasswordLength) {
            $substrlength = $expectedPasswordLength - $passwordLength;
            $password = substr($password, 0, $substrlength);
        }

        return str_shuffle($password);
    }

    /**
     * Password length.
     *
     * @return int
     */
    public function getLength($lenType)
    {
        if ($lenType === 'minimum') {
            return $this->getOptionValue(self::OPTION_LENGTH_MIN);
        } else if ($lenType === 'maximum') {
            return $this->getOptionValue(self::OPTION_LENGTH_MAX);
        }
    }

    /**
     * Set length of desired password(s).
     *
     * @param int $characterCount
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setLength($characterCount, $lenType)
    {
        if (!is_int($characterCount) || $characterCount < 1) {
            throw new \InvalidArgumentException('Expected positive integer');
        }

        if ($lenType === 'minimum') {
            $this->setOptionValue(self::OPTION_LENGTH_MIN, $characterCount);
        } else if ($lenType === 'maximum') {
            $this->setOptionValue(self::OPTION_LENGTH_MAX, $characterCount);
        }

        return $this;
    }

    /**
     * Are Uppercase characters enabled?
     *
     * @return bool
     */
    public function getUppercase()
    {
        return $this->getOptionValue(self::OPTION_UPPER_CASE);
    }

    /**
     * Enable uppercase characters.
     *
     * @param bool $enable
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setUppercase($enable = true)
    {
        if (!is_bool($enable)) {
            throw new \InvalidArgumentException('Expected boolean');
        }

        $this->setOptionValue(self::OPTION_UPPER_CASE, $enable);

        return $this;
    }

    /**
     * Are Lowercase characters enabled?
     *
     * @return string
     */
    public function getLowercase()
    {
        return $this->getOptionValue(self::OPTION_LOWER_CASE);
    }

    /**
     * Enable lowercase characters.
     *
     * @param bool $enable
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setLowercase($enable = true)
    {
        if (!is_bool($enable)) {
            throw new \InvalidArgumentException('Expected boolean');
        }

        $this->setOptionValue(self::OPTION_LOWER_CASE, $enable);

        return $this;
    }

    /**
     * Are Numbers enabled?
     *
     * @return string
     */
    public function getNumbers()
    {
        return $this->getOptionValue(self::OPTION_NUMBERS);
    }

    /**
     * Enable numbers.
     *
     * @param bool $enable
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setNumbers($enable = true)
    {
        if (!is_bool($enable)) {
            throw new \InvalidArgumentException('Expected boolean');
        }

        $this->setOptionValue(self::OPTION_NUMBERS, $enable);

        return $this;
    }

    /**
     * Are Symbols enabled?
     *
     * @return string
     */
    public function getSymbols()
    {
        return $this->getOptionValue(self::OPTION_SYMBOLS);
    }

    /**
     * Enable symbol characters.
     *
     * @param bool $enable
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setSymbols($enable = true)
    {
        if (!is_bool($enable)) {
            throw new \InvalidArgumentException('Expected boolean');
        }

        $this->setOptionValue(self::OPTION_SYMBOLS, $enable);

        return $this;
    }

    /**
     * Avoid similar characters enabled?
     *
     * @return string
     */
    public function getAvoidSimilar()
    {
        return $this->getOptionValue(self::OPTION_AVOID_SIMILAR);
    }

    /**
     * Enable characters to be removed when avoiding similar characters.
     *
     * @param bool $enable
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setAvoidSimilar($enable = true)
    {
        if (!is_bool($enable)) {
            throw new \InvalidArgumentException('Expected boolean');
        }

        $this->setOptionValue(self::OPTION_AVOID_SIMILAR, $enable);

        return $this;
    }
}
