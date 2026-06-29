<?php
/**
 * Unit tests for ArticleSummaryExtension
 * ArticleSummaryExtension 单元测试
 */

namespace Tests;

use PHPUnit\Framework\TestCase;

// 加载 bootstrap 文件中的模拟类
require_once __DIR__ . '/../phpstan-bootstrap.php';

// 加载主要的类文件
require_once __DIR__ . '/../extension.php';

class TestArticleSummaryEntry extends \FreshRSS_Entry
{
    private string $id;
    private string $content;

    public function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function _content(string $content): void
    {
        $this->content = $content;
    }
}

/**
 * Test class for ArticleSummaryExtension
 * ArticleSummaryExtension 测试类
 */
class ArticleSummaryExtensionTest extends TestCase
{
    /**
     * Test that the extension class exists
     * 测试扩展类是否存在
     */
    public function testExtensionClassExists(): void
    {
        $this->assertTrue(class_exists('ArticleSummaryExtension'));
    }

    /**
     * Test that the extension extends Minz_Extension
     * 测试扩展是否继承自 Minz_Extension
     */
    public function testExtensionExtendsMinzExtension(): void
    {
        $reflection = new \ReflectionClass('ArticleSummaryExtension');
        $this->assertTrue($reflection->isSubclassOf('Minz_Extension'));
    }

    /**
     * Test that the extension is final
     * 测试扩展是否为 final 类
     */
    public function testExtensionIsFinal(): void
    {
        $reflection = new \ReflectionClass('ArticleSummaryExtension');
        $this->assertTrue($reflection->isFinal());
    }

    /**
     * Test that the init method exists
     * 测试 init 方法是否存在
     */
    public function testInitMethodExists(): void
    {
        $this->assertTrue(method_exists('ArticleSummaryExtension', 'init'));
    }

    /**
     * Test that the addSummaryButton method exists
     * 测试 addSummaryButton 方法是否存在
     */
    public function testAddSummaryButtonMethodExists(): void
    {
        $this->assertTrue(method_exists('ArticleSummaryExtension', 'addSummaryButton'));
    }

    /**
     * Test that the handleConfigureAction method exists
     * 测试 handleConfigureAction 方法是否存在
     */
    public function testHandleConfigureActionMethodExists(): void
    {
        $this->assertTrue(method_exists('ArticleSummaryExtension', 'handleConfigureAction'));
    }

    public function testAddSummaryButtonRendersCenteredHeaderAndVisibleTextCount(): void
    {
        $extension = new \ArticleSummaryExtension();
        $entry = new TestArticleSummaryEntry('entry-1', '<p>Hello <strong>世界</strong>&nbsp;!</p>');

        $result = $extension->addSummaryButton($entry);
        $content = $result->content();

        $this->assertStringContainsString('class="oai-summary-header"', $content);
        $this->assertStringContainsString('class="oai-summary-meta"', $content);
        $this->assertStringContainsString('全文约 8 字', $content);
    }

    public function testAddSummaryButtonDoesNotDoubleEscapeRequestUrl(): void
    {
        \Minz_Url::$displayUrl = './?c=ArticleSummary&amp;a=summarize&amp;params%5Bid%5D=entry-1';
        $extension = new \ArticleSummaryExtension();
        $entry = new TestArticleSummaryEntry('entry-1', '<p>Hello</p>');

        $result = $extension->addSummaryButton($entry);
        $content = $result->content();

        $this->assertStringContainsString(
            'data-request="./?c=ArticleSummary&amp;a=summarize&amp;params%5Bid%5D=entry-1"',
            $content
        );
        $this->assertStringNotContainsString('&amp;amp;', $content);
    }

    public function testChineseSummaryButtonLabelIsAiSummary(): void
    {
        $translations = require __DIR__ . '/../i18n/zh-CN/ArticleSummary.php';

        $this->assertSame('AI总结', $translations['button']['summarize']);
    }
}
