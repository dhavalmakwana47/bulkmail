<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class -->
    <li class="nav-item">
      <a href="{{ route('home') }}" class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>
    
    <!-- Bulk Mail System -->
    <li class="nav-header">BULK MAIL</li>
    
    @if (permissionCheck())
    <li class="nav-item">
      <a href="{{ route('corporate-debtors.index') }}" class="nav-link {{ Request::routeIs('corporate-debtors.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Corporate Debtors</p>
      </a>
    </li>
    @endif
    
    <li class="nav-item">
      <a href="{{ route('contacts.index') }}" class="nav-link {{ Request::routeIs('contacts.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-address-book"></i>
        <p>Contacts</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('mail-configurations.index') }}" class="nav-link {{ Request::routeIs('mail-configurations.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-envelope"></i>
        <p>Mail Configurations</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('debtor-attachments.index') }}" class="nav-link {{ Request::routeIs('debtor-attachments.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-paperclip"></i>
        <p>Attachments</p>
      </a>
    </li>

    <li class="nav-header">SYSTEM</li>

    <li class="nav-item">
      <a href="{{ route('activity-logs.index') }}" class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-history"></i>
        <p>Activity Logs</p>
      </a>
    </li>

    <li class="nav-header">ACCOUNT</li>

    <li class="nav-item">
      <a href="{{ route('userpassword.change') }}" class="nav-link {{ Request::routeIs('userpassword.change') ? 'active' : '' }}">
        <i class="nav-icon fas fa-key"></i>
        <p>Change Password</p>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
         onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    </li>
  </ul>
</nav>
