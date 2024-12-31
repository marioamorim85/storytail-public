<div class="sidebar-toggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
</div>

<div class="sidebar">
    <div class="sidebar-header"></div>

    <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin') ? 'active' : '' }}"
               onclick="window.location.href='{{ route('admin.dashboard') }}'">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </li>

        <!-- Books -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('admin/books*') ? 'active' : '' }}" href="#" id="booksSubmenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-book"></i> Books
            </a>
            <ul class="dropdown-menu {{ Request::is('admin/books*') ? 'show' : '' }}" aria-labelledby="booksSubmenu">
                <li><a class="dropdown-item {{ Request::is('admin/books') ? 'active' : '' }}" href="{{ route('admin.books.list') }}">All Books</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/books/create') ? 'active' : '' }}" href="{{ route('admin.books.create') }}">Add New</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/books/tags') ? 'active' : '' }}" href="{{ route('admin.books.tags.list') }}">Tags</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/books/age-groups') ? 'active' : '' }}" href="{{ route('admin.books.age-groups.list') }}">Age Groups</a></li>
            </ul>
        </li>

        <!-- Activities -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('admin/activities*') ? 'active' : '' }}" href="#" id="activitiesSubmenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-calendar"></i> Activities
            </a>
            <ul class="dropdown-menu {{ Request::is('admin/activities*') ? 'show' : '' }}" aria-labelledby="activitiesSubmenu">
                <li><a class="dropdown-item {{ Request::is('admin/activities') ? 'active' : '' }}" href="{{ route('admin.activities.list') }}">All Activities</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/activities/create') ? 'active' : '' }}" href="{{ route('admin.activities.create') }}">Add New</a></li>
            </ul>
        </li>

        <!-- Authors -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('admin/authors*') ? 'active' : '' }}" href="#" id="authorsSubmenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person"></i> Authors
            </a>
            <ul class="dropdown-menu {{ Request::is('admin/authors*') ? 'show' : '' }}" aria-labelledby="authorsSubmenu">
                <li><a class="dropdown-item {{ Request::is('admin/authors*') ? 'active' : '' }}" href="{{ route('admin.authors.list') }}">All Authors</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/authors/create') ? 'active' : '' }}" href="{{ route('admin.authors.create') }}">Add New</a></li>
            </ul>
        </li>

        <!-- Users -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('admin/users', 'admin/users/*') ? 'active' : '' }}" href="#" id="usersSubmenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-people"></i> Users
            </a>
            <ul class="dropdown-menu {{ Request::is('admin/users', 'admin/users/*') ? 'show' : '' }}" aria-labelledby="usersSubmenu">
                <li>
                    <a class="dropdown-item {{ Request::is('admin/users') ? 'active' : '' }}" href="{{ route('admin.users.list') }}">
                        All Users
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('admin/users/user-types') ? 'active' : '' }}" href="{{ route('admin.users.user-types.list') }}">
                        User Types
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('admin/users/subscriptions') ? 'active' : '' }}" href="{{ route('admin.users.subscriptions.list') }}">
                        Subscriptions
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('admin/users/plans') ? 'active' : '' }}" href="{{ route('admin.users.plans.list') }}">
                        Plans
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('admin/users/comments') ? 'active' : '' }}" href="{{ route('admin.users.comments.list') }}">
                        Comments
                    </a>
                </li>
            </ul>
        </li>


        <!-- Approvals -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('admin/approvals*') ? 'active' : '' }}" href="#" id="approvalsSubmenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-check-circle"></i> Approvals
            </a>
            <ul class="dropdown-menu {{ Request::is('admin/approvals*') ? 'show' : '' }}" aria-labelledby="approvalsSubmenu">
                <li><a class="dropdown-item {{ Request::is('admin/approvals/comments') ? 'active' : '' }}" href="{{ route('admin.approvals.comments') }}">Comment Moderation</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/approvals/subscriptions') ? 'active' : '' }}" href="{{ route('admin.approvals.subscriptions') }}">Subscription Requests</a></li>
                <li><a class="dropdown-item {{ Request::is('admin/approvals/history') ? 'active' : '' }}" href="{{ route('admin.approvals.history') }}">Approval History</a></li>
            </ul>
        </li>

        <!-- Reports -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('admin/reports*') ? 'active' : '' }}" href="#" id="reportsSubmenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-graph-up"></i> Reports
            </a>
            <ul class="dropdown-menu {{ Request::is('admin/reports*') ? 'show' : '' }}" aria-labelledby="reportsSubmenu">
                <li>
                    <a class="dropdown-item {{ Request::is('admin/reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                        All Reports
                    </a>
                </li>
            </ul>
        </li>

        <!-- Return to Site -->
        <li class="nav-item">
            <a class="nav-link" onclick="window.location.href='{{ route('home') }}'">
                <i class="bi bi-box-arrow-in-left"></i> Return to Site
            </a>
        </li>
    </ul>
</div>


