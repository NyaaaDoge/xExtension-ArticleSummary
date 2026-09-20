# AI Summary Button Layout Design

## Goal

Update the article summary control without changing the summary request flow.

## Requirements

- Change the summary button label to `AI总结` in Chinese locales.
- Keep the button and word-count note on the same centered row when space allows.
- Let the row wrap on narrow containers so it does not overflow.
- Compute the note from the existing article HTML with lightweight server-side visible-text counting.
- Do not run the heavier HTML-to-Markdown conversion used for AI requests just to calculate the note.

## Implementation

- Add a focused helper on `ArticleSummaryExtension` that strips tags, decodes entities, normalizes whitespace, and counts Unicode characters with `mb_strlen` when available.
- Render a new `.oai-summary-header` inside `.oai-summary-wrap` containing the existing `.oai-summary-btn` and a new `.oai-summary-meta`.
- Keep existing data attributes and request URL behavior unchanged.
- Update CSS so `.oai-summary-header` uses centered flex layout with wrapping, while `.oai-summary-content` remains below it.

## Testing

- Add a PHPUnit test that calls `addSummaryButton()` on an entry with HTML content and asserts the rendered output includes the AI label, centered-row hook, and expected visible-text word count.
