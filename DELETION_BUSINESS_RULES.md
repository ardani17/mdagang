# Raw Material Deletion Business Rules

## Overview
This document outlines the enhanced deletion functionality for raw materials in the manufacturing system.

## Business Rules

### 1. Deletion Validation
- ✅ **Materials with stock**: Can be deleted (stock will be automatically adjusted to zero)
- ❌ **Materials used in active recipes**: Cannot be deleted (must be removed from recipes first)
- ✅ **Materials with zero stock**: Can be deleted immediately
- ✅ **Inactive materials**: Can be deleted regardless of stock status

### 2. Stock Adjustment Process
When deleting a material with current stock > 0:

1. **Stock Movement Record**: Automatically created with:
   - Type: `raw_material`
   - Movement Type: `out`
   - Quantity: Current stock amount
   - Reason: `material_deletion`
   - Notes: "Stock adjusted to zero before material deletion - [Material Name]"

2. **Activity Log**: Records the stock adjustment with:
   - Old stock value
   - New stock value (0)
   - Reason for adjustment
   - Financial impact (stock value)

3. **Material Update**: 
   - Current stock set to 0
   - Status updated to `out_of_stock`

4. **Final Deletion**: Material record is permanently removed

### 3. User Experience

#### Confirmation Dialog
- **Materials without stock**: Simple confirmation dialog
- **Materials with stock**: Enhanced dialog showing:
  - Current stock amount and unit
  - Estimated stock value
  - Warning about automatic stock adjustment
  - Irreversible action notice

#### Success Messages
- **Without stock**: "Raw material deleted successfully"
- **With stock**: "Raw material deleted successfully. Stock was automatically adjusted to zero and recorded in movement history."

### 4. Audit Trail

All deletion operations create comprehensive audit trails:

1. **Stock Movement Records**: For materials with stock
2. **Activity Logs**: For both stock adjustments and deletions
3. **Detailed Messages**: Including stock quantities and values

### 5. Error Handling

#### Cannot Delete Scenarios
- Material is used in active recipes
- Database transaction fails
- User lacks proper permissions

#### Error Messages
- Clear, actionable error messages in Indonesian
- Specific guidance for resolving issues
- Context-aware help text

## Implementation Details

### Backend Changes
- Enhanced `destroy` method in `RawMaterialController`
- Automatic stock adjustment with proper transaction handling
- Improved error messages and validation

### Frontend Changes
- Enhanced confirmation dialogs with stock information
- Better success/error message handling
- Responsive design for mobile devices

### Database Impact
- Stock movements table receives new records for deletions
- Activity logs table tracks all deletion activities
- No schema changes required

## Testing Scenarios

### Test Cases Covered
1. ✅ Delete material with zero stock
2. ✅ Delete material with positive stock
3. ✅ Attempt to delete material used in recipes
4. ✅ Handle database transaction failures
5. ✅ Verify stock movement record creation
6. ✅ Confirm activity log entries
7. ✅ Test frontend confirmation dialogs
8. ✅ Validate success/error message display

### Edge Cases
- Materials with very high stock values
- Materials with decimal stock quantities
- Concurrent deletion attempts
- Network failures during deletion

## Security Considerations

- CSRF token validation on all deletion requests
- User authentication required
- Activity logging for audit purposes
- Transaction rollback on failures

## Performance Impact

- Minimal performance impact
- Single database transaction for entire operation
- Efficient stock movement record creation
- No additional queries for simple deletions

## Future Enhancements

### Potential Improvements
1. **Bulk Deletion**: Allow multiple materials to be deleted at once
2. **Soft Delete Option**: Alternative to hard deletion
3. **Approval Workflow**: Require approval for high-value deletions
4. **Export Before Delete**: Automatic backup of deleted materials
5. **Restoration Feature**: Ability to restore recently deleted materials

### Configuration Options
- Maximum stock value for automatic deletion
- Required approval thresholds
- Retention period for deletion logs
- Email notifications for deletions

## Conclusion

The enhanced deletion functionality provides a better user experience while maintaining data integrity and comprehensive audit trails. Users can now delete materials with stock, and the system automatically handles the necessary adjustments and record-keeping.