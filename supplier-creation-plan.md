# Supplier Creation Form Implementation Plan

## Overview
Create a comprehensive supplier creation form for the manufacturing system that allows users to add new suppliers with complete information including contact details, product categories, and business information.

## Requirements Analysis

### Current State
- Supplier list page exists at `/manufacturing/raw-materials/suppliers`
- "Tambah Pemasok" button exists but no creation form
- Need to add route for supplier creation
- Need to create supplier creation view

### User Requirements
- Add new suppliers with complete business information
- Capture contact person details
- Specify product categories they supply
- Set supplier rating and status
- Mobile-responsive form design
- Form validation and error handling

## Technical Implementation Plan

### 1. Route Addition
Add supplier creation route to `routes/web.php`:
```php
Route::get('/suppliers/create', function () {
    return view('manufacturing.raw-materials.create-supplier');
})->name('suppliers.create');
```

### 2. View Creation
Create `resources/views/manufacturing/raw-materials/create-supplier.blade.php` with:

#### Form Fields
- **Company Information**
  - Nama Perusahaan (Company Name) - Required
  - Jenis Perusahaan (Company Type) - Dropdown: PT, CV, UD, Toko, Perorangan
  - NPWP (Tax ID) - Optional
  - Alamat Lengkap (Full Address) - Required, Textarea

- **Contact Information**
  - Nama Kontak Utama (Primary Contact Name) - Required
  - Jabatan (Position) - Optional
  - Nomor Telepon (Phone Number) - Required
  - Email - Required, Email validation
  - WhatsApp - Optional

- **Business Details**
  - Kategori Produk (Product Categories) - Multiple select checkboxes
    - Bahan Utama (Main Ingredients)
    - Pemanis (Sweeteners)
    - Herbal
    - Kemasan (Packaging)
    - Lainnya (Others)
  - Minimum Order - Optional, Number input
  - Lead Time (hari) - Optional, Number input
  - Payment Terms - Dropdown: Cash, 7 hari, 14 hari, 30 hari, 60 hari

- **Rating & Status**
  - Rating Awal (Initial Rating) - Star rating 1-5, Default: 3
  - Status - Radio: Aktif, Tidak Aktif, Default: Aktif
  - Catatan (Notes) - Optional, Textarea

#### Form Features
- Real-time validation
- Mobile-responsive design
- Progress indicator for form completion
- Save as draft functionality
- Clear form button
- Cancel and return to suppliers list

### 3. Form Validation Rules
```javascript
- Company Name: Required, min 3 characters
- Address: Required, min 10 characters
- Contact Name: Required, min 3 characters
- Phone: Required, Indonesian phone format
- Email: Required, valid email format
- Product Categories: At least one must be selected
```

### 4. UI/UX Design
- Consistent with existing manufacturing module design
- Card-based layout with sections
- Tailwind CSS for styling
- Mobile-first responsive design
- Loading states and success/error messages
- Breadcrumb navigation

### 5. Form Submission
- POST to `/api/suppliers` endpoint
- Success: Redirect to suppliers list with success message
- Error: Show validation errors inline
- Loading state during submission

### 6. API Integration
Mock API response for supplier creation:
```json
{
  "success": true,
  "message": "Pemasok berhasil ditambahkan",
  "data": {
    "id": 4,
    "name": "New Supplier Name"
  }
}
```

## Implementation Steps

1. **Add Route** - Add supplier creation route to web.php
2. **Create View** - Build comprehensive supplier creation form
3. **Add Validation** - Implement client-side and server-side validation
4. **Test Form** - Ensure all fields work correctly
5. **Mobile Testing** - Verify responsive design
6. **Integration** - Connect form to existing supplier list

## Success Criteria
- ✅ Form loads without errors
- ✅ All fields are properly validated
- ✅ Mobile responsive design works
- ✅ Form submission works with mock API
- ✅ Success/error messages display correctly
- ✅ Navigation back to suppliers list works
- ✅ Form matches existing design system

## Files to Create/Modify
1. `routes/web.php` - Add supplier creation route
2. `resources/views/manufacturing/raw-materials/create-supplier.blade.php` - New supplier form
3. Update suppliers list to link to creation form properly

## Next Steps
Switch to Code mode to implement the planned supplier creation functionality.