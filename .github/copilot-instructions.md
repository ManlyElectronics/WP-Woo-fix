# AI Agent Instructions: Manly Fix

## Project Overview
A lightweight WordPress plugin that removes XHTML-style trailing slashes (`/>`) from HTML5 void elements globally across all page output. This converts `<meta ... />` to `<meta ... >` format for standards-compliant HTML5 output.

## Core Architecture
The plugin uses a single hook-based approach:
- **Hook**: `template_redirect` (runs before output sent to browser)
- **Method**: Output buffering with regex pattern matching and replacement
- **Scope**: Operates on entire rendered HTML page content

## Key Implementation Patterns

### Void Elements List
The plugin targets 18 HTML void elements (self-closing tags that never have content):
```
meta, link, base, img, input, br, hr, source, track, embed, area, col, command, keygen, param, wbr
```
These are hardcoded in the `$void_elements` array - if adding more, update this list.

### Regex Pattern Logic
Pattern: `#<(tag)(\s[^<>]*?)(\s*)/?>#i`
- Captures opening tag and attributes: `(\s[^<>]*?)`
- Handles optional whitespace before slash: `(\s*)?`
- Case-insensitive flag: `i`
- Replacement preserves attributes and closes with `<tag attributes>`

## Critical Workflows

### Testing Changes
1. Install plugin in WordPress admin
2. Inspect page source (browser developer tools) to verify trailing slashes are removed
3. Test with pages containing void elements (images, meta tags, links)
4. Verify no HTML corruption in attributes

### Debugging
- Add error logging if regex fails: `error_log()` within the closure
- Check `ob_start()` isn't conflicting with other plugins
- Verify plugin loads: check `wp-cli plugin list`

## Project-Specific Considerations

### Why This Plugin Exists
XHTML-style trailing slashes are unnecessary in HTML5 and can interfere with certain systems. Manly Fix maintains HTML5 standards compliance by removing them globally.

### Performance Notes
- Uses output buffering: minimal overhead on typical sites (processes once at output stage)
- Regex runs on entire page: monitor on high-traffic sites with large pages
- No database queries - purely markup transformation

### Version & Compatibility
- Current version: 1.2
- Target: WordPress sites requiring HTML5 compliance
- No external dependencies beyond WordPress core

## File Structure
```
manly-fix-trailing-slash/
├── manly-fix-trailing-slash.php     (all logic - single file)
├── readme.txt                       (WordPress plugin readme)
└── .github/copilot-instructions.md  (this file)
```

## Modification Guidelines

When adding features or fixing bugs:
1. Maintain single-file structure (simplicity)
2. Keep void elements list synchronized with HTML5 spec
3. Test regex against sample HTML pages before deploying
4. Update Version header in file comments
5. Avoid adding admin pages or database options (keep lightweight)

## Common Pitfalls & Edge Cases

### Regex Pattern Considerations
- The pattern `(\s[^<>]*?)` requires at least one space after the tag - tags with NO attributes won't match. Example: `<br/>` won't be caught, only `<br />`. This is intentional (unattributed void elements are less common).
- The `[^<>]*?` is non-greedy to stop at first `>`, preventing matches across multiple tags.
- Case-insensitive flag handles `<META />`, `<Meta />`, etc.

### Output Buffering Conflicts
- If another plugin uses `ob_start()` after this one, it may intercept buffered content first. Verify plugin load order in `/wp-content/plugins/` (alphabetical).
- The closure captures `$void_elements` by value - safe for concurrent requests.

### Testing Real-World Output
Always test with actual WordPress page output (not just isolated HTML snippets):
- Test with plugins that output void elements (Jetpack, site icons, etc.)
- Verify attribute preservation: `<meta name="viewport" content="..." />` → `<meta name="viewport" content="...">` (exact match)
- Check for false positives in inline scripts/styles containing the text `<meta` (unlikely but test anyway)
