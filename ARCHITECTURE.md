# Sales Management Application Architecture

## 1. System Overview

### Application Name: BRO Manajemen Sales Management System
A comprehensive sales management application for food and beverage products with production cost tracking, financial journaling, and responsive UI.

### Technology Stack
- **Backend**: Laravel 12.x
- **Frontend**: Blade Templates + Alpine.js
- **CSS Framework**: Tailwind CSS 4.0
- **Database**: PostgreSQL
- **Authentication**: Laravel Sanctum
- **State Management**: Alpine.js Store
- **Charts**: Chart.js
- **Icons**: Heroicons

## 2. Database Schema Design

### Core Tables

#### 2.1 Users & Authentication
```sql
-- users (extended from Laravel default)
- id (bigint, primary key)
- name (string)
- email (string, unique)
- password (string)
- role (enum: admin)
- phone (string, nullable)
- address (text, nullable)
- avatar (string, nullable)
- theme_preference (enum: light, dark, system)
- is_active (boolean, default: true)
- last_login_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

-- activity_logs
- id (bigint, primary key)
- user_id (bigint, foreign key)
- action (string)
- model_type (string)
- model_id (bigint)
- old_values (json, nullable)
- new_values (json, nullable)
- ip_address (string)
- user_agent (text)
- created_at (timestamp)
```

#### 2.2 Product Management
```sql
-- categories
- id (bigint, primary key)
- name (string)
- slug (string, unique)
- description (text, nullable)
- icon (string, nullable)
- parent_id (bigint, nullable, foreign key to categories)
- sort_order (integer, default: 0)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

-- products
- id (bigint, primary key)
- category_id (bigint, foreign key)
- sku (string, unique)
- name (string)
- description (text, nullable)
- type (enum: food, beverage)
- unit (string) -- pcs, kg, liter, etc
- image (string, nullable)
- base_price (decimal 15,2)
- selling_price (decimal 15,2)
- min_stock (integer, default: 0)
- current_stock (integer, default: 0)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

-- product_ingredients
- id (bigint, primary key)
- product_id (bigint, foreign key)
- name (string)
- quantity (decimal 10,3)
- unit (string)
- cost_per_unit (decimal 15,2)
- supplier (string, nullable)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

-- production_costs
- id (bigint, primary key)
- product_id (bigint, foreign key)
- ingredient_cost (decimal 15,2)
- labor_cost (decimal 15,2)
- overhead_cost (decimal 15,2)
- total_cost (decimal 15,2)
- batch_size (integer)
- cost_per_unit (decimal 15,2)
- calculated_at (timestamp)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

-- packaging_costs
- id (bigint, primary key)
- product_id (bigint, foreign key)
- packaging_type (string)
- material_cost (decimal 15,2)
- label_cost (decimal 15,2)
- other_cost (decimal 15,2)
- total_cost (decimal 15,2)
- cost_per_unit (decimal 15,2)
- supplier (string, nullable)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

#### 2.3 Sales & Orders
```sql
-- customers
- id (bigint, primary key)
- code (string, unique)
- name (string)
- email (string, nullable)
- phone (string)
- address (text)
- city (string)
- postal_code (string, nullable)
- type (enum: individual, business)
- tax_id (string, nullable)
- credit_limit (decimal 15,2, default: 0)
- outstanding_balance (decimal 15,2, default: 0)
- notes (text, nullable)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

-- orders
- id (bigint, primary key)
- order_number (string, unique)
- customer_id (bigint, foreign key)
- user_id (bigint, foreign key) -- staff who created
- order_date (date)
- delivery_date (date, nullable)
- status (enum: draft, confirmed, processing, shipped, delivered, cancelled)
- payment_status (enum: unpaid, partial, paid)
- subtotal (decimal 15,2)
- discount_amount (decimal 15,2, default: 0)
- tax_amount (decimal 15,2, default: 0)
- shipping_cost (decimal 15,2, default: 0)
- total_amount (decimal 15,2)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

-- order_items
- id (bigint, primary key)
- order_id (bigint, foreign key)
- product_id (bigint, foreign key)
- quantity (integer)
- unit_price (decimal 15,2)
- production_cost (decimal 15,2)
- packaging_cost (decimal 15,2)
- discount_amount (decimal 15,2, default: 0)
- total_price (decimal 15,2)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

-- shipping_costs
- id (bigint, primary key)
- order_id (bigint, foreign key)
- shipping_method (string)
- courier (string, nullable)
- tracking_number (string, nullable)
- weight (decimal 10,2) -- in kg
- distance (decimal 10,2, nullable) -- in km
- base_cost (decimal 15,2)
- additional_cost (decimal 15,2, default: 0)
- total_cost (decimal 15,2)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

#### 2.4 Financial Management
```sql
-- accounts
- id (bigint, primary key)
- code (string, unique)
- name (string)
- type (enum: asset, liability, equity, revenue, expense)
- parent_id (bigint, nullable, foreign key to accounts)
- balance (decimal 15,2, default: 0)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

-- journal_entries
- id (bigint, primary key)
- entry_number (string, unique)
- entry_date (date)
- description (string)
- reference_type (string, nullable) -- order, purchase, etc
- reference_id (bigint, nullable)
- user_id (bigint, foreign key)
- is_posted (boolean, default: false)
- created_at (timestamp)
- updated_at (timestamp)

-- journal_entry_lines
- id (bigint, primary key)
- journal_entry_id (bigint, foreign key)
- account_id (bigint, foreign key)
- debit (decimal 15,2, default: 0)
- credit (decimal 15,2, default: 0)
- description (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)

-- cash_flows
- id (bigint, primary key)
- transaction_date (date)
- type (enum: income, expense)
- category (string)
- subcategory (string, nullable)
- amount (decimal 15,2)
- payment_method (enum: cash, bank_transfer, credit_card, other)
- reference_type (string, nullable)
- reference_id (bigint, nullable)
- description (text)
- user_id (bigint, foreign key)
- is_recurring (boolean, default: false)
- created_at (timestamp)
- updated_at (timestamp)

-- expenses
- id (bigint, primary key)
- expense_number (string, unique)
- expense_date (date)
- category (string)
- vendor (string, nullable)
- amount (decimal 15,2)
- tax_amount (decimal 15,2, default: 0)
- total_amount (decimal 15,2)
- payment_method (string)
- payment_status (enum: unpaid, paid)
- receipt_url (string, nullable)
- notes (text, nullable)
- user_id (bigint, foreign key)
- approved_by (bigint, nullable, foreign key to users)
- approved_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

#### 2.5 Inventory Management
```sql
-- stock_movements
- id (bigint, primary key)
- product_id (bigint, foreign key)
- type (enum: in, out, adjustment)
- quantity (integer)
- unit_cost (decimal 15,2, nullable)
- reference_type (string, nullable)
- reference_id (bigint, nullable)
- reason (string)
- notes (text, nullable)
- user_id (bigint, foreign key)
- created_at (timestamp)
- updated_at (timestamp)

-- suppliers
- id (bigint, primary key)
- code (string, unique)
- name (string)
- contact_person (string, nullable)
- email (string, nullable)
- phone (string)
- address (text, nullable)
- payment_terms (string, nullable)
- notes (text, nullable)
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

-- purchases
- id (bigint, primary key)
- purchase_number (string, unique)
- supplier_id (bigint, foreign key)
- purchase_date (date)
- due_date (date, nullable)
- status (enum: draft, ordered, received, cancelled)
- payment_status (enum: unpaid, partial, paid)
- total_amount (decimal 15,2)
- notes (text, nullable)
- user_id (bigint, foreign key)
- created_at (timestamp)
- updated_at (timestamp)

-- purchase_items
- id (bigint, primary key)
- purchase_id (bigint, foreign key)
- item_name (string)
- quantity (decimal 10,3)
- unit (string)
- unit_price (decimal 15,2)
- total_price (decimal 15,2)
- received_quantity (decimal 10,3, default: 0)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## 3. Module Architecture

### 3.1 Authentication & Authorization Module
- Single role system (Admin only)
- Simplified access control
- Activity logging for audit trail
- Session management
- Two-factor authentication (optional)

### 3.2 Product Management Module
- Product CRUD with categories
- Ingredient management for production cost
- Packaging cost configuration
- Batch production tracking
- Product image management
- SKU generation

### 3.3 Sales & Order Module
- Customer management
- Order creation and tracking
- Invoice generation
- Payment tracking
- Shipping cost calculation
- Order status workflow

### 3.4 Financial Module
- Double-entry bookkeeping
- Automatic journal entries from transactions
- Cash flow tracking
- Income statement generation
- Balance sheet
- Financial reports and analytics

### 3.5 Inventory Module
- Real-time stock tracking
- Stock movement history
- Low stock alerts
- Purchase order management
- Supplier management
- Stock valuation (FIFO/LIFO/Average)

### 3.6 Dashboard & Analytics Module
- Sales analytics
- Profit margin analysis
- Product performance
- Customer insights
- Financial summaries
- Inventory status

## 4. UI/UX Design Guidelines

### 4.1 Theme Configuration
```css
/* Light Theme Colors */
--primary: #FFC107 (Amber/Yellow)
--primary-dark: #FFA000
--background: #FFFFFF
--surface: #F5F5F5
--text-primary: #212121
--text-secondary: #757575

/* Dark Theme Colors */
--primary: #FFD54F
--primary-dark: #FFC107
--background: #121212
--surface: #1E1E1E
--text-primary: #FFFFFF
--text-secondary: #B0B0B0
```

### 4.2 Responsive Design Breakpoints
- Mobile: < 640px
- Tablet: 640px - 1024px
- Desktop: > 1024px

### 4.3 Component Library
- Navigation: Responsive sidebar/navbar
- Forms: Validated input components
- Tables: Sortable, filterable data tables
- Cards: Information display cards
- Modals: Confirmation and form dialogs
- Charts: Interactive data visualizations
- Alerts: Toast notifications

## 5. API Architecture

### 5.1 RESTful API Endpoints
```
Authentication:
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/refresh
GET    /api/auth/user

Products:
GET    /api/products
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
DELETE /api/products/{id}
GET    /api/products/{id}/costs
POST   /api/products/{id}/calculate-cost

Orders:
GET    /api/orders
POST   /api/orders
GET    /api/orders/{id}
PUT    /api/orders/{id}
DELETE /api/orders/{id}
POST   /api/orders/{id}/confirm
POST   /api/orders/{id}/ship
POST   /api/orders/{id}/deliver

Financial:
GET    /api/journal-entries
POST   /api/journal-entries
GET    /api/cash-flows
POST   /api/cash-flows
GET    /api/reports/income-statement
GET    /api/reports/balance-sheet
GET    /api/reports/cash-flow-statement

Dashboard:
GET    /api/dashboard/summary
GET    /api/dashboard/sales-chart
GET    /api/dashboard/top-products
GET    /api/dashboard/recent-orders
```

### 5.2 API Response Format
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {},
  "meta": {
    "current_page": 1,
    "total_pages": 10,
    "total_records": 100
  }
}
```

## 6. Security Considerations

### 6.1 Authentication
- Laravel Sanctum for API authentication
- Session-based authentication for web
- CSRF protection
- Rate limiting

### 6.2 Authorization
- Admin-only access control
- Simplified permission system
- Basic policy classes for data protection

### 6.3 Data Protection
- Input validation and sanitization
- SQL injection prevention (Eloquent ORM)
- XSS protection
- Encrypted sensitive data
- Regular backups

## 7. Performance Optimization

### 7.1 Database
- Proper indexing strategy
- Query optimization
- Database connection pooling
- Caching frequently accessed data

### 7.2 Application
- Redis caching
- Queue jobs for heavy operations
- Image optimization
- Lazy loading
- Code splitting

### 7.3 Frontend
- Minification and compression
- CDN for static assets
- Browser caching
- Progressive Web App (PWA) features

## 8. Development Workflow

### 8.1 Git Branch Strategy
- main: Production-ready code
- develop: Development branch
- feature/*: Feature branches
- hotfix/*: Emergency fixes

### 8.2 Testing Strategy
- Unit tests for models and services
- Feature tests for API endpoints
- Browser tests for UI (Dusk)
- Test coverage > 80%

### 8.3 Deployment
- Environment: Production, Staging, Development
- CI/CD pipeline with GitHub Actions
- Database migrations
- Zero-downtime deployment

## 9. Monitoring & Logging

### 9.1 Application Monitoring
- Error tracking (Sentry/Bugsnag)
- Performance monitoring
- Uptime monitoring
- User activity tracking

### 9.2 Logging
- Application logs (Laravel Log)
- Access logs
- Error logs
- Audit logs for sensitive operations

## 10. Documentation

### 10.1 Technical Documentation
- API documentation (OpenAPI/Swagger)
- Database schema documentation
- Code documentation (PHPDoc)
- Architecture decision records (ADRs)

### 10.2 User Documentation
- User manual
- Admin guide
- API integration guide
- FAQ section