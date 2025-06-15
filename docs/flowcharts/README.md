# UCUA Safety Reporting System - PlantUML Flowcharts

This directory contains comprehensive PlantUML flowcharts documenting the key workflows and user interactions in the UCUA Safety Reporting System.

## Flowchart Files

### 1. User Authentication Flow (`1-user-authentication-flow.puml`)
Documents the complete login process for all four user types:
- **Regular Users** (`/login`) - Email verification required, OTP verification, web guard
- **Admin** (`/admin/login`) - Skip email verification, OTP verification, admin role check
- **UCUA Officer** (`/ucua/login`) - Skip email verification, OTP verification, ucua_officer role check  
- **Department** (`/department/login`) - Department guard, OTP verification

**Key Features:**
- 6-character OTP with 5-minute expiration
- Role-based redirects after successful authentication
- Guard assignment (web vs department)
- Email verification requirements

### 2. Report Submission Workflow (`2-report-submission-workflow.puml`)
Shows the complete flow from incident report creation to final resolution:
- Form validation and auto-population of employee ID
- Anonymous reporting options
- Status transitions: `pending` → `in_progress` → `resolved`
- Department assignment and review process
- Privacy protections and confidentiality measures

**Key Features:**
- Separate fields for unsafe_conditions and unsafe_acts
- Past date/time restrictions
- RPT-XXX ID format generation
- Anonymous reporting with user_id tracking

### 3. Warning Letter System Flow (`3-warning-letter-system-flow.puml`)
Illustrates the workflow from UCUA suggestion to admin approval to email delivery:
- UCUA Officer suggestion process
- Admin approval/rejection workflow
- Internal vs external violator handling
- Email delivery and manual delivery tracking
- Escalation rules for repeat violations

**Key Features:**
- Only applies to unsafe_act violations
- WL-XXX ID format generation
- Three severity levels: minor, moderate, severe
- Escalation: 3 warnings in 3 months triggers escalation

### 4. Department Assignment Process (`4-department-assignment-process.puml`)
Documents how UCUA officers assign reports to departments:
- Report review and selection process
- Assignment form with deadline and remarks
- Department investigation and violator identification
- Confidential remarks system
- Resolution process with notes

**Key Features:**
- Future date deadline requirements
- Assignment context and remarks
- Violator identification for unsafe_act reports
- Confidential department remarks

### 5. User Role Permissions Matrix (`5-user-role-permissions-matrix.puml`)
Visual matrix showing what actions each user type can perform:
- **Regular Users**: Submit reports, view own reports, anonymous reporting
- **Departments**: View assigned reports, add remarks, resolve reports, identify violators
- **UCUA Officers**: View all reports, assign departments, suggest warnings, send reminders
- **Admin**: Full system access, user management, warning approval, system settings

**Permission Levels:**
- ✅ **ALLOWED**: Full access to feature
- ❌ **DENIED**: No access to feature
- ⚠️ **LIMITED**: Restricted access (own data, assigned data, etc.)

### 6. System Overview (`6-system-overview.puml`)
High-level diagram showing how all workflows interconnect:
- Authentication layer with all user types
- Report lifecycle from submission to resolution
- Warning letter system integration
- Data flow and privacy controls
- Status tracking and ID generation

### 7. System Architecture (`7-system-architecture.puml`)
Technical architecture diagram showing the complete system structure:
- **Frontend Layer**: Blade templates, Vue.js components, Bootstrap/Tailwind CSS
- **Backend Layer**: Laravel framework, controllers, middleware, services
- **Database Layer**: MySQL with Eloquent ORM, model relationships
- **Authentication Layer**: Multi-guard system, Spatie permissions, OTP verification
- **External Services**: Email delivery, file storage, notifications
- **Security Layer**: CSRF protection, middleware stack, role-based access

### 8. Database Schema (`8-database-schema.puml`)
Comprehensive database schema showing all tables and relationships:
- **Core Tables**: users, departments, reports, warnings, remarks
- **Authentication Tables**: roles, permissions, model relationships
- **System Tables**: escalation rules, warning templates, admin settings
- **Audit Tables**: report status history, escalations
- **Relationships**: Foreign keys, constraints, indexes
- **Data Types**: Field specifications, enums, constraints

### 9. Deployment Architecture (`9-deployment-architecture.puml`)
Production deployment architecture with infrastructure components:
- **Client Layer**: Web browsers, user access patterns
- **Web Layer**: Nginx/Apache, SSL termination, load balancing
- **Application Layer**: PHP-FPM, Laravel instances, file storage, caching
- **Database Layer**: MySQL primary/backup, automated backups
- **External Services**: SMTP, monitoring, security tools
- **DevOps**: CI/CD pipeline, version control, environment management

## How to Use These Flowcharts

### Viewing PlantUML Diagrams

1. **Online PlantUML Editor**: 
   - Visit [plantuml.com/plantuml](http://www.plantuml.com/plantuml)
   - Copy and paste the content of any `.puml` file
   - View the rendered diagram

2. **VS Code Extension**:
   - Install "PlantUML" extension
   - Open any `.puml` file
   - Use `Alt+D` to preview the diagram

3. **Local PlantUML Installation**:
   ```bash
   # Install PlantUML (requires Java)
   java -jar plantuml.jar filename.puml
   ```

### Integration with Documentation

These flowcharts can be:
- Embedded in project documentation
- Used for training new team members
- Referenced during system maintenance
- Updated as the system evolves

## Technical Architecture Overview

### Frontend Architecture
- **Framework**: Laravel Blade templates with Vue.js components
- **CSS**: Bootstrap 4.5.2 + Tailwind CSS for utility classes
- **Build Tools**: Vite for asset compilation and hot reloading
- **JavaScript**: Axios for HTTP requests, jQuery for DOM manipulation
- **Icons**: Font Awesome 6.0 for consistent iconography

### Backend Architecture
- **Framework**: Laravel 11.x with PHP 8.1+
- **Pattern**: MVC with Service Layer for business logic
- **Authentication**: Multi-guard system (web/department guards)
- **Authorization**: Spatie Laravel Permission for role-based access
- **ORM**: Eloquent with model relationships and policies
- **Validation**: Form Request classes with custom rules

### Database Architecture
- **Engine**: MySQL 8.0+ with InnoDB storage engine
- **Design**: Normalized schema with proper foreign key constraints
- **Indexing**: Optimized indexes for query performance
- **Audit Trail**: Status history tracking for all major entities
- **Backup**: Automated daily backups with point-in-time recovery

### Security Architecture
- **Authentication**: Multi-factor with OTP verification
- **Authorization**: Role-based permissions with guard separation
- **Data Protection**: CSRF tokens, input validation, SQL injection prevention
- **Privacy**: Anonymous reporting, confidential remarks, data encryption
- **Session Management**: Secure session handling with proper timeouts

### Deployment Architecture
- **Web Server**: Nginx/Apache with SSL termination and static file serving
- **Application Server**: PHP-FPM with multiple worker processes
- **Caching**: Redis for session storage and application caching
- **File Storage**: Local filesystem with backup to external storage
- **Monitoring**: Application performance monitoring and error tracking
- **CI/CD**: Automated testing and deployment pipeline

## Key System Features

### ID Formats
- **Reports**: RPT-001, RPT-002, RPT-003...
- **Warning Letters**: WL-001, WL-002, WL-003...
- **Recommendation Letters**: RL-001, RL-002, RL-003...

### Privacy Features
- Anonymous reporting hides user identity in UI while maintaining internal tracking
- Department remarks are confidential from regular users
- Resolution notes are visible to report submitters
- Violator identification only applies to unsafe_act violations

### Authentication Guards
- **Web Guard**: Regular users, UCUA officers, Admin (uses users table)
- **Department Guard**: Department users only (uses departments table)

### Role Hierarchy
- **Admin**: Full system access, user management, warning approval
- **UCUA Officer**: Report management, department assignment, warning suggestions
- **Department**: Assigned report handling, investigation, resolution
- **Regular User**: Report submission, own report viewing

## Maintenance

When updating the system:
1. Review relevant flowcharts for impact
2. Update diagrams to reflect changes
3. Validate workflows still match implementation
4. Update this README if new diagrams are added

These flowcharts serve as living documentation and should be kept in sync with the actual system implementation.
