<?php

namespace CommerceGuys\Intl\NumberFormat;

use CommerceGuys\Intl\LocaleResolverTrait;
use Symfony\Component\Yaml\Parser;

/**
 * Manages number formats based on YAML definitions.
 */
class DefaultNumberFormatManager implements NumberFormatManagerInterface
{
    use LocaleResolverTrait;

    /**
     * Number format definitions.
     *
     * @var array
     */
    protected $definitions = array();

    /**
     * The yaml parser.
     *
     * @var \Symfony\Component\Yaml\Parser
     */
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->definitionPath = __DIR__ . '/../../resources/number_format/';
    }

    /**
     * {@inheritdoc}
     */
    public function get($locale, $fallbackLocale = null)
    {
        $locale = $this->resolveLocale($locale, $fallbackLocale);
        if (!isset($this->definitions[$locale])) {
            $filename = $this->definitionPath . $locale . '.yml';
            $this->definitions[$locale] = $this->parser->parse(file_get_contents($filename));
        }

        return $this->createNumberFormatFromDefinition($this->definitions[$locale]);
    }

    /**
     * Creates a number format object from the provided definition.
     *
     * @param array $definition The number format definition.
     *
     * @return \CommerceGuys\Intl\NumberFormat\NumberFormat
     */
    protected function createNumberFormatFromDefinition(array $definition)
    {
        $definition += array(
            'decimal_separator' => '.',
            'grouping_separator' => ',',
            'plus_sign' => '+',
            'minus_sign' => '-',
            'percent_sign' => '%',
        );

        $numberFormat = new NumberFormat();
        $numberFormat->setLocale($definition['locale']);
        $numberFormat->setNumberingSystem($definition['numbering_system']);
        $numberFormat->setDecimalSeparator($definition['decimal_separator']);
        $numberFormat->setGroupingSeparator($definition['grouping_separator']);
        $numberFormat->setPlusSign($definition['plus_sign']);
        $numberFormat->setMinusSign($definition['minus_sign']);
        $numberFormat->setPercentSign($definition['percent_sign']);
        $numberFormat->setDecimalPattern($definition['decimal_pattern']);
        $numberFormat->setPercentPattern($definition['percent_pattern']);
        $numberFormat->setCurrencyPattern($definition['currency_pattern']);
        $numberFormat->setAccountingCurrencyPattern($definition['accounting_currency_pattern']);

        return $numberFormat;
    }
}