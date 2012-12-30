{init path="Controllers/Ichnaea/Menu"}
<div id="menu" style="float: left">
<nav>
Welcome <a href="/account/settings">{$username}</a> |
{if $is_admin}
<a href="/admin/users">Admin Users</a> |
{/if}
<a href="/home">My Projects</a> |
<a href="/matrixs/my-matrixs">My Matrixs</a> |
<a href="/trainings/my-trainings">My Trainings</a> | 
<a href="/help/help">Help</a> |
<a href="/logout">Logout</a>
</nav>
</div>
