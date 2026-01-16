# Screenshot Upload Issue - Current Status

## Problem
The script is successfully capturing screenshots but failing to upload them to tasks via the `/tasks/{uuid}/attachments` endpoint.

## Error
```
HTTP 500: {"error":"Failed to parse body as FormData."}
```

## What We've Tried
1. ✅ Using `form-data` package with buffers
2. ✅ Using `form-data` package with streams  
3. ✅ Using `formdata-node` package
4. ✅ Using `node-fetch` instead of native fetch
5. ✅ Setting Content-Type headers correctly (verified: `multipart/form-data; boundary=...`)

## Current Implementation
- Using `form-data` package
- Using `createReadStream` for file
- Setting headers with `formData.getHeaders()`
- Content-Type header is correctly set

## Next Steps to Try
1. Check if the API expects a different field name (maybe `attachment` instead of `file`?)
2. Try using Node.js 18+ native FormData if available
3. Check API documentation for exact expected format
4. Contact API maintainer about the parsing error
5. Consider using a different upload method if available

## User's Original Code Reference
```javascript
const formData = new FormData();
formData.append('file', new Blob([fileBuffer], { type: 'image/png' }), 'screenshot.png');
formData.append('tag', 'reference');

await fetch('https://app.noteware.dev/api/agent/tasks/{uuid}/attachments', {
  method: 'POST',
  headers: { 'x-agent-key': API_KEY },
  body: formData
});
```

Note: User's code uses `new Blob()` which suggests browser environment. Our Node.js implementation should be equivalent but server is rejecting it.
