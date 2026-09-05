<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\NumberHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NumberHelperTest extends TestCase
{

    #[Test]
    public function format_formatsNumberWithoutDecimalsByDefault()
    {
        $this->assertSame('1 235', NumberHelper::format(1234.56));
    }

    #[Test]
    public function format_formatsNumberWithDecimals()
    {
        $this->assertSame('1 234,57', NumberHelper::format(1234.567, 2));
    }

    #[Test]
    public function format_usesCustomDecimalPoint()
    {
        $this->assertSame('1 234.57', NumberHelper::format(1234.567, 2, '.'));
    }

    #[Test]
    public function format_usesCustomThousandsSeparator()
    {
        $this->assertSame(
            '1,234.57',
            NumberHelper::format(1234.567, 2, '.', ',')
        );
    }

    #[Test]
    public function format_formatsNegativeNumber()
    {
        $this->assertSame('-1 234,57', NumberHelper::format(-1234.567, 2));
    }

    #[Test]
    public function percent_calculatesPercentage()
    {
        $this->assertSame(25.0, NumberHelper::percent(50, 200));
    }

    #[Test]
    public function percent_appliesPrecision()
    {
        $this->assertSame(33.33, NumberHelper::percent(1, 3));
    }

    #[Test]
    public function percent_supportsZeroPrecision()
    {
        $this->assertSame(33.0, NumberHelper::percent(1, 3, 0));
    }

    #[Test]
    public function percent_supportsCustomPrecision()
    {
        $this->assertSame(33.3333, NumberHelper::percent(1, 3, 4));
    }

    #[Test]
    public function percent_returnsZeroWhenTotalIsZero()
    {
        $this->assertSame(0.0, NumberHelper::percent(10, 0));
    }

    #[Test]
    public function percent_supportsValuesGreaterThanTotal()
    {
        $this->assertSame(150.0, NumberHelper::percent(150, 100));
    }

    #[Test]
    public function percent_supportsNegativeValues()
    {
        $this->assertSame(-25.0, NumberHelper::percent(-50, 200));
    }

    #[Test]
    public function generateIntegerCode_generatesSixDigitCodeByDefault()
    {
        $result = NumberHelper::generateIntegerCode();

        $this->assertGreaterThanOrEqual(100000, $result);
        $this->assertLessThanOrEqual(999999, $result);
    }

    #[Test]
    public function generateIntegerCode_generatesCodeWithSpecifiedLength()
    {
        $result = NumberHelper::generateIntegerCode(4);

        $this->assertGreaterThanOrEqual(1000, $result);
        $this->assertLessThanOrEqual(9999, $result);
    }

    #[Test]
    public function generateIntegerCode_generatesOneDigitCode()
    {
        $result = NumberHelper::generateIntegerCode(1);

        $this->assertGreaterThanOrEqual(1, $result);
        $this->assertLessThanOrEqual(9, $result);
    }

    #[Test]
    public function generateIntegerCode_returnsZeroForInvalidLength()
    {
        $this->assertSame(0, NumberHelper::generateIntegerCode(0));

        $this->assertSame(0, NumberHelper::generateIntegerCode(-1));
    }

    #[Test]
    public function ceiling_roundsNumberUpToIntegerByDefault()
    {
        $this->assertSame(1.0, NumberHelper::ceiling(1.1));
    }

    #[Test]
    public function ceiling_roundsNumberToSpecifiedPlacement()
    {
        $this->assertSame(1.3, NumberHelper::ceiling(1.251, 2));
    }

    #[Test]
    public function ceiling_keepsNumberWhenAlreadyAtPlacement()
    {
        $this->assertSame(1.2, NumberHelper::ceiling(1.2, 2));
    }

    #[Test]
    public function ceiling_usesMinimumPlacementOfOne()
    {
        $this->assertSame(1.0, NumberHelper::ceiling(1.1, 0));

        $this->assertSame(1.0, NumberHelper::ceiling(1.1, -2));
    }

    #[Test]
    public function ceiling_handlesNegativeNumber()
    {
        $this->assertSame(-1.2, NumberHelper::ceiling(-1.21, 2));
    }
}
