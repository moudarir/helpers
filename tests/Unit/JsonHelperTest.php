<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use JsonException;
use Moudarir\Helpers\JsonHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JsonHelperTest extends TestCase
{

    #[Test]
    public function decode_decodesValidJsonAsArray()
    {
        $json = '{"name":"John","age":30}';

        $result = JsonHelper::decode($json);

        $this->assertSame([
            'name' => 'John',
            'age' => 30,
        ], $result);
    }

    #[Test]
    public function decode_decodesValidJsonAsObjectWhenAssocIsFalse()
    {
        $json = '{"name":"John","age":30}';

        $result = JsonHelper::decode($json, false);

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertSame('John', $result->name);
        $this->assertSame(30, $result->age);
    }

    #[Test]
    public function decode_decodesValidJsonScalar()
    {
        $this->assertSame('hello', JsonHelper::decode('"hello"'));
        $this->assertSame(123, JsonHelper::decode('123'));
        $this->assertTrue(JsonHelper::decode('true'));
    }

    #[Test]
    public function decode_returnsNullForInvalidJson()
    {
        $this->assertNull(JsonHelper::decode('{"name":"John"'));
    }

    #[Test]
    public function decode_returnsNullForJsonNull()
    {
        $this->assertNull(JsonHelper::decode('null'));
    }

    #[Test]
    public function jsonify_encodesArray()
    {
        $data = [
            'name' => 'John',
            'age' => 30,
        ];

        $result = JsonHelper::jsonify($data, false);

        $this->assertSame('{"name":"John","age":30}', $result);
    }

    #[Test]
    public function jsonify_usesHexEncoding()
    {
        $data = [
            'value' => '<tag>"test"&\'quote\'',
        ];

        $result = JsonHelper::jsonify($data, false);

        $expected = '{"value":"\u003Ctag\u003E\u0022test\u0022' . '\u0026\u0027quote\u0027"}';

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function jsonify_returnsJsonWhenHeaderIsEnabled()
    {
        $data = ['success' => true];

        $result = JsonHelper::jsonify($data);

        $this->assertSame('{"success":true}', $result);
    }

    #[Test]
    public function jsonify_returnsJsonWhenHeaderIsDisabled()
    {
        $data = ['success' => true];

        $result = JsonHelper::jsonify($data, false);

        $this->assertSame('{"success":true}', $result);
    }

    #[Test]
    public function jsonify_returnsEmptyStringWhenEncodingFails()
    {
        $data = ["\xB1\x31"];

        $this->assertSame('', JsonHelper::jsonify($data, false));
    }

    #[Test]
    public function encodeForLdFormat_wrapsJsonInScriptTag()
    {
        $result = JsonHelper::encodeForLdFormat([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
        ]);

        $expected = <<<HTML
<script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "Article"
}
</script>
HTML;

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function encodeForLdFormat_usesPrettyPrintedJson()
    {
        $schema = [
            'name' => 'Example',
            'value' => 123,
        ];

        $result = JsonHelper::encodeForLdFormat($schema);

        $expected = "{\n"
            . "    \"name\": \"Example\",\n"
            . "    \"value\": 123\n"
            . "}";

        $this->assertStringContainsString($expected, $result);
    }

    #[Test]
    public function encodeForLdFormat_doesNotEscapeSlashes()
    {
        $schema = [
            'url' => 'https://example.com/test',
        ];

        $result = JsonHelper::encodeForLdFormat($schema);

        $this->assertStringContainsString('"url": "https://example.com/test"', $result);
        $this->assertStringNotContainsString('https:\\/', $result);
    }

    #[Test]
    public function encodeForLdFormat_returnsEmptyStringWhenEncodingFails()
    {
        $schema = ["\xB1\x31"];

        $this->assertSame('', JsonHelper::encodeForLdFormat($schema));
    }

    #[Test]
    public function encodeAsJsonL_encodesEachItemAsJson()
    {
        $data = [
            ['id' => 1, 'name' => 'One'],
            ['id' => 2, 'name' => 'Two'],
        ];

        $result = JsonHelper::encodeAsJsonL($data);

        $expected = '{"id":1,"name":"One"}' . "\n" . '{"id":2,"name":"Two"}';

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function encodeAsJsonL_separatesItemsWithNewLines()
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $result = JsonHelper::encodeAsJsonL($data);

        $this->assertSame("{\"id\":1}\n{\"id\":2}\n{\"id\":3}", $result);
    }

    #[Test]
    public function encodeAsJsonL_returnsEmptyStringForEmptyArray()
    {
        $this->assertSame('', JsonHelper::encodeAsJsonL([]));
    }

    #[Test]
    public function encodeAsJsonL_throwsExceptionForInvalidUtf8()
    {
        $data = [["\xB1\x31"]];

        $this->expectException(JsonException::class);

        JsonHelper::encodeAsJsonL($data);
    }

    #[Test]
    public function decodeFromJsonL_decodesMultipleLines()
    {
        $response = <<<JSON
{"id":1,"name":"One"}
{"id":2,"name":"Two"}
JSON;

        $result = JsonHelper::decodeFromJsonL($response);

        $this->assertSame([
            ['id' => 1, 'name' => 'One'],
            ['id' => 2, 'name' => 'Two'],
        ], $result);
    }

    #[Test]
    public function decodeFromJsonL_ignoresEmptyLines()
    {
        $response = "\n{\"id\":1}\n\n{\"id\":2}\n";

        $result = JsonHelper::decodeFromJsonL($response);

        $this->assertSame([
            ['id' => 1],
            ['id' => 2],
        ], $result);
    }

    #[Test]
    public function decodeFromJsonL_trimsInput()
    {
        $response = "  \n{\"id\":1}\n{\"id\":2}\n  ";

        $result = JsonHelper::decodeFromJsonL($response);

        $this->assertSame([
            ['id' => 1],
            ['id' => 2],
        ], $result);
    }

    #[Test]
    public function decodeFromJsonL_acceptsDifferentLineEndings()
    {
        $response = "{\"id\":1}\r\n{\"id\":2}\r{\"id\":3}\n";

        $result = JsonHelper::decodeFromJsonL($response);

        $this->assertSame([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ], $result);
    }

    #[Test]
    public function decodeFromJsonL_returnsEmptyArrayForEmptyInput()
    {
        $this->assertSame([], JsonHelper::decodeFromJsonL(''));
        $this->assertSame([], JsonHelper::decodeFromJsonL(" \n\t "));
    }

    #[Test]
    public function decodeFromJsonL_throwsExceptionForInvalidJson()
    {
        $response = "{\"id\":1}\n{\"id\":2";

        $this->expectException(JsonException::class);

        JsonHelper::decodeFromJsonL($response);
    }

    #[Test]
    public function decodeFromJsonL_throwsTypeErrorForNonArrayJsonValue()
    {
        $this->expectException(\TypeError::class);

        JsonHelper::decodeFromJsonL("123\ntrue");
    }
}
