{init path="Controllers/Ichnaea/Menu"}
<div id="menu" style="float: left">
<nav>
Welcome <a href="/account/settings">{$username}</a> |
{if $is_admin}
<a href="/admin/users">Users</a> |
{/if}
<a href="/projects/dashboard">Projects</a> |
<a href="/matrixs/my-matrixs">Matrixs</a> |
<a href="/trainings/my-trainings">Trainings</a> | 
<a href="/help/help">Help</a> |
<a href="/logout">Logout</a>
</nav>
</div>
