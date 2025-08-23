# Raw Materials Authentication Fix Plan

## Problem Analysis

**Issue:** "Unauthenticated" error when creating raw materials

**Root Cause:**
- Frontend form submits to `/api/raw-materials` (API route)
- API routes use `auth:sanctum` middleware (token-based authentication)
- Web application uses session-based authentication (`auth` middleware)
- Authentication method mismatch causes the error

## Solution: Add Web Routes (Option A)

### 1. Add Web Routes for Raw Materials

Add these routes to `routes/web.php` inside the existing `auth` middleware group, within the manufacturing section:

```php
// Add to manufacturing raw-materials section (around line 179-219)
Route::prefix('raw-materials')->name('raw-materials.')->group(function () {
    // Existing view routes...
    
    // Add CRUD routes
    Route::post('/', [App\Http\Controllers\RawMaterialController::class, 'store'])->name('store');
    Route::get('/{id}/show', [App\Http\Controllers\RawMaterialController::class, 'show'])->name('show');
    Route::put('/{id}', [App\Http\Controllers\RawMaterialController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\RawMaterialController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/adjust-stock', [App\Http\Controllers\RawMaterialController::class, 'adjustStock'])->name('adjust-stock');
    Route::get('/stats', [App\Http\Controllers\RawMaterialController::class, 'statistics'])->name('statistics');
    Route::get('/low-stock', [App\Http\Controllers\RawMaterialController::class, 'lowStock'])->name('low-stock');
});
```

### 2. Update Frontend Form

Modify `resources/views/manufacturing/raw-materials/create.blade.php`:

**Change the form submission URL:**
- From: `/api/raw-materials` (line 345)
- To: `/manufacturing/raw-materials` (web route)

**Add CSRF token:**
- Ensure CSRF token is included in headers
- The token is already being fetched (line 308) but needs to be sent properly

**Update the fetch request:**
```javascript
const response = await fetch('/manufacturing/raw-materials', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    },
    credentials: 'same-origin', // Important for session auth
    body: JSON.stringify(this.form)
});
```

### 3. Modify RawMaterialController Response Format

The controller currently returns JSON responses suitable for API usage. For web routes, we need to ensure:

1. **Success responses** return JSON with proper structure
2. **Error responses** are handled appropriately for web context
3. **Validation errors** are formatted correctly

### 4. Test the Implementation

1. **Login to the web application**
2. **Navigate to raw materials creation form**
3. **Fill out the form with test data**
4. **Submit and verify successful creation**
5. **Check that the data is saved to database**

## Expected Outcome

After implementing these changes:
- ✅ Form submission will use session-based authentication
- ✅ No more "Unauthenticated" errors
- ✅ Raw materials can be created successfully
- ✅ Consistent authentication method across the application

## Files to Modify

1. `routes/web.php` - Add web routes for raw materials CRUD
2. `resources/views/manufacturing/raw-materials/create.blade.php` - Update form submission
3. Test the functionality

## Implementation Priority

1. **High Priority:** Add web routes and update form submission
2. **Medium Priority:** Ensure proper error handling
3. **Low Priority:** Optimize response formats if needed

## Notes

- This solution maintains consistency with existing web authentication
- No changes needed to API routes (they remain for future API usage)
- Minimal impact on existing codebase
- Easy to test and verify