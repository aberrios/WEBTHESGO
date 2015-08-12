<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:mod_name} - {lang:restart}</td>
  </tr>
  <tr>
    <td class="leftb">{lang:restart_rly}</td>
  </tr>
  <tr>
    <td class="centerc">
      <form method="post" id="cups_restart" action="{url:cups_restart}">
        <input type="hidden" name="id" value="{cup:id}" />
        <input type="submit" name="submit" value="{lang:confirm}" />
        <input type="submit" name="cancel" value="{lang:cancel}" />
       </form>
    </td>
  </tr>
</table>