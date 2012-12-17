{extends file="Tmpls/ichnaea_root.tpl"}

{block name="page"}
<div class="examples">

    <button name="sample2" class="sample2">Sample 2 (post)</button>
    <button name="zz" class="zz">X</button>
    <script language="javascript" type="text/javascript">
    window.alert("sometext");
    $('.zz').click( function() { window.alert("X");});

    $('.sample2').click( function() {

        $.ajax({
          type: 'POST',
          url: 'http://dev.ichnaea.lsi.upc.edu/response.php?action=sample2',
          data: 'name=Andrew&nickname=Aramis',
          success: function(data){
            $('.results').html(data);
          }
        });

    });

   </script>

    <div class="results">awaiting for response</div>
    <div style="clear:both"></div>
</div>
{/block}
