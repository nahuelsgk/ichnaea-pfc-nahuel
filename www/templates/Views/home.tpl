{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
  {init path="Controllers/Ichnaea/Home/Dashboard" function="display_home"}

  <header>
  <h1>Welcome to your Dashboard</h1>
  </header>
  {if $empty}
    There aren't any project.
  {else}
    {* List projects *}
    <table border="1">
    <tr><th>Projects</th><th>Operations</th></tr>
    {section name=p loop=$projects}
    <tr>
      <td>{$projects[p].name}</td>
      <td><a href="/project/edit_new?pid={$projects[p].id}">Edit</a> | <a href="/project/matrixs?pid={$projects[p].id}">View matrixs</a> | <a href="/matrix/edit_new?pid={$projects[p].id}">Create Matrixs</a> |View trainings | Create Trainings | <button class="delete" operation=' { "op": "deleteProject", "params": { "pid" : {$projects[p].id} } } ' onclick='send_event2(this);' >Delete project</button></td>
    </tr>
    {/section}
    </table>
  {/if}
  <a href='project/edit_new'>Add a new project!</a>
</script>

{/block}
{block name='help_text'}
<h4>Welcome to your dashboard!</h4>
<p>
Here you can see your projects currently open.
</p>
{/block}

