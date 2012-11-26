{extends file="../Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
  {call_controller path="Ichnaea/Home/Dashboard" function="display_home"}

  <header>
  <h1>Welcome to your Dashboard</h1>
  </header>
  {if $empty}
    There aren't any project.
  {else}
    {* List projects *}
    <table border="1">
    <tr><th>Name</th><th>Status</th></tr>
    {section name=matrix loop=$matrixs}
    <tr>
      <td>{$matrixs[matrix].name}</td>
      <td>{$matrixs[matrix].status}</td>
    <tr>
    {/section}
  {/if}
  <a href='project/new'>Add a new project!</a>
{/block}

