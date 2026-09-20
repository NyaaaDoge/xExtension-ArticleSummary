# AI Summary Button Layout Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update the article summary control to show an `AI总结` button and a centered same-row visible-text word count without changing summary request behavior.

**Architecture:** Keep rendering inside `ArticleSummaryExtension::addSummaryButton()`. Add a private helper for lightweight visible-text counting from the existing entry HTML, then adjust only the wrapper markup and CSS.

**Tech Stack:** PHP FreshRSS extension, PHPUnit tests, CSS flexbox.

---

### Task 1: Add Rendering Coverage

**Files:**
- Modify: `tests/ArticleSummaryExtensionTest.php`

- [ ] **Step 1: Add a fake entry and assertions**

Add a test-local class extending `FreshRSS_Entry` with mutable content. Assert `addSummaryButton()` renders `.oai-summary-header`, `.oai-summary-meta`, and a count for visible text from HTML.

- [ ] **Step 2: Run the targeted test and verify it fails**

Run: `vendor\bin\phpunit.bat tests\ArticleSummaryExtensionTest.php`

Expected before implementation: failure because the new header/meta markup and word-count note do not exist.

### Task 2: Implement Markup and Counting

**Files:**
- Modify: `extension.php`
- Modify: `i18n/zh-CN/ArticleSummary.php`
- Modify: `i18n/zh-TW/ArticleSummary.php`
- Modify: `i18n/en/ArticleSummary.php`

- [ ] **Step 1: Add lightweight visible-text counting**

Add a private method on `ArticleSummaryExtension` that strips tags, decodes HTML entities, normalizes whitespace, and counts characters with `mb_strlen()` when available.

- [ ] **Step 2: Render the centered header row**

Wrap the existing button in `.oai-summary-header` and add `.oai-summary-meta` with `全文约 N 字`.

- [ ] **Step 3: Update button translations**

Set Chinese locale `button.summarize` values to `AI总结`; set English to `AI Summary`.

### Task 3: Fit Layout Without Overflow

**Files:**
- Modify: `static/style.css`

- [ ] **Step 1: Add centered flex row styles**

Use `display: flex`, `justify-content: center`, `align-items: center`, `gap`, and `flex-wrap: wrap` on `.oai-summary-header`.

- [ ] **Step 2: Make button and note stable**

Remove the old block-centered button margin, keep the button from shrinking, and allow the note to wrap with `overflow-wrap: anywhere`.

### Task 4: Verify

**Files:**
- Verify: `extension.php`
- Verify: `static/style.css`
- Verify: `tests/ArticleSummaryExtensionTest.php`

- [ ] **Step 1: Run syntax checks**

Run: `php -l extension.php`

Expected: `No syntax errors detected in extension.php`

- [ ] **Step 2: Run available tests**

Run: `vendor\bin\phpunit.bat tests\ArticleSummaryExtensionTest.php` if dependencies are installed.

If `vendor/` is missing, report that PHPUnit could not be run in this checkout.
