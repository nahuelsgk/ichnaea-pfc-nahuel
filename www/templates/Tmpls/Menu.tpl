
<ul class="myMenu"> 
  {if $is_admin}<li><a href="/admin/users">Admin Users</a></li>{/if}
  <li><a href="/home">Projects</a>
    <ul>
      <li><a href="/home">My projects</a></li>
    </ul>
  </li> 
  <li><a href="#">Matrixs</a> 
    <ul> 
      <li><a href="/matrix/my-matrixs">My matrixs</a></li> 
    </ul> 
  </li>
  <li><a href="/vars/list_vars">Variables</a>
    <ul><li><a href="/vars/list_vars">My vars</a></li></ul>
  </li>
  <li><a href="/training/my-trainings">My Trainings</a></li> 
  <li><a id="help_trigger"> Help </a></li>
  <li><a href="/logout">Logout</a></li> 
</ul>

<script type="text/javascript"> 
  $(document).ready(function() { 
    $('.myMenu > li').bind('mouseover', openSubMenu); 
    $('.myMenu > li').bind('mouseout', closeSubMenu); 
    function openSubMenu() { 
      $(this).find('ul').css('visibility', 'visible'); 
    }; 
    function closeSubMenu() { 
      $(this).find('ul').css('visibility', 'hidden'); 
    }; 
  }); 
</script>
