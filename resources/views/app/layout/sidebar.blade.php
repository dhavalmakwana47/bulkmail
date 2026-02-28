<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class -->
    <li class="nav-item">
      <a href="{{ route('home') }}" class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>
    @if (permissionCheck())
    <li class="nav-item">
      <a href="{{ route('users.index') }}" class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Users</p>
      </a>
    </li>
    @endif
    

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
