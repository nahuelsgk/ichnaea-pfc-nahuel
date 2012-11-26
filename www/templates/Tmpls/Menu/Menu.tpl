{call_controller path="Ichnaea/Menu"}
<div id="menu" style="float: left">
<nav>
Welcome <a href="account/settings">{$name}</a> |
{if $is_admin}
<a href="/admin/users">Users</a> |
{/if}
<a href="projects">Matrixs</a> |
<a href="logout">Logout</a></li>
</nav>
</div>
