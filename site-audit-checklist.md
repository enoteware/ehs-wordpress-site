# Site Audit Checklist

Use this checklist to determine which sites to keep vs. delete before migration.

## For Each Site:

### 1. Activity Check
- [ ] Last content update date?
- [ ] Current traffic (Google Analytics)?
- [ ] Any recent client requests?
- [ ] Site still serves a purpose?

### 2. Client Status
- [ ] Is client still active?
- [ ] Do they pay hosting fees?
- [ ] Have they requested site removal?
- [ ] Contact info still valid?

### 3. Technical Status
- [ ] Site accessible and working?
- [ ] Any security issues?
- [ ] WordPress version current?
- [ ] Plugins/themes updated?

### 4. Decision
- [ ] **KEEP** - Active, needed, client pays
- [ ] **DELETE** - Inactive, no client, no purpose
- [ ] **ARCHIVE** - Keep backup but don't migrate
- [ ] **STATIC** - Convert to static site (Vercel)

---

## Quick Audit Script

Run this to check site status:

```bash
# Check if site is accessible
curl -I https://example.com

# Check last modified date
curl -I https://example.com | grep -i "last-modified"

# Check WordPress version (if not hidden)
curl -s https://example.com | grep -i "wp-content"
```

---

## Sites Needing Immediate Review

Based on the list, these look suspicious:

1. **old.avmw.com** - "old" subdomain = likely inactive
2. **4d20afbdc3.nxcli.net** - Temporary/staging domain
3. **frednoteware.com** - Personal site? Still needed?
4. Sites with no recent activity

---

## Migration Priority

### HIGH Priority (Migrate First)
- ehsanalytical.com (active project)
- notewaredigital.com (your agency)

### MEDIUM Priority (Migrate After High)
- Sites with staging environments
- Sites with active clients

### LOW Priority (Review First)
- Sites with no recent activity
- Personal/old sites
- Test/staging sites

### DELETE
- Confirmed inactive sites
- Old/staging domains
- Sites with no purpose
