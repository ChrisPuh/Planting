#!/bin/bash

# Repository Info
REPO="ChrisPuh/Planting"

echo "🚀 Creating missing Phase 2-4 issues and linking them..."

# Get issue numbers for epics (to reference them)
EPIC_1_NUMBER=$(gh issue list --label "type:epic" --search "Event Sourcing Foundation" --json number --jq '.[0].number')
EPIC_2_NUMBER=$(gh issue list --label "type:epic" --search "Timeline & UI Integration" --json number --jq '.[0].number')
EPIC_3_NUMBER=$(gh issue list --label "type:epic" --search "Request Management System" --json number --jq '.[0].number')
EPIC_4_NUMBER=$(gh issue list --label "type:epic" --search "Performance & Polish" --json number --jq '.[0].number')

echo "Epic numbers found: E1:#$EPIC_1_NUMBER, E2:#$EPIC_2_NUMBER, E3:#$EPIC_3_NUMBER, E4:#$EPIC_4_NUMBER"

# ==== PHASE 2 TASKS (Timeline & UI) ====
echo "⏰ Creating Phase 2 tasks..."

# Task 2.1: Timeline Component
gh issue create \
  --title "⏰ Task: Create Timeline Livewire Component" \
  --label "type:task,priority:high,domain:timeline" \
  --body "$(cat <<EOF
## Description
Build interactive timeline component showing plant lifecycle events.

## Epic
Related to #$EPIC_2_NUMBER - Timeline & UI Integration

## Tasks
- [ ] Create \`Timeline\` Livewire component
- [ ] Implement real-time event updates via Livewire events
- [ ] Add admin vs user permission views
- [ ] Create timeline blade template with Flux UI
- [ ] Add loading states and animations
- [ ] Test with 100+ timeline events

## Acceptance Criteria
- Shows all plant events chronologically
- Updates in real-time when events occur
- Different views for admin/user permissions
- Performant with 100+ events
- Mobile responsive design
- Uses existing TimelineEvent ValueObject

## Files to Create
- \`app/Http/Livewire/Plants/Timeline.php\`
- \`resources/views/livewire/plants/timeline.blade.php\`

## Dependencies
- Epic 1 must be completed (Event Sourcing Foundation)
- PlantTimelineProjection must exist

## Reference
See Implementation Plan for complete component code.

## Priority
🟠 High

## Estimate
8 hours
EOF
)"

# Task 2.2: Timeline Mapper Enhancement
gh issue create \
  --title "🗺️ Task: Enhance Timeline Mapper for Real Data" \
  --label "type:task,priority:high,domain:timeline" \
  --body "$(cat <<EOF
## Description
Update PlantTimelineMapper to work with actual Event Store data instead of dummy data.

## Epic
Related to #$EPIC_2_NUMBER - Timeline & UI Integration

## Tasks
- [ ] Update \`mapTimelineEventsFromDatabase\` method
- [ ] Remove dummy data generation methods
- [ ] Add error handling for malformed events
- [ ] Optimize mapping performance for large datasets
- [ ] Add support for event details and metadata
- [ ] Test with real Event Store data

## Acceptance Criteria
- Maps real Event Store data correctly
- Handles all event types properly
- Performance optimized for large datasets
- Graceful error handling for corrupted events
- Maintains compatibility with existing TimelineEvent ValueObject

## Files to Modify
- \`app/Domains/Admin/Plants/Mappers/PlantTimelineMapper.php\`

## Dependencies
- Task 1.4 (Projectors) must be completed
- Real events must be flowing into projections

## Reference
See Implementation Plan for mapper updates.

## Priority
🟠 High

## Estimate
4 hours
EOF
)"

# Task 2.3: Real-time UI Updates
gh issue create \
  --title "🔄 Task: Implement Real-time Timeline Updates" \
  --label "type:task,priority:high,domain:timeline" \
  --body "$(cat <<EOF
## Description
Implement real-time updates for timeline when new events occur.

## Epic
Related to #$EPIC_2_NUMBER - Timeline & UI Integration

## Tasks
- [ ] Add Livewire event listeners to Timeline component
- [ ] Dispatch events when plants are updated/created/deleted
- [ ] Implement optimistic UI updates
- [ ] Add loading/success states for user feedback
- [ ] Test real-time updates across multiple browser tabs
- [ ] Add debouncing for rapid fire events

## Acceptance Criteria
- Timeline updates immediately when events occur
- No page refresh needed
- Works across multiple browser sessions
- Smooth animations for new timeline entries
- Handles concurrent updates gracefully

## Files to Modify
- \`app/Http/Livewire/Plants/Timeline.php\`
- Timeline blade template
- Plant CRUD operations (to dispatch events)

## Dependencies
- Task 2.1 (Timeline Component) must be completed

## Priority
🟠 High

## Estimate
6 hours
EOF
)"

# ==== PHASE 3 TASKS (Request Management) ====
echo "📋 Creating Phase 3 tasks..."

# Task 3.1: Request Services
gh issue create \
  --title "🔧 Task: Create Request Management Services" \
  --label "type:task,priority:high,domain:requests" \
  --body "$(cat <<EOF
## Description
Implement services for handling user requests and admin approvals.

## Epic
Related to #$EPIC_3_NUMBER - Request Management System

## Tasks
- [ ] Create \`RequestService\` with CRUD operations
- [ ] Implement approval/rejection workflow
- [ ] Add request validation logic
- [ ] Create notification system integration
- [ ] Add request statistics and reporting
- [ ] Integrate with PlantService for approved requests

## Acceptance Criteria
- Users can submit plant requests
- Admins can approve/reject requests
- Approved requests create plants automatically via Event Sourcing
- Email notifications sent on status changes
- Complete audit trail maintained
- Request statistics available

## Files to Create
- \`app/Domains/RequestManagement/Services/RequestService.php\`

## Dependencies
- Epic 1 (Event Sourcing Foundation) must be completed
- RequestAggregate must exist

## Reference
See Implementation Plan for complete service code.

## Priority
🟠 High

## Estimate
10 hours
EOF
)"

# Task 3.2: Admin Dashboard
gh issue create \
  --title "👨‍💼 Task: Create Admin Request Dashboard" \
  --label "type:task,priority:high,domain:admin" \
  --body "$(cat <<EOF
## Description
Build admin interface for managing pending requests.

## Epic
Related to #$EPIC_3_NUMBER - Request Management System

## Tasks
- [ ] Create \`RequestDashboard\` Livewire component
- [ ] Add filtering and sorting capabilities
- [ ] Implement bulk operations (approve/reject multiple)
- [ ] Add request statistics overview
- [ ] Create approval/rejection modals with Flux UI
- [ ] Add real-time notifications for new requests

## Acceptance Criteria
- Shows all pending requests in organized table
- Bulk approve/reject functionality
- Real-time updates when new requests arrive
- Statistics dashboard with metrics
- Mobile-friendly interface
- Integration with existing admin layout

## Files to Create
- \`app/Http/Livewire/Admin/RequestDashboard.php\`
- \`resources/views/livewire/admin/request-dashboard.blade.php\`

## Dependencies
- Task 3.1 (Request Services) must be completed
- RequestQueueProjection must exist

## Priority
🟠 High

## Estimate
12 hours
EOF
)"

# Task 3.3: User Request Form
gh issue create \
  --title "📝 Task: Create User Request Form" \
  --label "type:task,priority:high,domain:requests" \
  --body "$(cat <<EOF
## Description
Build user interface for submitting plant requests and contributions.

## Epic
Related to #$EPIC_3_NUMBER - Request Management System

## Tasks
- [ ] Create \`PlantRequest\` Livewire component
- [ ] Add form validation and error handling
- [ ] Implement image upload functionality
- [ ] Add duplicate plant detection
- [ ] Create request status tracking page
- [ ] Add contribution requests for existing plants

## Acceptance Criteria
- Users can submit new plant requests
- Users can suggest improvements to existing plants
- Form validates all required fields
- Duplicate detection prevents spam
- Users can track request status
- Integration with existing user interface

## Files to Create
- \`app/Http/Livewire/User/PlantRequest.php\`
- \`resources/views/livewire/user/plant-request.blade.php\`
- \`app/Http/Livewire/User/MyRequests.php\`

## Dependencies
- Task 3.1 (Request Services) must be completed

## Priority
🟠 High

## Estimate
10 hours
EOF
)"

# ==== PHASE 4 TASKS (Polish & Performance) ====
echo "✨ Creating Phase 4 tasks..."

# Task 4.1: Testing Suite
gh issue create \
  --title "🧪 Task: Create Comprehensive Test Suite" \
  --label "type:task,priority:medium,domain:infrastructure" \
  --body "$(cat <<EOF
## Description
Build complete testing coverage for Event Sourcing functionality.

## Epic
Related to #$EPIC_4_NUMBER - Performance & Polish

## Tasks
- [ ] Create Feature tests for plant lifecycle
- [ ] Create Integration tests for request workflow
- [ ] Add Unit tests for aggregates and projectors
- [ ] Test event replay functionality
- [ ] Performance testing for large datasets
- [ ] Test timeline component behavior
- [ ] Test admin dashboard functionality

## Acceptance Criteria
- 90%+ code coverage
- All critical paths tested
- Event replay works correctly
- Performance benchmarks passed
- CI/CD pipeline ready
- Tests run fast (<30 seconds)

## Files to Create
- \`tests/Feature/PlantEventSourcingTest.php\`
- \`tests/Feature/RequestWorkflowTest.php\`
- \`tests/Integration/TimelineTest.php\`
- \`tests/Unit/PlantAggregateTest.php\`
- \`tests/Unit/RequestAggregateTest.php\`

## Dependencies
- All previous epics must be mostly completed

## Priority
🟡 Medium

## Estimate
16 hours
EOF
)"

# Task 4.2: Performance Optimization
gh issue create \
  --title "⚡ Task: Performance Optimization" \
  --label "type:task,priority:medium,domain:infrastructure" \
  --body "$(cat <<EOF
## Description
Optimize application performance for production scale.

## Epic
Related to #$EPIC_4_NUMBER - Performance & Polish

## Tasks
- [ ] Optimize database queries and indexes
- [ ] Implement caching for frequent projections
- [ ] Add event store snapshots for large aggregates
- [ ] Optimize timeline rendering for 1000+ events
- [ ] Add pagination for admin dashboard
- [ ] Implement lazy loading for components

## Acceptance Criteria
- Timeline loads in <100ms with 1000+ events
- Admin dashboard handles 100+ pending requests smoothly
- Database queries are optimized with proper indexes
- Memory usage is reasonable
- No N+1 query problems

## Files to Modify
- Database migrations (add indexes)
- Projector classes (add caching)
- Livewire components (optimize queries)

## Dependencies
- All core functionality must be implemented

## Priority
🟡 Medium

## Estimate
12 hours
EOF
)"

# Task 4.3: Documentation & Deployment
gh issue create \
  --title "📚 Task: Documentation & Deployment Prep" \
  --label "type:task,priority:medium,domain:docs" \
  --body "$(cat <<EOF
## Description
Complete documentation and prepare for production deployment.

## Epic
Related to #$EPIC_4_NUMBER - Performance & Polish

## Tasks
- [ ] Write API documentation
- [ ] Create deployment guide
- [ ] Document Event Sourcing architecture
- [ ] Create user manual for request system
- [ ] Write admin guide for managing requests
- [ ] Setup monitoring and logging

## Acceptance Criteria
- Complete documentation for developers
- Deployment guide with step-by-step instructions
- User and admin guides available
- Monitoring setup for production
- Error logging and debugging tools ready

## Files to Create
- \`README.md\` (comprehensive)
- \`docs/deployment.md\`
- \`docs/architecture.md\`
- \`docs/user-guide.md\`
- \`docs/admin-guide.md\`

## Dependencies
- All functionality must be completed and tested

## Priority
🟡 Medium

## Estimate
8 hours
EOF
)"

# Update existing Phase 1 tasks to reference Epic 1
echo "🔗 Updating Phase 1 tasks to reference Epic 1..."

# We'll add comments to link them (GitHub doesn't allow editing issue body via CLI easily)
gh issue comment 6 --body "📋 **Epic Reference:** This task is part of #$EPIC_1_NUMBER - Event Sourcing Foundation Setup"
gh issue comment 7 --body "📋 **Epic Reference:** This task is part of #$EPIC_1_NUMBER - Event Sourcing Foundation Setup"
gh issue comment 8 --body "📋 **Epic Reference:** This task is part of #$EPIC_1_NUMBER - Event Sourcing Foundation Setup"
gh issue comment 9 --body "📋 **Epic Reference:** This task is part of #$EPIC_1_NUMBER - Event Sourcing Foundation Setup"
gh issue comment 10 --body "📋 **Epic Reference:** This task is part of #$EPIC_1_NUMBER - Event Sourcing Foundation Setup"

echo ""
echo "🎉 Complete issue set created!"
echo ""
echo "📊 Summary:"
echo "- 4 Epic Issues (#2, #3, #4, #5)"
echo "- 5 Phase 1 Tasks (#6-10) - Critical priority"
echo "- 3 Phase 2 Tasks (#11-13) - High priority"
echo "- 3 Phase 3 Tasks (#14-16) - High priority"
echo "- 3 Phase 4 Tasks (#17-19) - Medium priority"
echo ""
echo "Total: 18 Issues across 4 phases"
echo "Estimated total work: ~120 hours (3 months part-time)"
echo ""
echo "🚀 Next steps:"
echo "1. Create GitHub Project Board"
echo "2. Add all issues to the board"
echo "3. Move Task #6 (Database Migrations) to 'Ready'"
echo "4. Start coding! 💪"
